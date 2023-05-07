import $ from 'jquery';

import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, HandleResponseFunction, getAsserted, getFormField, getRequired, getStringOrNull, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFieldResult, validFormData, invalidFormData} from '../../Common/OlzDefaultForm/OlzDefaultForm';
import {loadRecaptchaToken, loadRecaptcha} from '../../../Utils/recaptchaUtils';

$(() => {
    $('#reset-password-modal').on('shown.bs.modal', () => {
        $('#reset-password-username-input').trigger('focus');
    });
});

export function olzResetPasswordRecaptchaConsent(value: boolean): void {
    if (value) {
        const submitButton = document.getElementById('reset-password-submit-button');
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

export function olzResetPasswordModalReset(form: HTMLFormElement): boolean {
    olzResetPasswordModalActuallyReset(form);
    return false;
}

const handleResponse: HandleResponseFunction<'resetPassword'> = (response) => {
    if (response.status === 'ERROR') {
        throw new Error('E-Mail konnte nicht versendet werden. Bitte später erneut versuchen.');
    } else if (response.status !== 'OK') {
        throw new Error(`Antwort: ${response.status}`);
    }
    window.setTimeout(() => {
        // This removes Google's injected reCaptcha script again
        window.location.href = '/';
    }, 3000);
    return 'E-Mail versendet. Bitte warten...';
};

async function olzResetPasswordModalActuallyReset(form: HTMLFormElement): Promise<void> {
    let token: string|null = null;
    if (getFormField(form, 'recaptcha-consent-given').value === 'yes') {
        token = await loadRecaptchaToken();
    }

    const getDataForRequestFn: GetDataForRequestFunction<'resetPassword'> = (f) => {
        let recaptchaConsentGiven = getFormField(f, 'recaptcha-consent-given');
        recaptchaConsentGiven = getAsserted(
            () => recaptchaConsentGiven.value === 'yes',
            'Bitte akzeptiere den Datenschutzhinweis!',
            recaptchaConsentGiven,
        );
        const fieldResults: OlzRequestFieldResult<'resetPassword'> = {
            usernameOrEmail: getRequired(getStringOrNull(getFormField(f, 'username-or-email'), {trim: true})),
            recaptchaToken: getRequired(validFieldResult('', token)),
        };
        if (!isFieldResultOrDictThereofValid(fieldResults) || !isFieldResultOrDictThereofValid(recaptchaConsentGiven)) {
            return invalidFormData([
                ...getFieldResultOrDictThereofErrors(fieldResults),
                ...getFieldResultOrDictThereofErrors(recaptchaConsentGiven),
            ]);
        }
        return validFormData(getFieldResultOrDictThereofValue(fieldResults));
    };

    olzDefaultFormSubmit(
        'resetPassword',
        getDataForRequestFn,
        form,
        handleResponse,
    );
}
