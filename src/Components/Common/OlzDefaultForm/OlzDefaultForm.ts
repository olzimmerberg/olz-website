import * as bootstrap from 'bootstrap';
import $ from 'jquery';

import {OlzApiRequests, OlzApiResponses, OlzApiEndpoint, callOlzApi, ValidationError, OlzApi} from '../../../../src/Api/client';
import {getErrorOrThrow} from '../../../../src/Utils/generalUtils';

export const EMAIL_REGEX = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

export const SWISS_DATETIME_REGEX = /^\s*([0-9]{1,2})\.\s*([0-9]{1,2})\.\s*([0-9]{4})\W+([0-9]{2}):([0-9]{2})(:([0-9]{2}))?\s*$/;
export const SWISS_DATE_REGEX = /^\s*([0-9]{1,2})\.\s*([0-9]{1,2})\.\s*([0-9]{4})\s*$/;

export const COUNTRY_CODE_MAP: {[countryName: string]: string} = {
    'switzerland': 'CH',
    'schweiz': 'CH',
};

// Reusable: Field result

export interface ValidFieldResult<T> {
    isValid: true;
    fieldId: string;
    value: T;
    errors: ValidationError[];
}

export interface InvalidFieldResult {
    isValid: false;
    fieldId: string;
    value: unknown;
    errors: ValidationError[];
}

export type FieldResult<T> = ValidFieldResult<T>|InvalidFieldResult;

function isAny(thing: unknown): thing is any {
    return true;
}

export function isValidFieldResult<T>(
    thing: unknown,
    isT: (val: unknown) => val is T = isAny,
): thing is ValidFieldResult<T> {
    if (!isFieldResult(thing, isT)) {
        return false;
    }
    return thing.isValid;
}

export function isInvalidFieldResult<T>(
    thing: unknown,
    isT: (val: unknown) => val is T = isAny,
): thing is InvalidFieldResult {
    if (!isFieldResult(thing, isT)) {
        return false;
    }
    return !thing.isValid;
}

export function isFieldResult<T>(
    thing: unknown,
    isT: (val: unknown) => val is T = isAny,
): thing is FieldResult<T> {
    const fieldResult = (thing as FieldResult<T>);
    return (
        typeof fieldResult?.isValid === 'boolean'
        && typeof fieldResult?.fieldId === 'string'
        && isT(fieldResult?.value)
        && fieldResult?.errors instanceof Array
    );
}

export function validFieldResult<T>(
    fieldId: string,
    value: T,
): FieldResult<T> {
    return {
        fieldId,
        isValid: true,
        value,
        errors: [],
    };
}

export function invalidFieldResult<T>(
    fieldId: string,
    errors: ValidationError[],
    value: T,
): FieldResult<T> {
    return {
        fieldId,
        isValid: false,
        value,
        errors,
    };
}

function recordErrorMessages<T>(
    input: FieldResult<T>,
    errorMessages: string[],
): InvalidFieldResult {
    const errors = errorMessages.map((errorMessage) => new ValidationError('', {
        [input.fieldId]: [errorMessage],
    }));
    return recordErrors(input, errors);
}

function recordErrors<T>(
    input: FieldResult<T>,
    errors: ValidationError[],
): InvalidFieldResult {
    return {
        fieldId: input.fieldId,
        isValid: false,
        value: input.value,
        errors: [
            ...input.errors,
            ...errors,
        ],
    };
}

export type FieldResultOrDictThereof<T> = T extends Record<string, unknown> ? DictOfFieldResults<T> : FieldResult<T>;

export type DictOfFieldResults<T> = {
    [key in keyof T]: FieldResultOrDictThereof<T[key]>
};

function keys<T>(object: {[key in keyof T]: unknown}): Array<keyof T> {
    return Object.keys(object)as unknown as Array<keyof T>;
}

export function isFieldResultOrDictThereofValid<T>(
    fieldResultOrDictThereof: FieldResultOrDictThereof<T>,
): boolean {
    if (isFieldResult(fieldResultOrDictThereof)) {
        return fieldResultOrDictThereof.isValid;
    }
    const dictOfFieldResult = fieldResultOrDictThereof as DictOfFieldResults<T>;
    return keys(dictOfFieldResult).every((key) => {
        const field = dictOfFieldResult[key] as FieldResultOrDictThereof<unknown>;
        return isFieldResultOrDictThereofValid(field);
    });
}

export function getFieldResultOrDictThereofErrors<T>(
    fieldResultOrDictThereof: FieldResultOrDictThereof<T>,
): ValidationError[] {
    if (isFieldResult(fieldResultOrDictThereof)) {
        return fieldResultOrDictThereof.errors;
    }
    const dictOfFieldResult = fieldResultOrDictThereof as DictOfFieldResults<T>;
    const errors: ValidationError[] = [];
    return errors.concat(...keys(dictOfFieldResult).map((key) => {
        const field = dictOfFieldResult[key] as FieldResultOrDictThereof<unknown>;
        return getFieldResultOrDictThereofErrors(field);
    }));
}

