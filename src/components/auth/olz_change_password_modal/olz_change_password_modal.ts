import {OlzApiEndpoint, OlzApiResponses, ValidationError} from '../../../api/client';
import {olzDefaultFormSubmit, GetDataForRequestDict, getPassword, getRequired, showErrorOnField, clearErrorOnField} from '../../../components/common/olz_default_form/olz_default_form';

$(() => {
    $('#change-password-modal').on('shown.bs.modal', () => {
        $('#change-password-old-input').trigger('focus');
    });
});

export function olzChangePasswordModalUpdate(userId: number, form: HTMLFormElement): boolean {
    const getDataForRequestDict: GetDataForRequestDict<OlzApiEndpoint.updatePassword> = {
        id: () => userId,
        oldPassword: (f) => f.old.value,
        newPassword: (f) => {
            const newPassword = f.new.value;
            const repeatPassword = f.repeat.value;
            const hasInvalidRepetition = newPassword !== repeatPassword;
            if (hasInvalidRepetition) {
                showErrorOnField(form.repeat, 'Das Passwort und die Wiederholung müssen übereinstimmen!');
            } else {
                clearErrorOnField(form.repeat);
            }
            const result = getRequired('new', getPassword('new', newPassword));
            if (hasInvalidRepetition) {
                throw new ValidationError('', {});
            }
            return result;
        },
    };

    return olzDefaultFormSubmit(
        OlzApiEndpoint.updatePassword,
        getDataForRequestDict,
        form,
        handleResponse,
    );
}

function handleResponse(response: OlzApiResponses[OlzApiEndpoint.updatePassword]): string|void {
    if (response.status !== 'OK') {
        throw new Error(`Antwort: ${response.status}`);
    }
    $('#change-password-modal').modal('hide');
    return 'Passwort erfolgreich aktualisiert.';
}
