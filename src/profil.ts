import {OlzApiResponses, OlzApiEndpoint} from './api/client';
import {olzDefaultFormSubmit, GetDataForRequestDict, getGender, getIsoDateFromSwissFormat} from './components/common/olz_default_form/olz_default_form';

export function olzProfileUpdateUser(userId: number, form: HTMLFormElement): boolean {
    const getDataForRequestDict: GetDataForRequestDict<OlzApiEndpoint.updateUser> = {
        id: () => userId,
        firstName: (f) => f['first-name'].value,
        lastName: (f) => f['last-name'].value,
        username: (f) => f.username.value,
        email: (f) => f.email.value,
        gender: (f) => getGender('gender', f.gender.value),
        birthdate: (f) => getIsoDateFromSwissFormat('birthdate', f.birthdate.value),
        street: (f) => f.street.value,
        postalCode: (f) => f['postal-code'].value,
        city: (f) => f.city.value,
        region: (f) => f.region.value,
        countryCode: (f) => f['country-code'].value,
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
