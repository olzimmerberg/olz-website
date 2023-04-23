import {olzApi} from '../src/Api/client';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, HandleResponseFunction, getAsserted, getCountryCode, getEmail, getFormField, getGender, getInteger, getIsoDateFromSwissFormat, getPhone, getRequired, getStringOrNull, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFieldResult, validFormData, invalidFormData, FieldResult} from '../src/Components/Common/OlzDefaultForm/OlzDefaultForm';
import {olzConfirm} from '../src/Components/Common/OlzConfirmationDialog/OlzConfirmationDialog';
import {loadRecaptchaToken, loadRecaptcha} from '../src/Utils/recaptchaUtils';

export function olzProfileDeleteUser(userId: number): boolean {
    olzConfirm(
        'OLZ-Konto wirklich unwiderruflich löschen?',
        {confirmButtonStyle: 'btn-danger', confirmLabel: 'Löschen'},
    ).then(() => {
        olzApi.call('deleteUser', {id: userId}).then(() => {
            window.location.href = '/';
        });
    });
    return false;
}

export function olzProfileInit(): void {
    const existingEmailInput = document.getElementById('profile-existing-email-input') as
        HTMLInputElement|undefined;
    const emailInput = document.getElementById('profile-email-input') as
        HTMLInputElement|undefined;
    if (emailInput && existingEmailInput) {
        emailInput.addEventListener('keyup', () => {
            const elem = document.getElementById('recaptcha-consent-container');
            if (!elem) {
                return;
            }
            const isEmailChanged = emailInput.value !== existingEmailInput.value;
            elem.style.display = isEmailChanged ? 'block' : 'none';
        });
    }
}

export function olzProfileRecaptchaConsent(value: boolean): void {
    if (value) {
        const submitButton = document.getElementById('update-user-submit-button');
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

export function olzProfileUpdateUser(userId: number, form: HTMLFormElement): boolean {
    olzProfileActuallyUpdateUser(userId, form);
    return false;
}

const handleResponse: HandleResponseFunction<'updateUser'> = (response) => {
    if (response.status !== 'OK') {
        throw new Error(`Antwort: ${response.status}`);
    }
    return 'Benutzerdaten erfolgreich aktualisiert.';
};

export async function olzProfileActuallyUpdateUser(userId: number, form: HTMLFormElement): Promise<boolean> {
    const existingEmail = getFormField(form, 'existing-email');
    const newEmail = getFormField(form, 'email');
    let token: string|null = null;
    if (newEmail.value !== existingEmail.value) {
        if (getFormField(form, 'recaptcha-consent-given').value === 'yes') {
            token = await loadRecaptchaToken();
        }
    }

    const getDataForRequestFunction: GetDataForRequestFunction<'updateUser'> = (f) => {
        let recaptchaConsentGiven: FieldResult<string|null> =
            validFieldResult('recaptcha-consent-given', null);
        if (newEmail.value !== existingEmail.value) {
            recaptchaConsentGiven = getFormField(f, 'recaptcha-consent-given');
            recaptchaConsentGiven = getAsserted(
                () => recaptchaConsentGiven.value === 'yes',
                'Bitte akzeptiere die Nutzung von Google reCaptcha!',
                recaptchaConsentGiven,
            );
        }
        const fieldResults: OlzRequestFieldResult<'updateUser'> = {
            id: validFieldResult('', userId),
            firstName: getRequired(getStringOrNull(getFormField(f, 'first-name'))),
            lastName: getRequired(getStringOrNull(getFormField(f, 'last-name'))),
            username: getRequired(getStringOrNull(getFormField(f, 'username'))),
            phone: getPhone(getFormField(f, 'phone')),
            email: getRequired(getEmail(getFormField(f, 'email'))),
            gender: getGender(getFormField(f, 'gender')),
            birthdate: getIsoDateFromSwissFormat(getFormField(f, 'birthdate')),
            street: getFormField(f, 'street'),
            postalCode: getFormField(f, 'postal-code'),
            city: getFormField(f, 'city'),
            region: getFormField(f, 'region'),
            countryCode: getCountryCode(getFormField(f, 'country-code')),
            siCardNumber: getInteger(getFormField(f, 'si-card-number')),
            solvNumber: getFormField(f, 'solv-number'),
            avatarId: getStringOrNull(getFormField(f, 'avatar-id')),
            recaptchaToken: validFieldResult('recaptcha-consent-given', token),
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
        'updateUser',
        getDataForRequestFunction,
        form,
        handleResponse,
    );
    return false;
}
