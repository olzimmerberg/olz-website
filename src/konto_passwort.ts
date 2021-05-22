import {OlzApiEndpoint, OlzApiResponses, ValidationError} from './api/client';
import {olzDefaultFormSubmit, GetDataForRequestDict, getCountryCode, getEmail, getGender, getIsoDateFromSwissFormat, getPassword, showErrorOnField, clearErrorOnField} from './components/common/olz_default_form/olz_default_form';

export function olzKontoSignUpWithPassword(form: HTMLFormElement): boolean {
    const getDataForRequestDict: GetDataForRequestDict<OlzApiEndpoint.signUpWithPassword> = {
        firstName: (f) => f['first-name'].value,
        lastName: (f) => f['last-name'].value,
        username: (f) => f.username.value,
        password: (f) => {
            const password = f.password.value;
            const passwordRepeat = form['password-repeat'].value;
            const hasInvalidRepetition = password !== passwordRepeat;
            if (hasInvalidRepetition) {
                showErrorOnField(form['password-repeat'], 'Das Passwort und die Wiederholung müssen übereinstimmen!');
            } else {
                clearErrorOnField(form['password-repeat']);
            }
            const result = getPassword('password', password);
            if (hasInvalidRepetition) {
                throw new ValidationError('', {});
            }
            return result;
        },
        email: (f) => getEmail('email', f.email.value),
        gender: (f) => getGender('gender', f.gender.value),
        birthdate: (f) => getIsoDateFromSwissFormat('birthdate', f.birthdate.value),
        street: (f) => f.street.value,
        postalCode: (f) => f['postal-code'].value,
        city: (f) => f.city.value,
        region: (f) => f.region.value,
        countryCode: (f) => getCountryCode('countryCode', f['country-code'].value),
    };

    return olzDefaultFormSubmit(
        OlzApiEndpoint.signUpWithPassword,
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
