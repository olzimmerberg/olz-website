
export function olzKontoLoginWithStrava(code) {
    $('#sign-up-with-strava-login-status').attr('class', 'alert alert-secondary');
    $('#sign-up-with-strava-login-status').text('Login mit Strava...');
    $.ajax({
        type: 'POST',
        url: `/_/api/index.php/loginWithStrava`, 
        data: JSON.stringify({code}),
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
    })
        .done(response => {
            if (response.status === 'AUTHENTICATED') {
                $('#sign-up-with-strava-login-status').attr('class', 'alert alert-success');
                $('#sign-up-with-strava-login-status').text('Login mit Strava erfolgreich.');
                // TODO: This could probably be done more smoothly!
                window.location.href = 'startseite.php';
            } else if (response.status === 'NOT_REGISTERED') {
                $('#sign-up-with-strava-login-status').attr('class', 'alert alert-primary');
                $('#sign-up-with-strava-login-status').html("Erstelle jetzt dein OLZ-Konto. Felder mit <span class='required-field-asterisk'>*</span> müssen zwingend ausgefüllt werden.");
                $('#sign-up-with-strava-form').removeClass('hidden');
                $('#sign-up-with-strava-form [name=strava-user]').val(response.userIdentifier);
                $('#sign-up-with-strava-form [name=access-token]').val(`${response.tokenType} ${response.accessToken}`);
                $('#sign-up-with-strava-form [name=refresh-token]').val(`${response.tokenType} ${response.refreshToken}`);
                $('#sign-up-with-strava-form [name=expires-at]').val(response.expiresAt);
                $('#sign-up-with-strava-form [name=first-name]').val(response.firstName);
                $('#sign-up-with-strava-form [name=last-name]').val(response.lastName);
                const username = `${response.firstName} ${response.lastName}`.toLowerCase()
                    .replace('ä', 'ae').replace('ö', 'oe').replace('ü', 'ue')
                    .replace(' ', '.').replace(/[^a-z0-9\.\-]/, '?');
                $('#sign-up-with-strava-form [name=username]').val(username);
                $('#sign-up-with-strava-form [name=gender]').val(response.gender);
                $('#sign-up-with-strava-form [name=city]').val(response.city);
                $('#sign-up-with-strava-form [name=region]').val(response.region);
            } else {
                $('#sign-up-with-strava-login-status').attr('class', 'alert alert-danger');
                $('#sign-up-with-strava-login-status').text('Fehler beim Login mit Strava.');
            }
        })
        .fail(() => {
            $('#sign-up-with-strava-login-status').attr('class', 'alert alert-danger');
            $('#sign-up-with-strava-login-status').text('Fehler beim Login mit Strava.');
        });
    return false;
}

export function olzKontoSignUpWithStrava(form) {
    const stravaUser = form['strava-user'].value;
    const accessToken = form['access-token'].value;
    const refreshToken = form['refresh-token'].value;
    const expiresAt = form['expires-at'].value;

    const firstName = form['first-name'].value;
    const lastName = form['last-name'].value;
    const username = form['username'].value;
    const email = form['email'].value;
    const gender = form['gender'].value || null;
    const birthdate = form['birthdate'].value || null;
    const street = form['street'].value;
    const postalCode = form['postal-code'].value;
    const city = form['city'].value;
    const region = form['region'].value;
    const countryCode = form['country-code'].value;

    $.ajax({
        type: 'POST',
        url: `/_/api/index.php/signUpWithStrava`, 
        data: JSON.stringify({stravaUser, accessToken, refreshToken, expiresAt, 
            firstName, lastName, username, email, gender, birthdate, street, 
            postalCode, city, region, countryCode}),
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
    })
        .done(response => {
            if (response.status === 'OK') {
                $('#sign-up-with-strava-success-message').text('Benutzerkonto erfolgreich erstellt.');
                $('#sign-up-with-strava-error-message').text('');
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.href = 'startseite.php';
                }, 3000);
            } else {
                $('#sign-up-with-strava-success-message').text('');
                $('#sign-up-with-strava-error-message').text('Fehler beim Erstellen des Benutzerkontos.');
            }
        })
        .fail(() => {
            $('#sign-up-with-strava-success-message').text('');
            $('#sign-up-with-strava-error-message').text('Fehler beim Erstellen des Benutzerkontos.');
        });
    return false;
}