export function getFieldResultOrDictThereofValue<T>(
    fieldResultOrDictThereof: FieldResultOrDictThereof<T>,
): T {
    if (isValidFieldResult(fieldResultOrDictThereof)) {
        return fieldResultOrDictThereof.value;
    }
    if (isInvalidFieldResult(fieldResultOrDictThereof)) {
        throw new Error('getFieldResultOrDictThereofValue can only be called on valid.');
    }
    const dictOfFieldResult = fieldResultOrDictThereof as DictOfFieldResults<T>;
    const value: {[key in keyof T]?: unknown} = {};
    for (const key of keys(dictOfFieldResult)) {
        const field = dictOfFieldResult[key] as FieldResultOrDictThereof<unknown>;
        value[key] = getFieldResultOrDictThereofValue(field);
    }
    return value as unknown as T;
}

// ---

export type OlzRequestFieldResult<T extends OlzApiEndpoint> = FieldResultOrDictThereof<OlzApiRequests[T]>;

// Form data for request

type ValidFormDataForRequest<T extends OlzApiEndpoint> = {
    isValid: true,
    data: OlzApiRequests[T],
};

type InvalidFormDataForRequest = {
    isValid: false,
    errors: ValidationError[],
};

export function validFormData<T extends OlzApiEndpoint>(data: OlzApiRequests[T]): ValidFormDataForRequest<T> {
    return {
        isValid: true,
        data,
    };
}

export function invalidFormData(errors: ValidationError[]): InvalidFormDataForRequest {
    return {
        isValid: false,
        errors,
    };
}

// ---

export type GetDataForRequestFunction<T extends OlzApiEndpoint> = (
    form: HTMLFormElement,
)=> ValidFormDataForRequest<T>|InvalidFormDataForRequest;

export type HandleResponseFunction<T extends OlzApiEndpoint> = (
    response: OlzApiResponses[T],
) => string|void;

export function olzDefaultFormSubmit<T extends OlzApiEndpoint>(
    endpoint: T,
    getDataForRequestFn: GetDataForRequestFunction<T>,
    form: HTMLFormElement,
    handleResponse: HandleResponseFunction<T>,
): Promise<OlzApiResponses[T]> {
    clearValidationErrors(form);
    let request: OlzApiRequests[T]|undefined;
    try {
        request = getDataForRequest(getDataForRequestFn, form);
    } catch (unk: unknown) {
        const err = getErrorOrThrow(unk);
        const errorMessage = err.message ? `Fehlerhafte Eingabe: ${err.message}` : 'Fehlerhafte Eingabe';
        $(form).find('.success-message').text('');
        $(form).find('.error-message').text(errorMessage);
        if (err instanceof ValidationError) {
            showValidationErrors<T>(err, form);
        }
        return Promise.reject(new Error('Die Anfrage konnte nicht gesendet werden.'));
    }

    return callOlzApi(endpoint, request)
        .then((response: OlzApiResponses[T]) => {
            try {
                const customSuccessMessage = handleResponse(response);
                const successMessage = customSuccessMessage || 'Erfolgreich ausgeführt.';
                $(form).find('.success-message').text(successMessage);
                $(form).find('.error-message').text('');
                clearValidationErrors(form);
            } catch (unk: unknown) {
                const err = getErrorOrThrow(unk);
                $(form).find('.success-message').text('');
                $(form).find('.error-message').text(err.message);
                if (err instanceof ValidationError) {
                    showValidationErrors<T>(err, form);
                }
            }
            return response;
        })
        .catch((err: unknown) => {
            const errorMessage = err instanceof Error ? `Fehlerhafte Anfrage: ${err.message}` : 'Fehlerhafte Anfrage.';
            $(form).find('.success-message').text('');
            $(form).find('.error-message').text(errorMessage);
            if (err instanceof ValidationError) {
                showValidationErrors<T>(err, form);
            }
            return Promise.reject(err);
        });
}

export function getDataForRequest<T extends OlzApiEndpoint>(
    getDataForRequestFn: GetDataForRequestFunction<T>,
    form: HTMLFormElement,
): OlzApiRequests[T] {
    let data: Partial<OlzApiRequests[T]> = {};
    const validationErrors: ValidationError[] = [];
    let hasOtherError = false;
    const result = getDataForRequestFn(form);
    if (result.isValid === true) {
        data = result.data;
    }
    if (result.isValid === false) {
        for (const err of result.errors) {
            if (err instanceof ValidationError) {
                validationErrors.push(err);
            } else {
                hasOtherError = true;
            }
        }
    }
    if (hasOtherError) {
        throw new Error('Unexpected Error in getDataForRequest');
    }
    if (validationErrors.length > 0) {
        const olzApi = new OlzApi();
        throw olzApi.mergeValidationErrors(validationErrors);
    }
    return data as OlzApiRequests[T]; // should now be complete.
}

