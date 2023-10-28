import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, HandleResponseFunction, getAsserted, getFormField, getRequired, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFieldResult, validFormData, invalidFormData} from '../../Common/OlzDefaultForm/OlzDefaultForm';
import {loadRecaptchaToken, loadRecaptcha} from '../../../Utils/recaptchaUtils';
import { codeHref } from '../../../Utils/constants';

export function olzVerifyUserEmailRecaptchaConsent(value: boolean): void {
    if (value) {
        const submitButton = document.getElementById('verify-user-email-submit-button');
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

export function olzVerifyUserEmailModalVerify(form: HTMLFormElement): boolean {
    olzVerifyUserEmailModalActuallyVerify(form);
    return false;
}

const handleResponse: HandleResponseFunction<'verifyUserEmail'> = (response) => {
    if (response.status !== 'OK') {
        throw new Error(`Antwort: ${response.status}`);
    }
    window.setTimeout(() => {
        // This removes Google's injected reCaptcha script again
        window.location.href = `${codeHref}profil`;
    }, 1000);
    return 'E-Mail versendet. Bitte warten...';
};

async function olzVerifyUserEmailModalActuallyVerify(form: HTMLFormElement): Promise<void> {
    let token: string|null = null;
    if (getFormField(form, 'recaptcha-consent-given').value === 'yes') {
        token = await loadRecaptchaToken();
    }

    const getDataForRequestFn: GetDataForRequestFunction<'verifyUserEmail'> = (f) => {
        let recaptchaConsentGiven = getFormField(f, 'recaptcha-consent-given');
        recaptchaConsentGiven = getAsserted(
            () => recaptchaConsentGiven.value === 'yes',
            'Bitte akzeptiere den Datenschutzhinweis!',
            recaptchaConsentGiven,
        );
        const fieldResults: OlzRequestFieldResult<'verifyUserEmail'> = {
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
        'verifyUserEmail',
        getDataForRequestFn,
        form,
        handleResponse,
    );
}
