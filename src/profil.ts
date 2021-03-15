import {OlzApiEndpoint, callOlzApi} from './api/client';

export function olzProfileUpdateUser(userId: number, form: Record<string, {value?: string}>): boolean {
    const firstName = form['first-name'].value;
    const lastName = form['last-name'].value;
    const username = form.username.value;
    const email = form.email.value;
    const gender = getGender(form.gender.value);
    const birthdate = getIsoDateFromSwissFormat(form.birthdate.value);
    const street = form.street.value;
    const postalCode = form['postal-code'].value;
    const city = form.city.value;
    const region = form.region.value;
    const countryCode = form['country-code'].value;

    callOlzApi(
        OlzApiEndpoint.updateUser,
        {id: userId, firstName, lastName, username, email, gender, birthdate, street, postalCode, city, region, countryCode},
    )
        .then((response) => {
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

function getGender(genderInput?: string): 'M'|'F'|'O'|null {
    switch (genderInput) {
        case 'M': return 'M';
        case 'F': return 'F';
        case 'O': return 'O';
        default: return null;
    }
}

function getIsoDateFromSwissFormat(date?: string): string|undefined {
    if (date === undefined) {
        return undefined;
    }
    const res = /^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/.exec(date);
    if (!res) {
        return undefined;
    }
    const timestamp = Date.parse(`${res[3]}-${res[2]}-${res[1]}`);
    if (!timestamp) {
        return undefined;
    }
    const isoDate = new Date(timestamp).toISOString().substr(0, 10);
    return `${isoDate} 12:00:00`;
}
