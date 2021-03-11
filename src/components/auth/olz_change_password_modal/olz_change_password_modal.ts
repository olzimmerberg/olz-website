import {OlzApiEndpoint, callOlzApi} from '../../../api/client';

$(() => {
    $('#change-password-modal').on('shown.bs.modal', () => {
        $('#change-password-old-input').trigger('focus');
    });
});

export function olzChangePasswordModalUpdate(userId: number, form: Record<string, {value?: string}>): void {
    const oldPassword = form.old.value;
    const newPassword = form.new.value;
    const repeatPassword = form.repeat.value;

    if (newPassword !== repeatPassword) {
        $('#change-password-message').text('Die Passwort-Wiederholung stimmt nicht mit dem neuen Passwort überein.');
        return;
    }

    callOlzApi(
        OlzApiEndpoint.updatePassword,
        {oldPassword, newPassword, id: userId},
    )
        .then((response) => {
            if (response.status === 'OK') {
                $('#change-password-modal').modal('hide');
            } else {
                $('#change-password-message').text(response.status);
            }
        })
        .catch((err) => {
            $('#change-password-message').text(err.message);
        });
}
