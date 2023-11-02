import $ from 'jquery';
import * as bootstrap from 'bootstrap';

import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, HandleResponseFunction, getAsserted, getFormField, getPassword, getRequired, validFieldResult, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData} from '../../Common/OlzDefaultForm/OlzDefaultForm';

$(() => {
    const changePasswordModal = document.getElementById('change-password-modal');
    changePasswordModal?.addEventListener('shown.bs.modal', () => {
        $('#change-password-old-input').trigger('focus');
    });
});

const handleResponse: HandleResponseFunction<'updatePassword'> = (response) => {
    if (response.status !== 'OK') {
        throw new Error(`Antwort: ${response.status}`);
    }
    const modal = document.getElementById('change-password-modal');
    if (modal) {
        bootstrap.Modal.getOrCreateInstance(modal).hide();
    }
    return 'Passwort erfolgreich aktualisiert.';
};

export function olzChangePasswordModalUpdate(userId: number, form: HTMLFormElement): boolean {
    const getDataForRequestFn: GetDataForRequestFunction<'updatePassword'> = (f) => {
        const newPassword = getFormField(f, 'new');
        let repeatPassword = getFormField(f, 'repeat');
        const hasValidRepetition = newPassword.value === repeatPassword.value;
        repeatPassword = getAsserted(
            () => hasValidRepetition,
            'Das Passwort und die Wiederholung müssen übereinstimmen!',
            repeatPassword,
        );
        const fieldResults: OlzRequestFieldResult<'updatePassword'> = {
            id: validFieldResult('', userId),
            oldPassword: getRequired(getFormField(f, 'old')),
            newPassword: getRequired(getPassword(newPassword)),
        };
        if (
            !isFieldResultOrDictThereofValid(fieldResults)
            || !isFieldResultOrDictThereofValid(repeatPassword)
        ) {
            return invalidFormData([
                ...getFieldResultOrDictThereofErrors(fieldResults),
                ...getFieldResultOrDictThereofErrors(repeatPassword),
            ]);
        }
        return validFormData(getFieldResultOrDictThereofValue(fieldResults));
    };

    olzDefaultFormSubmit(
        'updatePassword',
        getDataForRequestFn,
        form,
        handleResponse,
    );
    return false;
}
