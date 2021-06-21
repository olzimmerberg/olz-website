import {RequestFieldId, OlzApiRequests, OlzApiResponses, OlzApiEndpoint, callOlzApi, OlzApiError, ValidationError, mergeValidationErrors} from '../../../api/client';

export const EMAIL_REGEX = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

export const COUNTRY_CODE_MAP: {[countryName: string]: string} = {
    'switzerland': 'CH',
    'schweiz': 'CH',
};

export type GetDataForRequestDict<T extends OlzApiEndpoint> = {
    [fieldId in keyof OlzApiRequests[T]]: (
        form: HTMLFormElement,
    ) => OlzApiRequests[T][fieldId]
};

export type HandleResponseFunction<T extends OlzApiEndpoint> = (
    response: OlzApiResponses[T],
) => string|void;

export function olzDefaultFormSubmit<T extends OlzApiEndpoint>(
    endpoint: T,
    getDataForRequestDict: GetDataForRequestDict<T>,
    form: HTMLFormElement,
    handleResponse: HandleResponseFunction<T>,
): boolean {
    clearValidationErrors(form);
    let request: OlzApiRequests[T]|undefined;
    try {
        request = getDataForRequest(getDataForRequestDict, form);
    } catch (err) {
        const errorMessage = err.message ? `Fehlerhafte Eingabe: ${err.message}` : 'Fehlerhafte Eingabe.';
        $(form).find('.success-message').text('');
        $(form).find('.error-message').text(errorMessage);
        showValidationErrors<T>(err, form);
        return false;
    }

    callOlzApi(endpoint, request)
        .then((response: OlzApiResponses[T]) => {
            try {
                const customSuccessMessage = handleResponse(response);
                const successMessage = customSuccessMessage || 'Erfolgreich ausgeführt.';
                $(form).find('.success-message').text(successMessage);
                $(form).find('.error-message').text('');
                clearValidationErrors(form);
            } catch (err) {
                $(form).find('.success-message').text('');
                $(form).find('.error-message').text(err.message);
                showValidationErrors<T>(err, form);
            }
        })
        .catch((err: OlzApiError<T>) => {
            const errorMessage = err.message ? `Fehlerhafte Anfrage: ${err.message}` : 'Fehlerhafte Anfrage.';
            $(form).find('.success-message').text('');
            $(form).find('.error-message').text(errorMessage);
            showValidationErrors<T>(err, form);
        });
    return false;
}

export function getDataForRequest<T extends OlzApiEndpoint>(
    getDataForRequestDict: GetDataForRequestDict<T>,
    form: HTMLFormElement,
): OlzApiRequests[T] {
    const fieldIds = Object.keys(getDataForRequestDict) as Array<RequestFieldId<T>>;
    const data: Partial<OlzApiRequests[T]> = {};
    const validationErrors: ValidationError<OlzApiEndpoint.updateUser>[] = [];
    let hasOtherError = false;
    fieldIds.map((fieldId) => {
        try {
            data[fieldId] = getDataForRequestDict[fieldId](form);
        } catch (err: unknown) {
            if (err instanceof ValidationError) {
                validationErrors.push(err);
            } else {
                hasOtherError = true;
            }
        }
    });
    if (hasOtherError) {
        throw new Error('Unexpected Error in getDataForRequest');
    }
    if (validationErrors.length > 0) {
        throw mergeValidationErrors(validationErrors);
    }
    return data as OlzApiRequests[T]; // should now be complete.
}

export function showValidationErrors<T extends OlzApiEndpoint>(
    error: OlzApiError<T>,
    form: HTMLFormElement,
): void {
    const validationErrorDict = error?.validationErrors || {};
    const fieldIds = Object.keys(validationErrorDict) as Array<RequestFieldId<T>>;
    fieldIds.map((fieldId) => {
        const formInput = form[camelCaseToDashCase(fieldId)];
        const errorMessage = error.validationErrors[fieldId].join('\n');
        showErrorOnField(formInput, errorMessage);
    });
}

export function clearValidationErrors(form: HTMLFormElement): void {
    for (let elementInd = 0; elementInd < form.elements.length; elementInd++) {
        const formInput = form.elements[elementInd];
        clearErrorOnField(formInput);
    }
}

