import {OlzApiEndpoint, callOlzApi, OlzApiResponses} from './api/client';
import {olzDefaultFormSubmit, GetDataForRequestDict, getCountryCode, getEmail, getGender, getIsoDateFromSwissFormat, getPhone} from './components/common/olz_default_form/olz_default_form';

export function olzKontoLoginWithStrava(code: string): boolean {
    $('#sign-up-with-strava-login-status').attr('class', 'alert alert-secondary');
    $('#sign-up-with-strava-login-status').text('Login mit Strava...');

    callOlzApi(
        OlzApiEndpoint.loginWithStrava,
        {code},
    )
        .then((response) => {
            if (response.status === 'AUTHENTICATED') {
                $('#sign-up-with-strava-login-status').attr('class', 'alert alert-success');
                $('#sign-up-with-strava-login-status').text('Login mit Strava erfolgreich.');
                // TODO: This could probably be done more smoothly!
                window.location.href = 'startseite.php';
            } else if (response.status === 'NOT_REGISTERED') {
                $('#sign-up-with-strava-login-status').attr('class', 'alert alert-primary');
                $('#sign-up-with-strava-login-status').html('Erstelle jetzt dein OLZ-Konto. Felder mit <span class=\'required-field-asterisk\'>*</span> müssen zwingend ausgefüllt werden.');
                $('#sign-up-with-strava-form').removeClass('hidden');
                $('#sign-up-with-strava-form [name=strava-user]').val(response.userIdentifier);
                $('#sign-up-with-strava-form [name=access-token]').val(`${response.tokenType} ${response.accessToken}`);
                $('#sign-up-with-strava-form [name=refresh-token]').val(`${response.tokenType} ${response.refreshToken}`);
                $('#sign-up-with-strava-form [name=expires-at]').val(response.expiresAt);
                $('#sign-up-with-strava-form [name=first-name]').val(response.firstName);
                $('#sign-up-with-strava-form [name=last-name]').val(response.lastName);
                const username = `${response.firstName} ${response.lastName}`.toLowerCase()
                    .replace('ä', 'ae').replace('ö', 'oe').replace('ü', 'ue')
                    .replace(' ', '.').replace(/[^a-z0-9.-]/, '?');
                $('#sign-up-with-strava-form [name=username]').val(username);
                $('#sign-up-with-strava-form [name=gender]').val(response.gender);
                $('#sign-up-with-strava-form [name=city]').val(response.city);
                $('#sign-up-with-strava-form [name=region]').val(response.region);
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
    const getDataForRequestDict: GetDataForRequestDict<OlzApiEndpoint.signUpWithStrava> = {
        stravaUser: (f) => f['strava-user'].value,
        accessToken: (f) => f['access-token'].value,
        refreshToken: (f) => f['refresh-token'].value,
        expiresAt: (f) => f['expires-at'].value,
        firstName: (f) => f['first-name'].value,
        lastName: (f) => f['last-name'].value,
        username: (f) => f.username.value,
        email: (f) => getEmail('email', f.email.value),
        phone: (f) => getPhone('phone', f.phone.value),
        gender: (f) => getGender('gender', f.gender.value),
        birthdate: (f) => getIsoDateFromSwissFormat('birthdate', f.birthdate.value),
        street: (f) => f.street.value,
        postalCode: (f) => f['postal-code'].value,
        city: (f) => f.city.value,
        region: (f) => f.region.value,
        countryCode: (f) => getCountryCode('countryCode', f['country-code'].value),
    };

    return olzDefaultFormSubmit(
        OlzApiEndpoint.signUpWithStrava,
        getDataForRequestDict,
        form,
        handleResponse,
    );
}

function handleResponse(response: OlzApiResponses[OlzApiEndpoint.signUpWithPassword]): string|null {
    if (response.status !== 'OK') {
        throw new Error(`Fehler beim Erstellen des Benutzerkontos: ${response.status}`);
    }
    window.setTimeout(() => {
        // TODO: This could probably be done more smoothly!
        window.location.href = 'startseite.php';
    }, 3000);
    return 'Benutzerkonto erfolgreich erstellt.';
}
