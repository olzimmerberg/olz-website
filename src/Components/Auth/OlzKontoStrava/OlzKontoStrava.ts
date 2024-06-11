import $ from 'jquery';

import {olzApi} from '../../../Api/client';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, HandleResponseFunction, getCountryCode, getEmail, getFormField, getGender, getInteger, getIsoDate, getPhone, getRequired, getStringOrNull, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData} from '../../../Components/Common/OlzDefaultForm/OlzDefaultForm';

export function olzKontoLoginWithStrava(code: string): boolean {
    $('#sign-up-with-strava-login-status').attr('class', 'alert alert-secondary');
    $('#sign-up-with-strava-login-status').text('Login mit Strava...');

    olzApi.call(
        'loginWithStrava',
        {code},
    )
        .then((response) => {
            if (response.status === 'AUTHENTICATED') {
                $('#sign-up-with-strava-login-status').attr('class', 'alert alert-success');
                $('#sign-up-with-strava-login-status').text('Login mit Strava erfolgreich.');
                // This could probably be done more smoothly!
                window.location.href = '/';
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

const handleResponse: HandleResponseFunction<'signUpWithStrava'> = (response) => {
    if (response.status !== 'OK') {
        throw new Error(`Fehler beim Erstellen des Benutzerkontos: ${response.status}`);
    }
    window.setTimeout(() => {
        // This could probably be done more smoothly!
        window.location.href = '/';
    }, 1000);
    return 'Benutzerkonto erfolgreich erstellt.';
};

export function olzKontoSignUpWithStrava(form: HTMLFormElement): boolean {
    const getDataForRequestFn: GetDataForRequestFunction<'signUpWithStrava'> = (f) => {
        const fieldResults: OlzRequestFieldResult<'signUpWithStrava'> = {
            stravaUser: getRequired(getFormField(f, 'strava-user')),
            accessToken: getRequired(getFormField(f, 'access-token')),
            refreshToken: getRequired(getFormField(f, 'refresh-token')),
            expiresAt: getRequired(getFormField(f, 'expires-at')),
            firstName: getRequired(getStringOrNull(getFormField(f, 'first-name'))),
            lastName: getRequired(getStringOrNull(getFormField(f, 'last-name'))),
            username: getRequired(getStringOrNull(getFormField(f, 'username'))),
            email: getRequired(getEmail(getFormField(f, 'email'))),
            phone: getPhone(getFormField(f, 'phone')),
            gender: getGender(getFormField(f, 'gender')),
            birthdate: getIsoDate(getFormField(f, 'birthdate')),
            street: getRequired(getFormField(f, 'street')),
            postalCode: getRequired(getFormField(f, 'postal-code')),
            city: getRequired(getFormField(f, 'city')),
            region: getRequired(getFormField(f, 'region')),
            countryCode: getRequired(getCountryCode(getFormField(f, 'country-code'))),
            siCardNumber: getInteger(getFormField(f, 'si-card-number')),
            solvNumber: getFormField(f, 'solv-number'),
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