export function showErrorOnField(
    formInput: Element,
    errorMessage: string,
): void {
    formInput.classList.add('is-invalid');
    formInput.setAttribute('data-toggle', 'tooltip');
    formInput.setAttribute('title', errorMessage);
    $(formInput).tooltip('show');
}

export function clearErrorOnField(formInput: Element): void {
    formInput.classList.remove('is-invalid');
    formInput.removeAttribute('data-toggle');
    formInput.removeAttribute('title');
    $(formInput).tooltip('dispose');
}

export function camelCaseToDashCase(camelCaseString: string): string {
    return camelCaseString.replace(/([A-Z])/g, '-$1').toLowerCase();
}

export function getEmail(fieldId: string, emailInput: string|undefined): string|null {
    if (!emailInput) {
        return null;
    }
    const trimmedEmailInput = emailInput.trim();
    if (!EMAIL_REGEX.exec(trimmedEmailInput)) {
        throw new ValidationError('', {
            [fieldId]: [`Ungültige E-Mail Adresse "${trimmedEmailInput}".`],
        });
    }
    return trimmedEmailInput;
}

export function getGender(fieldId: string, genderInput: string|undefined): 'M'|'F'|'O'|null {
    switch (genderInput) {
        case 'M': return 'M';
        case 'F': return 'F';
        case 'O': return 'O';
        case '': return null;
        default: throw new ValidationError('', {
            [fieldId]: [`Ungültiges Geschlecht "${genderInput}" ausgewählt.`],
        });
    }
}

export function getIsoDateFromSwissFormat(fieldId: string, date: string|undefined): string|null {
    if (date === undefined || date === '') {
        return null;
    }
    const res = /^\s*([0-9]{1,2})\.\s*([0-9]{1,2})\.\s*([0-9]{4})\s*$/.exec(date);
    if (!res) {
        throw new ValidationError('', {
            [fieldId]: ['Das Datum muss im Format TT.MM.YYYY sein.'],
        });
    }
    const timestamp = Date.parse(`${res[3]}-${res[2]}-${res[1]} 12:00:00`);
    if (!timestamp) {
        throw new ValidationError('', {
            [fieldId]: [`"${date}" ist ein ungültiges Datum.`],
        });
    }
    const isoDate = new Date(timestamp).toISOString().substr(0, 10);
    return `${isoDate} 12:00:00`;
}

export function getPassword(fieldId: string, passwordInput: string|undefined): string|null {
    if (!passwordInput) {
        return null;
    }
    if (passwordInput.length < 8) {
        throw new ValidationError('', {
            [fieldId]: ['Das Passwort muss mindestens 8 Zeichen lang sein.'],
        });
    }
    return passwordInput;
}

export function getPhone(fieldId: string, phoneInput: string|undefined): string|null {
    const phoneInputWithoutSpaces = phoneInput.replace(/\s+/g, '');
    if (!phoneInputWithoutSpaces) {
        return null;
    }
    if (!/^\+[0-9]+$/.exec(phoneInputWithoutSpaces)) {
        throw new ValidationError('', {
            [fieldId]: ['Die Telefonnummer muss mit internationalem Präfix (Schweiz: +41) eingegeben werden.'],
        });
    }
    return phoneInputWithoutSpaces;
}

export function getCountryCode(fieldId: string, countryCode: string|undefined): string {
    if (!countryCode) {
        return '';
    }
    const trimmedCountryCode = countryCode.trim();
    if (trimmedCountryCode.length === 1) {
        throw new ValidationError('', {
            [fieldId]: ['Der Ländercode muss zwei Zeichen lang sein.'],
        });
    } else if (trimmedCountryCode.length === 2) {
        return trimmedCountryCode.toUpperCase();
    }
    const normalizedCountryName = trimmedCountryCode.toLowerCase();
    const countryCodeByName = COUNTRY_CODE_MAP[normalizedCountryName];
    if (countryCodeByName) {
        return countryCodeByName;
    }
    throw new ValidationError('', {
        [fieldId]: ['Der Ländercode muss zwei Zeichen lang sein.'],
    });
}
