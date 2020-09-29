
export function olzProfileUpdateUser(userId, form) {
    const firstName = form['first-name'].value; 
    const lastName = form['last-name'].value;
    const username = form['username'].value;
    const email = form['email'].value;
    $.ajax({
        type: 'PATCH',
        url: `/_/api/index.php/updateUser`, 
        data: JSON.stringify({firstName, lastName, username, email, id: userId}),
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
    })
        .done(response => {
            if (response.status === 'OK') {
                $('#profile-update-success-message').text('Benutzerdaten erfolgreich aktualisiert.');
                $('#profile-update-error-message').text('');
            } else {
                $('#profile-update-success-message').text('');
                $('#profile-update-error-message').text('Fehler beim Aktualisieren der Benutzerdaten.');
            }
        })
        .fail(() => {
            $('#profile-update-success-message').text('');
            $('#profile-update-error-message').text('Fehler beim Aktualisieren der Benutzerdaten.');
        });
    return false;
}
