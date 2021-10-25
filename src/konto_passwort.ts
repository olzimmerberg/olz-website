import {OlzApiResponses, ValidationError} from './api/client';
import {olzDefaultFormSubmit, GetDataForRequestDict, getCountryCode, getEmail, getFormField, getGender, getIsoDateFromSwissFormat, getPassword, getPhone, getRequired, showErrorOnField, clearErrorOnField} from './components/common/olz_default_form/olz_default_form';

export function olzKontoSignUpWithPassword(form: HTMLFormElement): boolean {
    const getDataForRequestDict: GetDataForRequestDict<'signUpWithPassword'> = {
        firstName: (f) => getFormField(f, 'first-name'),
        lastName: (f) => getFormField(f, 'last-name'),
        username: (f) => getFormField(f, 'username'),
        password: (f) => {
            const password = getFormField(f, 'password');
            const passwordRepeat = getFormField(f, 'password-repeat');
            const hasInvalidRepetition = password !== passwordRepeat;
            if (hasInvalidRepetition) {
                showErrorOnField(form['password-repeat'], 'Das Passwort und die Wiederholung müssen übereinstimmen!');
            } else {
                clearErrorOnField(form['password-repeat']);
            }
            const result = getRequired('password', getPassword('password', password));
            if (hasInvalidRepetition) {
                throw new ValidationError('', {});
            }
            return result;
        },
        email: (f) => getRequired('email', getEmail('email', getFormField(f, 'email'))),
        phone: (f) => getPhone('phone', getFormField(f, 'phone')),
        gender: (f) => getGender('gender', getFormField(f, 'gender')),
        birthdate: (f) => getIsoDateFromSwissFormat('birthdate', getFormField(f, 'birthdate')),
        street: (f) => getFormField(f, 'street') || '',
        postalCode: (f) => getFormField(f, 'postal-code') || '',
        city: (f) => getFormField(f, 'city') || '',
        region: (f) => getFormField(f, 'region') || '',
        countryCode: (f) => getCountryCode('countryCode', getFormField(f, 'country-code')),
    };

    olzDefaultFormSubmit(
        'signUpWithPassword',
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
    return 'Benutzerkonto erfolgreich erstellt. Bitte warten...';
}
