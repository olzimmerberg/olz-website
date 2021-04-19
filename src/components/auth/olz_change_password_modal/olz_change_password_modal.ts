import {OlzApiEndpoint, OlzApiResponses, ValidationError} from '../../../api/client';
import {olzDefaultFormSubmit, GetDataForRequestDict} from '../../common/olz_default_form/olz_default_form';

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
            if (newPassword !== repeatPassword) {
                throw new ValidationError('', {
                    repeat: ['Die Passwort-Wiederholung stimmt nicht mit dem neuen Passwort überein.'],
                });
            }
            return newPassword;
        },
    };

    return olzDefaultFormSubmit(
        OlzApiEndpoint.updatePassword,
        getDataForRequestDict,
        form,
        handleResponse,
    );
}

function handleResponse(response: OlzApiResponses[OlzApiEndpoint.updatePassword]): string|null {
    if (response.status !== 'OK') {
        throw new Error(`Antwort: ${response.status}`);
    }
    $('#change-password-modal').modal('hide');
    return 'Passwort erfolgreich aktualisiert.';
}
