import {callOlzApi, OlzApiResponses} from './api/client';
import {olzDefaultFormSubmit, GetDataForRequestDict, getCountryCode, getEmail, getFormField, getGender, getIsoDateFromSwissFormat, getPhone, getRequired} from './components/common/olz_default_form/olz_default_form';

export function olzKontoLoginWithStrava(code: string): boolean {
    $('#sign-up-with-strava-login-status').attr('class', 'alert alert-secondary');
    $('#sign-up-with-strava-login-status').text('Login mit Strava...');

    callOlzApi(
        'loginWithStrava',
        {code},
    )
        .then((response) => {
            if (response.status === 'AUTHENTICATED') {
                $('#sign-up-with-strava-login-status').attr('class', 'alert alert-success');
                $('#sign-up-with-strava-login-status').text('Login mit Strava erfolgreich.');
                // TODO: This could probably be done more smoothly!
                window.location.href = 'startseite.php';
            } else if (response.status === 'NOT_REGISTERED') {
                const userIdentifier = response.userIdentifier;
                if (!userIdentifier) {
                    $('#sign-up-with-strava-login-status').attr('class', 'alert alert-danger');
                    $('#sign-up-with-strava-login-status').text('Fehler beim Login mit Strava: Keine Benutzeridentifikation.');
                    return;
                }
                const expiresAt = response.expiresAt;
                if (!expiresAt) {
                    $('#sign-up-with-strava-login-status').attr('class', 'alert alert-danger');
                    $('#sign-up-with-strava-login-status').text('Fehler beim Login mit Strava: Kein Ablaufdatum.');
                    return;
                }
                $('#sign-up-with-strava-login-status').attr('class', 'alert alert-primary');
                $('#sign-up-with-strava-login-status').html('Erstelle jetzt dein OLZ-Konto. Felder mit <span class=\'required-field-asterisk\'>*</span> müssen zwingend ausgefüllt werden.');
                $('#sign-up-with-strava-form').removeClass('hidden');
                $('#sign-up-with-strava-form [name=strava-user]').val(userIdentifier);
                $('#sign-up-with-strava-form [name=access-token]').val(`${response.tokenType} ${response.accessToken}`);
                $('#sign-up-with-strava-form [name=refresh-token]').val(`${response.tokenType} ${response.refreshToken}`);
                $('#sign-up-with-strava-form [name=expires-at]').val(expiresAt);
                $('#sign-up-with-strava-form [name=first-name]').val(response.firstName ?? '');
                $('#sign-up-with-strava-form [name=last-name]').val(response.lastName ?? '');
                const username = `${response.firstName} ${response.lastName}`.toLowerCase()
                    .replace('ä', 'ae').replace('ö', 'oe').replace('ü', 'ue')
                    .replace(' ', '.').replace(/[^a-z0-9.-]/, '?');
                $('#sign-up-with-strava-form [name=username]').val(username);
                if (response.gender) {
                    $('#sign-up-with-strava-form [name=gender]').val(response.gender);
                }
                $('#sign-up-with-strava-form [name=city]').val(response.city ?? '');
                $('#sign-up-with-strava-form [name=region]').val(response.region ?? '');
            } else {
                $('#sign-up-with-strava-login-status').attr('class', 'alert alert-danger');
                $('#sign-up-with-strava-login-status').text('Fehler beim Login mit Strava.');
            }
        })
        .catch(() => {
            $('#sign-up-with-strava-login-status').attr('class', 'alert alert-danger');
            $('#sign-up-with-strava-login-status').text('Fehler beim Login mit Strava.');
        });
    return false;
}

export function olzKontoSignUpWithStrava(form: HTMLFormElement): boolean {
    const getDataForRequestDict: GetDataForRequestDict<'signUpWithStrava'> = {
        stravaUser: (f) => getFormField(f, 'strava-user'),
        accessToken: (f) => getFormField(f, 'access-token'),
        refreshToken: (f) => getFormField(f, 'refresh-token'),
        expiresAt: (f) => getFormField(f, 'expires-at'),
        firstName: (f) => getFormField(f, 'first-name'),
        lastName: (f) => getFormField(f, 'last-name'),
        username: (f) => getFormField(f, 'username'),
        email: (f) => getRequired('email', getEmail('email', getFormField(f, 'email'))),
        phone: (f) => getPhone('phone', getFormField(f, 'phone')),
        gender: (f) => getGender('gender', getFormField(f, 'gender')),
        birthdate: (f) => getIsoDateFromSwissFormat('birthdate', getFormField(f, 'birthdate')),
        street: (f) => getFormField(f, 'street'),
        postalCode: (f) => getFormField(f, 'postal-code'),
        city: (f) => getFormField(f, 'city'),
        region: (f) => getFormField(f, 'region'),
        countryCode: (f) => getCountryCode('countryCode', getFormField(f, 'country-code')),
    };

    olzDefaultFormSubmit(
        'signUpWithStrava',
        getDataForRequestDict,
        form,
        handleResponse,
    );
    return false;
}

function handleResponse(response: OlzApiResponses['signUpWithPassword']): string|void {
    if (response.status !== 'OK') {
        throw new Error(`Fehler beim Erstellen des Benutzerkontos: ${response.status}`);
    }
    window.setTimeout(() => {
        // TODO: This could probably be done more smoothly!
        window.location.href = 'startseite.php';
    }, 3000);
    return 'Benutzerkonto erfolgreich erstellt.';
}
