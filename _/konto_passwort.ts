import {OlzApiResponses} from '../src/Api/client';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, getAsserted, getCountryCode, getEmail, getFormField, validFieldResult, getGender, getInteger, getIsoDateFromSwissFormat, getPassword, getPhone, getRequired, getStringOrNull, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData} from '../src/Components/Common/OlzDefaultForm/OlzDefaultForm';
import {loadRecaptchaToken, loadRecaptcha} from '../src/Utils/recaptchaUtils';

export function olzSignUpConsent(value: boolean): void {
    if (value) {
        const submitButton = document.getElementById('sign-up-with-password-submit-button');
        submitButton.classList.remove('btn-primary');
        submitButton.classList.add('btn-secondary');
        const originalInnerHtml = submitButton.innerHTML;
        submitButton.innerHTML = 'Bitte warten...';
        loadRecaptcha().then(() => {
            window.setTimeout(() => {
                submitButton.classList.remove('btn-secondary');
                submitButton.classList.add('btn-primary');
                submitButton.innerHTML = originalInnerHtml;
            }, 1100);
        });
    }
}

export function olzKontoSignUpWithPassword(form: HTMLFormElement): boolean {
    olzKontoActuallySignUpWithPassword(form);
    return false;
}

async function olzKontoActuallySignUpWithPassword(form: HTMLFormElement): Promise<void> {
    let token: string|null = null;
    if (getFormField(form, 'consent-given').value === 'yes') {
        token = await loadRecaptchaToken();
    }

    const getDataForRequestFn: GetDataForRequestFunction<'signUpWithPassword'> = (f) => {
        const password = getRequired(getPassword(getFormField(f, 'password')));
        let passwordRepeat = getFormField(f, 'password-repeat');
        const hasValidRepetition = password.value === passwordRepeat.value;
        passwordRepeat = getAsserted(
            () => hasValidRepetition,
            'Das Passwort und die Wiederholung müssen übereinstimmen!',
            passwordRepeat,
        );
        let consentGiven = getFormField(f, 'consent-given');
        consentGiven = getAsserted(
            () => consentGiven.value === 'yes',
            'Bitte akzeptiere den Datenschutzhinweis!',
            consentGiven,
        );
        const fieldResults: OlzRequestFieldResult<'signUpWithPassword'> = {
            firstName: getRequired(getStringOrNull(getFormField(f, 'first-name'))),
            lastName: getRequired(getStringOrNull(getFormField(f, 'last-name'))),
            username: getRequired(getStringOrNull(getFormField(f, 'username'))),
            password: password,
            email: getRequired(getEmail(getFormField(f, 'email'))),
            phone: getPhone(getFormField(f, 'phone')),
            gender: getGender(getFormField(f, 'gender')),
            birthdate: getIsoDateFromSwissFormat(getFormField(f, 'birthdate')),
            street: getFormField(f, 'street'),
            postalCode: getFormField(f, 'postal-code'),
            city: getFormField(f, 'city'),
            region: getFormField(f, 'region'),
            countryCode: getCountryCode(getFormField(f, 'country-code')),
            siCardNumber: getInteger(getFormField(f, 'si-card-number')),
            solvNumber: getFormField(f, 'solv-number'),
            recaptchaToken: validFieldResult('', token),
        };
        if (!isFieldResultOrDictThereofValid(fieldResults) || !isFieldResultOrDictThereofValid(passwordRepeat) || !isFieldResultOrDictThereofValid(consentGiven)) {
            return invalidFormData([
                ...getFieldResultOrDictThereofErrors(fieldResults),
                ...getFieldResultOrDictThereofErrors(passwordRepeat),
                ...getFieldResultOrDictThereofErrors(consentGiven),
            ]);
        }
        return validFormData<'signUpWithPassword'>(getFieldResultOrDictThereofValue(fieldResults));
    };

    olzDefaultFormSubmit(
        'signUpWithPassword',
        getDataForRequestFn,
        form,
        handleResponse,
    );
}

function handleResponse(response: OlzApiResponses['signUpWithPassword']): string|void {
    if (response.status !== 'OK') {
        throw new Error(`Fehler beim Erstellen des Benutzerkontos: ${response.status}`);
    }
    window.setTimeout(() => {
        // This removes Google's injected reCaptcha script again
        window.location.href = 'startseite.php';
    }, 3000);
    return 'Benutzerkonto erfolgreich erstellt. Bitte warten...';
}
