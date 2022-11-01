import $ from 'jquery';

import {OlzApiResponses} from '../../../Api/client';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, getAsserted, getFormField, getRequired, getStringOrNull, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFieldResult, validFormData, invalidFormData} from '../../Common/OlzDefaultForm/OlzDefaultForm';
import {loadRecaptchaToken, loadRecaptcha} from '../../../Utils/recaptchaUtils';

$(() => {
    $('#reset-password-modal').on('shown.bs.modal', () => {
        $('#reset-password-username-input').trigger('focus');
    });
});

export function olzResetPasswordConsent(value: boolean): void {
    if (value) {
        loadRecaptcha();
    }
}

export function olzResetPasswordModalReset(form: HTMLFormElement): boolean {
    olzResetPasswordModalActuallyReset(form);
    return false;
}

async function olzResetPasswordModalActuallyReset(form: HTMLFormElement): Promise<void> {
    let token: string|null = null;
    if (getFormField(form, 'consent-given').value === 'yes') {
        token = await loadRecaptchaToken();
    }

    const getDataForRequestFn: GetDataForRequestFunction<'resetPassword'> = (f) => {
        let consentGiven = getFormField(f, 'consent-given');
        consentGiven = getAsserted(
            () => consentGiven.value === 'yes',
            'Bitte akzeptiere den Datenschutzhinweis!',
            consentGiven,
        );
        const fieldResults: OlzRequestFieldResult<'resetPassword'> = {
            usernameOrEmail: getRequired(getStringOrNull(getFormField(f, 'username-or-email'))),
            recaptchaToken: validFieldResult('', token),
        };
        if (!isFieldResultOrDictThereofValid(fieldResults) || !isFieldResultOrDictThereofValid(consentGiven)) {
            return invalidFormData([
                ...getFieldResultOrDictThereofErrors(fieldResults),
                ...getFieldResultOrDictThereofErrors(consentGiven),
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

function handleResponse(response: OlzApiResponses['resetPassword']): string|void {
    if (response.status !== 'OK') {
        throw new Error(`Antwort: ${response.status}`);
    }
    window.setTimeout(() => {
        // This removes Google's injected reCaptcha script again
        window.location.href = 'startseite.php';
    }, 3000);
    return 'E-Mail versendet. Bitte warten...';
}
