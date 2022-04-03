import * as bootstrap from 'bootstrap';
import {OlzApiResponses} from '../../../api/client';
import {olzDefaultFormSubmit, GetDataForRequestFunction, getFormField, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFieldResult, validFormData, invalidFormData} from '../../../components/common/olz_default_form/olz_default_form';
import {loadScript} from '../../../utils/generalUtils';

$(() => {
    $('#reset-password-modal').on('shown.bs.modal', () => {
        $('#reset-password-username-input').trigger('focus');
    });
});

declare const grecaptcha: {
    ready: (onReady: () => void) => void,
    execute: (siteKey: string, config: {action: string}) => Promise<string>,
}|undefined;

const siteKey = '6LetfAodAAAAALyY2vt84FQ-EI5Sj6HkTbGKWR3U';

export function olzResetPasswordModalReset(form: HTMLFormElement): boolean {
    olzResetPasswordModalActuallyReset(form);
    return false;
}

async function olzResetPasswordModalActuallyReset(form: HTMLFormElement): Promise<void> {
    const scriptUrl = `https://www.google.com/recaptcha/api.js?render=${siteKey}`;
    await loadScript(scriptUrl);
    const token = await getRecaptchaToken();

    const getDataForRequestFn: GetDataForRequestFunction<'resetPassword'> = (f) => {
        const fieldResults = {
            usernameOrEmail: getFormField(f, 'username_or_email'),
            recaptchaToken: validFieldResult('', token),
        };
        if (!isFieldResultOrDictThereofValid(fieldResults)) {
            return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
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

function getRecaptchaToken(): Promise<string> {
    return new Promise((resolve, reject) => {
        grecaptcha.ready(() => {
            grecaptcha.execute(siteKey, {action: 'submit'})
                .then(resolve, reject);
        });
    });
}

function handleResponse(response: OlzApiResponses['resetPassword']): string|void {
    if (response.status !== 'OK') {
        throw new Error(`Antwort: ${response.status}`);
    }
    bootstrap.Modal.getOrCreateInstance(
        document.getElementById('reset-password-modal'),
    ).hide();
    return 'E-Mail versendet.';
}
