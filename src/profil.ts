import {OlzApiResponses, OlzApiEndpoint} from './api/client';
import {olzDefaultFormSubmit, GetDataForRequestDict, getCountryCode, getEmail, getFormField, getGender, getIsoDateFromSwissFormat, getPhone, getRequired} from './components/common/olz_default_form/olz_default_form';

export function olzProfileUpdateUser(userId: number, form: HTMLFormElement): boolean {
    const getDataForRequestDict: GetDataForRequestDict<OlzApiEndpoint.updateUser> = {
        id: () => userId,
        firstName: (f) => getFormField(f, 'first-name'),
        lastName: (f) => getFormField(f, 'last-name'),
        username: (f) => getFormField(f, 'username'),
        phone: (f) => getPhone('phone', getFormField(f, 'phone')),
        email: (f) => getRequired('email', getEmail('email', getFormField(f, 'email'))),
        gender: (f) => getGender('gender', getFormField(f, 'gender')),
        birthdate: (f) => getIsoDateFromSwissFormat('birthdate', getFormField(f, 'birthdate')),
        street: (f) => getFormField(f, 'street'),
        postalCode: (f) => getFormField(f, 'postal-code'),
        city: (f) => getFormField(f, 'city'),
        region: (f) => getFormField(f, 'region'),
        countryCode: (f) => getCountryCode('countryCode', getFormField(f, 'country-code')),
    };

    olzDefaultFormSubmit(
        OlzApiEndpoint.updateUser,
        getDataForRequestDict,
        form,
        handleResponse,
    );
    return false;
}

function handleResponse(response: OlzApiResponses[OlzApiEndpoint.updateUser]): string|void {
    if (response.status !== 'OK') {
        throw new Error(`Antwort: ${response.status}`);
    }
    return 'Benutzerdaten erfolgreich aktualisiert.';
}
