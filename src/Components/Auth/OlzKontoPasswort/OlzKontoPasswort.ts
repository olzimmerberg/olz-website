import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, HandleResponseFunction, getAsserted, getCountryCode, getEmail, getFormField, validFieldResult, getGender, getInteger, getIsoDate, getPassword, getPhone, getRequired, getStringOrNull, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData} from '../../../Components/Common/OlzDefaultForm/OlzDefaultForm';
import { codeHref } from '../../../Utils/constants';
import {loadRecaptchaToken, loadRecaptcha} from '../../../Utils/recaptchaUtils';

export function olzSignUpRecaptchaConsent(value: boolean): void {
    if (value) {
        const submitButton = document.getElementById('sign-up-with-password-submit-button');
        if (!submitButton) {
            throw new Error('Submit button must exist');
        }
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

const handleResponse: HandleResponseFunction<'signUpWithPassword'> = (response) => {
    if (response.status !== 'OK' && response.status !== 'OK_NO_EMAIL_VERIFICATION') {
        throw new Error(`Fehler beim Erstellen des Benutzerkontos: ${response.status}`);
    }
    window.setTimeout(() => {
        // This removes Google's injected reCaptcha script again
        window.location.href = `${codeHref}profil`;
    }, 1000);
    if (response.status === 'OK_NO_EMAIL_VERIFICATION') {
        return 'Benutzerkonto erfolgreich erstellt. E-Mail bitte manuell bestätigen! Bitte warten...';
    }
    return 'Benutzerkonto erfolgreich erstellt. Bitte warten...';
};

async function olzKontoActuallySignUpWithPassword(form: HTMLFormElement): Promise<void> {
    let token: string|null = null;
    if (getFormField(form, 'recaptcha-consent-given').value === 'yes') {
        token = await loadRecaptchaToken();
    }

    const getDataForRequestFn: GetDataForRequestFunction<'signUpWithPassword'> = (f) => {
        const password = getPassword(getFormField(f, 'password'));
        let passwordRepeat = getFormField(f, 'password-repeat');
        const hasValidRepetition = password.value === passwordRepeat.value;
        if (password.value) {
            passwordRepeat = getAsserted(
                () => hasValidRepetition,
                'Das Passwort und die Wiederholung müssen übereinstimmen!',
                passwordRepeat,
            );
        }
        let recaptchaConsentGiven = getFormField(f, 'recaptcha-consent-given');
        recaptchaConsentGiven = getAsserted(
            () => recaptchaConsentGiven.value === 'yes',
            'Bitte akzeptiere die Nutzung von Google reCaptcha!',
            recaptchaConsentGiven,
        );
        let cookieConsentGiven = getFormField(f, 'cookie-consent-given');
        cookieConsentGiven = getAsserted(
            () => cookieConsentGiven.value === 'yes',
            'Bitte akzeptiere den Datenschutzhinweis!',
            cookieConsentGiven,
        );
        const fieldResults: OlzRequestFieldResult<'signUpWithPassword'> = {
            firstName: getRequired(getStringOrNull(getFormField(f, 'first-name'), {trim: true})),
            lastName: getRequired(getStringOrNull(getFormField(f, 'last-name'), {trim: true})),
            username: getRequired(getStringOrNull(getFormField(f, 'username'), {trim: true})),
            password: password,
            email: getEmail(getFormField(f, 'email')),
            phone: getPhone(getFormField(f, 'phone')),
            gender: getGender(getFormField(f, 'gender')),
            birthdate: getIsoDate(getFormField(f, 'birthdate')),
            street: getFormField(f, 'street'),
            postalCode: getFormField(f, 'postal-code'),
            city: getFormField(f, 'city'),
            region: getFormField(f, 'region'),
            countryCode: getCountryCode(getFormField(f, 'country-code')),
            siCardNumber: getInteger(getFormField(f, 'si-card-number')),
            solvNumber: getFormField(f, 'solv-number'),
            recaptchaToken: getRequired(validFieldResult('recaptcha-consent-given', token)),
        };
        if (!isFieldResultOrDictThereofValid(fieldResults) || !isFieldResultOrDictThereofValid(passwordRepeat) || !isFieldResultOrDictThereofValid(recaptchaConsentGiven) || !isFieldResultOrDictThereofValid(cookieConsentGiven)) {
            return invalidFormData([
                ...getFieldResultOrDictThereofErrors(fieldResults),
                ...getFieldResultOrDictThereofErrors(passwordRepeat),
                ...getFieldResultOrDictThereofErrors(recaptchaConsentGiven),
                ...getFieldResultOrDictThereofErrors(cookieConsentGiven),
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
