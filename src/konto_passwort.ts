import {OlzApiEndpoint, callOlzApi} from './api/client';

export function olzKontoSignUpWithPassword(form: Record<string, {value?: string}>): boolean {
    const firstName = form['first-name'].value;
    const lastName = form['last-name'].value;
    const username = form.username.value;
    const password = form.password.value;
    const passwordRepeat = form['password-repeat'].value;
    const email = form.email.value;
    const gender = getGender(form.gender.value);
    const birthdate = form.birthdate.value || null;
    const street = form.street.value;
    const postalCode = form['postal-code'].value;
    const city = form.city.value;
    const region = form.region.value;
    const countryCode = form['country-code'].value;

    if (password !== passwordRepeat) {
        $('#sign-up-with-password-success-message').text('');
        $('#sign-up-with-password-error-message').text('Das Passwort und die Wiederholung müssen übereinstimmen!');
        return false;
    }

    callOlzApi(
        OlzApiEndpoint.signUpWithPassword,
        {firstName, lastName, username, password, email, gender, birthdate,
            street, postalCode, city, region, countryCode},
    )
        .then((response) => {
            if (response.status === 'OK') {
                $('#sign-up-with-password-success-message').text('Benutzerkonto erfolgreich erstellt.');
                $('#sign-up-with-password-error-message').text('');
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.href = 'startseite.php';
                }, 3000);
            } else {
                $('#sign-up-with-password-success-message').text('');
                $('#sign-up-with-password-error-message').text('Fehler beim Erstellen des Benutzerkontos.');
            }
        })
        .catch(() => {
            $('#sign-up-with-password-success-message').text('');
            $('#sign-up-with-password-error-message').text('Fehler beim Erstellen des Benutzerkontos.');
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
