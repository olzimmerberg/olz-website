import {OlzApiResponses, callOlzApi} from '../src/Api/client';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, getCountryCode, getEmail, getFormField, getGender, getInteger, getIsoDateFromSwissFormat, getPhone, getRequired, getStringOrNull, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFieldResult, validFormData, invalidFormData} from '../src/Components/Common/OlzDefaultForm/OlzDefaultForm';
import {olzConfirm} from '../src/Components/Common/OlzConfirmationDialog/OlzConfirmationDialog';

export function olzProfileDeleteUser(userId: number): boolean {
    olzConfirm(
        'OLZ-Konto wirklich unwiderruflich löschen?',
        {confirmButtonStyle: 'btn-danger', confirmLabel: 'Löschen'},
    ).then(() => {
        callOlzApi('deleteUser', {id: userId}).then(() => {
            window.location.href = 'startseite.php';
        });
    });
    return false;
}

export function olzProfileUpdateUser(userId: number, form: HTMLFormElement): boolean {
    const getDataForRequestFunction: GetDataForRequestFunction<'updateUser'> = (f) => {
        const fieldResults: OlzRequestFieldResult<'updateUser'> = {
            id: validFieldResult('', userId),
            firstName: getRequired(getStringOrNull(getFormField(f, 'first-name'))),
            lastName: getRequired(getStringOrNull(getFormField(f, 'last-name'))),
            username: getRequired(getStringOrNull(getFormField(f, 'username'))),
            phone: getPhone(getFormField(f, 'phone')),
            email: getRequired(getEmail(getFormField(f, 'email'))),
            gender: getGender(getFormField(f, 'gender')),
            birthdate: getIsoDateFromSwissFormat(getFormField(f, 'birthdate')),
            street: getFormField(f, 'street'),
            postalCode: getFormField(f, 'postal-code'),
            city: getFormField(f, 'city'),
            region: getFormField(f, 'region'),
            countryCode: getCountryCode(getFormField(f, 'country-code')),
            siCardNumber: getInteger(getFormField(f, 'si-card-number')),
            solvNumber: getFormField(f, 'solv-number'),
            avatarId: getStringOrNull(getFormField(f, 'avatar-id')),
        };
        if (!isFieldResultOrDictThereofValid(fieldResults)) {
            return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
        }
        return validFormData(getFieldResultOrDictThereofValue(fieldResults));
    };

    olzDefaultFormSubmit(
        'updateUser',
        getDataForRequestFunction,
        form,
        handleResponse,
    );
    return false;
}

function handleResponse(response: OlzApiResponses['updateUser']): string|void {
    if (response.status !== 'OK') {
        throw new Error(`Antwort: ${response.status}`);
    }
    return 'Benutzerdaten erfolgreich aktualisiert.';
}
