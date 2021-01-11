import {OlzApiEndpoint, callOlzApi} from './api/client';

export function olzProfileUpdateUser(userId, form) {
    const firstName = form['first-name'].value; 
    const lastName = form['last-name'].value;
    const username = form['username'].value;
    const email = form['email'].value;

    callOlzApi(
        OlzApiEndpoint.updateUser,
        {firstName, lastName, username, email, id: userId},
    )
        .then(response => {
            if (response.status === 'OK') {
                $('#profile-update-success-message').text('Benutzerdaten erfolgreich aktualisiert.');
                $('#profile-update-error-message').text('');
            } else {
                $('#profile-update-success-message').text('');
                $('#profile-update-error-message').text('Fehler beim Aktualisieren der Benutzerdaten.');
            }
        })
        .catch(() => {
            $('#profile-update-success-message').text('');
            $('#profile-update-error-message').text('Fehler beim Aktualisieren der Benutzerdaten.');
        });
    return false;
}
