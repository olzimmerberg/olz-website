import {OlzApiResponses, OlzApiEndpoint} from './api/client';
import {olzDefaultFormSubmit, GetDataForRequestDict, getCountryCode, getEmail, getGender, getIsoDateFromSwissFormat, getPhone} from './components/common/olz_default_form/olz_default_form';

export function olzProfileUpdateUser(userId: number, form: HTMLFormElement): boolean {
    const getDataForRequestDict: GetDataForRequestDict<OlzApiEndpoint.updateUser> = {
        id: () => userId,
        firstName: (f) => f['first-name'].value,
        lastName: (f) => f['last-name'].value,
        username: (f) => f.username.value,
        phone: (f) => getPhone('phone', f.phone.value),
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
        OlzApiEndpoint.updateUser,
        getDataForRequestDict,
        form,
        handleResponse,
    );
}

function handleResponse(response: OlzApiResponses[OlzApiEndpoint.updateUser]): string|null {
    if (response.status !== 'OK') {
        throw new Error(`Antwort: ${response.status}`);
    }
    return 'Benutzerdaten erfolgreich aktualisiert.';
}
