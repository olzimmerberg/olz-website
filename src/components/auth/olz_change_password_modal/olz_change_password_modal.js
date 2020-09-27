
$(() => {
    $('#change-password-modal').on('shown.bs.modal', () => {
        $('#change-password-old-input').trigger('focus');
    });
});

export function olzChangePasswordModalUpdate(userId, form) {
    const oldPassword = form['old'].value;
    const newPassword = form['new'].value;
    const repeatPassword = form['repeat'].value;

    if (newPassword !== repeatPassword) {
        $('#change-password-message').text('Die Passwort-Wiederholung stimmt nicht mit dem neuen Passwort überein.');
        return;
    }

    $.post('/_/api/index.php/updatePassword', JSON.stringify({oldPassword, newPassword, id: userId}))
        .done(data => {
            const response = JSON.parse(data);
            if (response.status === 'OK') {
                $('#change-password-modal').modal('hide');
            } else {
                $('#change-password-message').text(response.status);
            }
        })
        .fail(data => {
            const response = JSON.parse(data.responseText);
            $('#change-password-message').text(response);
        });
}