export function showValidationErrors<T extends OlzApiEndpoint>(
    error: ValidationError,
    form: HTMLFormElement,
): void {
    const validationErrorDict = error?.getErrorsByField() || {};
    const fieldIds = Object.keys(validationErrorDict) as Array<keyof OlzApiRequests[T]>;
    fieldIds.map((fieldId) => {
        const formInput = form[camelCaseToDashCase(String(fieldId))];
        const errorMessage = error?.getErrorsForField(String(fieldId)).join('\n');
        if (errorMessage) {
            showErrorOnField(formInput, errorMessage);
        }
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
    formInput.setAttribute('title', errorMessage);
    formInput.setAttribute('data-bs-toggle', 'tooltip');
    if (formInput.parentElement) {
        new bootstrap.Tooltip(formInput, {container: formInput.parentElement}).show();
    }
}

export function clearErrorOnField(formInput: Element): void {
    formInput.classList.remove('is-invalid');
    formInput.removeAttribute('data-bs-toggle');
    formInput.removeAttribute('title');
    new bootstrap.Tooltip(formInput).dispose();
}

export function camelCaseToDashCase(camelCaseString: string): string {
    return camelCaseString.replace(/([A-Z])/g, '-$1').toLowerCase();
}

export function getAsserted<T>(
    assertFn: () => boolean,
    errorMessage: string,
    input: FieldResult<T>,
): FieldResult<T> {
    if (!assertFn()) {
        return recordErrorMessages(input, [errorMessage]);
    }
    return input;
}

export function getCountryCode(
    input: FieldResult<string|null|undefined>,
): FieldResult<string|null> {
    if (!isValidFieldResult(input)) {
        return input;
    }
    if (!input.value) {
        return {...input, value: null};
    }
    const trimmedValue = input.value.trim();
    if (trimmedValue.length === 1) {
        return recordErrorMessages(input, ['Der Ländercode muss zwei Zeichen lang sein.']);
    } else if (trimmedValue.length === 2) {
        return {...input, value: trimmedValue.toUpperCase()};
    }
    const normalizedCountryName = trimmedValue.toLowerCase();
    const countryCodeByName = COUNTRY_CODE_MAP[normalizedCountryName];
    if (countryCodeByName) {
        return {...input, value: countryCodeByName};
    }
    return recordErrorMessages(input, ['Der Ländercode muss zwei Zeichen lang sein.']);
}

export function getEmail(
    input: FieldResult<string|null|undefined>,
): FieldResult<string|null> {
    if (!isValidFieldResult(input)) {
        return input;
    }
    if (!input.value) {
        return {...input, value: null};
    }
    const trimmedValue = input.value.trim();
    if (trimmedValue === '') {
        return {...input, value: null};
    }
    if (!EMAIL_REGEX.exec(trimmedValue)) {
        return recordErrorMessages(input, [`Ungültige E-Mail Adresse "${trimmedValue}".`]);
    }
    return {...input, value: trimmedValue};
}

export function getGender(
    input: FieldResult<string|undefined|null>,
): FieldResult<'M'|'F'|'O'|null> {
    switch (input.value) {
        case 'M': return {...input, value: input.value};
        case 'F': return {...input, value: input.value};
        case 'O': return {...input, value: input.value};
        case '': return {...input, value: null};
        case null: return {...input, value: null};
        case undefined: return {...input, value: null};
        default:
            return recordErrorMessages(
                {...input, value: null},
                [`Ungültiges Geschlecht "${input}" ausgewählt.`],
            );
    }
}

export function getIsoDateFromSwissFormat(
    input: FieldResult<string|null|undefined>,
): FieldResult<string|null> {
    if (!isValidFieldResult(input)) {
        return input;
    }
    if (!input.value || input.value === '') {
        return {...input, value: null};
    }
    const res = SWISS_DATE_REGEX.exec(input.value);
    if (!res) {
        return recordErrorMessages(input, ['Das Datum muss im Format TT.MM.YYYY sein.']);
    }
    const timestamp = Date.parse(`${res[3]}-${res[2]}-${res[1]} 12:00:00`);
    if (!timestamp) {
        return recordErrorMessages(input, [`"${input.value}" ist ein ungültiges Datum.`]);
    }
    const dateObject = new Date(timestamp);
    const year = String(dateObject.getFullYear()).padStart(4, '0');
    const month = String(dateObject.getMonth() + 1).padStart(2, '0');
    const day = String(dateObject.getDate()).padStart(2, '0');
    const newValue = `${year}-${month}-${day} 12:00:00`;
    return {...input, value: newValue};
}

export function getInteger(
    input: FieldResult<string|null|undefined>,
): FieldResult<number|null> {
    if (!isValidFieldResult(input)) {
        return input;
    }
    if (!input.value) {
        return {...input, value: null};
    }
    const trimmed = input.value.trim();
    if (!/^[0-9]+$/.test(trimmed)) {
        return recordErrorMessages(
            {...input, value: null},
            ['Wert muss eine Ganzzahl sein.'],
        );
    }
    const number = Number(trimmed);
    return {...input, value: number};
}

export function getIsoDateTimeFromSwissFormat(
    input: FieldResult<string|null|undefined>,
): FieldResult<string|null> {
    if (!isValidFieldResult(input)) {
        return input;
    }
    if (!input.value || input.value === '') {
        return {...input, value: null};
    }
    const res = SWISS_DATETIME_REGEX.exec(input.value);
    if (!res) {
        return recordErrorMessages(input, ['Das Datum muss im Format TT.MM.YYYY SS:MM[:SS] sein.']);
    }
    const parsedSeconds = res[6] ?? '';
    const timestamp = Date.parse(
        `${res[3]}-${res[2]}-${res[1]} ${res[4]}:${res[5]}${parsedSeconds}`,
    );
    if (!timestamp) {
        return recordErrorMessages(input, [`"${input.value}" ist ein ungültiges Datum.`]);
    }
    const dateObject = new Date(timestamp);
    const year = String(dateObject.getFullYear()).padStart(4, '0');
    const month = String(dateObject.getMonth() + 1).padStart(2, '0');
    const day = String(dateObject.getDate()).padStart(2, '0');
    const hours = String(dateObject.getHours()).padStart(2, '0');
    const minutes = String(dateObject.getMinutes()).padStart(2, '0');
    const seconds = String(dateObject.getSeconds()).padStart(2, '0');
    const newValue = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    return {...input, value: newValue};
}

export function getPassword(
    input: FieldResult<string|null|undefined>,
): FieldResult<string|null> {
    if (!isValidFieldResult(input)) {
        return input;
    }
    const value = input.value;
    if (!value) {
        return {...input, value: null};
    }
    if (value.length < 8) {
        return recordErrorMessages(input, ['Das Passwort muss mindestens 8 Zeichen lang sein.']);
    }
    return {...input, value};
}

export function getPhone(
    input: FieldResult<string|null|undefined>,
): FieldResult<string|null> {
    if (!isValidFieldResult(input)) {
        return input;
    }
    if (!input.value) {
        return {...input, value: null};
    }
    const valueWithoutSpaces = input.value.replace(/\s+/g, '');
    if (!valueWithoutSpaces) {
        return {...input, value: null};
    }
    if (!/^\+[0-9]+$/.exec(valueWithoutSpaces)) {
        return recordErrorMessages(input, ['Die Telefonnummer muss mit internationalem Präfix (Schweiz: +41) eingegeben werden.']);
    }
    return {...input, value: valueWithoutSpaces};
}

export function getRequired<T>(
    input: FieldResult<T|null|undefined>,
): FieldResult<T> {
    if (!isValidFieldResult(input)) {
        return input;
    }
    const value = input.value;
    if (value === null || value === undefined) {
        return recordErrorMessages(input, ['Feld darf nicht leer sein.']);
    }
    return {...input, value};
}

export function getStringOrEmpty<T>(
    input: FieldResult<T|null|undefined>,
): FieldResult<string> {
    if (!input.value) {
        return {...input, value: ''};
    }
    const newValue = `${input.value}`;
    return {...input, value: newValue};
}

export function getStringOrNull<T>(
    input: FieldResult<T|null|undefined>,
): FieldResult<string|null> {
    if (!input.value) {
        return {...input, value: null};
    }
    const newValue = `${input.value}`;
    return {...input, value: newValue};
}

export function getFormField(
    form: HTMLFormElement,
    fieldId: string,
): FieldResult<string|null> {
    const field = form.elements.namedItem(fieldId) as HTMLInputElement;
    if (field) {
        if (field.type === 'checkbox' && 'checked' in field) {
            return {
                fieldId,
                isValid: true,
                value: field.checked ? 'yes' : 'no',
                errors: [],
            };
        }
        if ('value' in field) {
            return {
                fieldId,
                isValid: true,
                value: field.value,
                errors: [],
            };
        }
    }
    return {
        fieldId,
        isValid: false,
        value: null,
        errors: [
            new ValidationError('', {
                [fieldId]: [
                    `Error retrieving form field value for: ${fieldId}`,
                ],
            }),
        ],
    };
}
