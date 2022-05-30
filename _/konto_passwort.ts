import {OlzApiResponses} from './api/client';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, getAsserted, getCountryCode, getEmail, getFormField, getGender, getInteger, getIsoDateFromSwissFormat, getPassword, getPhone, getRequired, getStringOrNull, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData} from './components/common/olz_default_form/olz_default_form';

export function olzKontoSignUpWithPassword(form: HTMLFormElement): boolean {
    const getDataForRequestFn: GetDataForRequestFunction<'signUpWithPassword'> = (f) => {
        const password = getRequired(getPassword(getFormField(f, 'password')));
        let passwordRepeat = getFormField(f, 'password-repeat');
        const hasValidRepetition = password.value === passwordRepeat.value;
        passwordRepeat = getAsserted(
            () => hasValidRepetition,
            'Das Passwort und die Wiederholung müssen übereinstimmen!',
            passwordRepeat,
        );
        const fieldResults: OlzRequestFieldResult<'signUpWithPassword'> = {
            firstName: getRequired(getStringOrNull(getFormField(f, 'first-name'))),
            lastName: getRequired(getStringOrNull(getFormField(f, 'last-name'))),
            username: getRequired(getStringOrNull(getFormField(f, 'username'))),
            password: password,
            email: getRequired(getEmail(getFormField(f, 'email'))),
            phone: getPhone(getFormField(f, 'phone')),
            gender: getGender(getFormField(f, 'gender')),
            birthdate: getIsoDateFromSwissFormat(getFormField(f, 'birthdate')),
            street: getFormField(f, 'street'),
            postalCode: getFormField(f, 'postal-code'),
            city: getFormField(f, 'city'),
            region: getFormField(f, 'region'),
            countryCode: getCountryCode(getFormField(f, 'country-code')),
            siCardNumber: getInteger(getFormField(f, 'si-card-number')),
            solvNumber: getFormField(f, 'solv-number'),
        };
        if (!isFieldResultOrDictThereofValid(fieldResults) || !isFieldResultOrDictThereofValid(passwordRepeat)) {
            return invalidFormData([
                ...getFieldResultOrDictThereofErrors(fieldResults),
                ...getFieldResultOrDictThereofErrors(passwordRepeat),
            ]);
        }
        return validFormData<'signUpWithPassword'>(getFieldResultOrDictThereofValue(fieldResults));
    };

    olzDefaultFormSubmit(
        'signUpWithPassword',
        getDataForRequestFn,
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
