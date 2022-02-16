import {callOlzApi, OlzApiResponses} from './api/client';
import {olzDefaultFormSubmit, GetDataForRequestFunction, getCountryCode, getEmail, getFormField, getGender, getIsoDateFromSwissFormat, getPhone, getRequired, getStringOrNull, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData} from './components/common/olz_default_form/olz_default_form';

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
    const getDataForRequestFn: GetDataForRequestFunction<'signUpWithStrava'> = (f) => {
        const fieldResults = {
            stravaUser: getFormField(f, 'strava-user'),
            accessToken: getFormField(f, 'access-token'),
            refreshToken: getFormField(f, 'refresh-token'),
            expiresAt: getFormField(f, 'expires-at'),
            firstName: getRequired(getStringOrNull(getFormField(f, 'first-name'))),
            lastName: getRequired(getStringOrNull(getFormField(f, 'last-name'))),
            username: getRequired(getStringOrNull(getFormField(f, 'username'))),
            email: getRequired(getEmail(getFormField(f, 'email'))),
            phone: getPhone(getFormField(f, 'phone')),
            gender: getGender(getFormField(f, 'gender')),
            birthdate: getIsoDateFromSwissFormat(getFormField(f, 'birthdate')),
            street: getFormField(f, 'street'),
            postalCode: getFormField(f, 'postal-code'),
            city: getFormField(f, 'city'),
            region: getFormField(f, 'region'),
            countryCode: getCountryCode(getFormField(f, 'country-code')),
        };
        if (!isFieldResultOrDictThereofValid(fieldResults)) {
            return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
        }
        return validFormData(getFieldResultOrDictThereofValue(fieldResults));
    };

    olzDefaultFormSubmit(
        'signUpWithStrava',
        getDataForRequestFn,
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
