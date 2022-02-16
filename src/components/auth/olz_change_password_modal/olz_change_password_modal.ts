import {OlzApiResponses} from '../../../api/client';
import {olzDefaultFormSubmit, GetDataForRequestFunction, getAsserted, getFormField, getPassword, getRequired, validFieldResult, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData} from '../../../components/common/olz_default_form/olz_default_form';

$(() => {
    $('#change-password-modal').on('shown.bs.modal', () => {
        $('#change-password-old-input').trigger('focus');
    });
});

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
        const fieldResults = {
            id: validFieldResult('', userId),
            oldPassword: getFormField(f, 'old'),
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

function handleResponse(response: OlzApiResponses['updatePassword']): string|void {
    if (response.status !== 'OK') {
        throw new Error(`Antwort: ${response.status}`);
    }
    $('#change-password-modal').modal('hide');
    return 'Passwort erfolgreich aktualisiert.';
}
