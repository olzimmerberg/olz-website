import {olzApi} from '../../../Api/client';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, HandleResponseFunction, getCountryCode, getEmail, getFormField, getGender, getInteger, getIsoDate, getPhone, getRequired, getStringOrNull, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFieldResult, validFormData, invalidFormData} from '../../Common/OlzDefaultForm/OlzDefaultForm';
import {olzConfirm} from '../../Common/OlzConfirmationDialog/OlzConfirmationDialog';

import './OlzProfil.scss';

export function olzProfileDeleteUser(userId: number): boolean {
    olzConfirm(
        'OLZ-Konto wirklich unwiderruflich löschen?',
        {confirmButtonStyle: 'btn-danger', confirmLabel: 'Löschen'},
    ).then(() => {
        olzApi.call('deleteUser', {id: userId}).then(() => {
            window.location.href = '/';
        });
    });
    return false;
}

export function olzProfileUpdateUser(userId: number, form: HTMLFormElement): boolean {
    olzProfileActuallyUpdateUser(userId, form);
    return false;
}

const handleResponse: HandleResponseFunction<'updateUser'> = (response) => {
    if (response.status !== 'OK') {
        throw new Error(`Antwort: ${response.status}`);
    }
    return 'Benutzerdaten erfolgreich aktualisiert.';
};

export async function olzProfileActuallyUpdateUser(userId: number, form: HTMLFormElement): Promise<boolean> {
    const getDataForRequestFunction: GetDataForRequestFunction<'updateUser'> = (f) => {
        const fieldResults: OlzRequestFieldResult<'updateUser'> = {
            id: validFieldResult('', userId),
            meta: {
                ownerUserId: validFieldResult('password', null),
                ownerRoleId: validFieldResult('password', null),
                onOff: validFieldResult('password', true),
            },
            data: {
                firstName: getRequired(getStringOrNull(getFormField(f, 'first-name'), {trim: true})),
                lastName: getRequired(getStringOrNull(getFormField(f, 'last-name'), {trim: true})),
                username: getRequired(getStringOrNull(getFormField(f, 'username'), {trim: true})),
                phone: getPhone(getFormField(f, 'phone')),
                email: getRequired(getEmail(getFormField(f, 'email'))),
                password: validFieldResult('password', null),
                gender: getGender(getFormField(f, 'gender')),
                birthdate: getIsoDate(getFormField(f, 'birthdate')),
                street: getStringOrNull(getFormField(f, 'street')),
                postalCode: getStringOrNull(getFormField(f, 'postal-code')),
                city: getStringOrNull(getFormField(f, 'city')),
                region: getStringOrNull(getFormField(f, 'region')),
                countryCode: getStringOrNull(getCountryCode(getFormField(f, 'country-code'))),
                siCardNumber: getInteger(getFormField(f, 'si-card-number')),
                solvNumber: getStringOrNull(getFormField(f, 'solv-number')),
                avatarId: getStringOrNull(getFormField(f, 'avatar-id')),
            },
        };
        if (!isFieldResultOrDictThereofValid(fieldResults)) {
            return invalidFormData([
                ...getFieldResultOrDictThereofErrors(fieldResults),
            ]);
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
