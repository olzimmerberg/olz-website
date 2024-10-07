import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzUserData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzImageField} from '../../../Components/Upload/OlzImageField/OlzImageField';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {codeHref, user} from '../../../Utils/constants';
import {getApiNumber, getApiString, getFormNumber, getFormString, getResolverResult, validateCountryCodeOrNull, validateDateOrNull, validateEmail, validateEmailOrNull, validateGender, validateIntegerOrNull, validateNotEmpty, validatePassword, validatePhoneOrNull} from '../../../Utils/formUtils';
import {loadRecaptcha, loadRecaptchaToken} from '../../../Utils/recaptchaUtils';

import './OlzEditUserModal.scss';

interface OlzEditUserForm {
    parentUserId: number|null;
    firstName: string;
    lastName: string;
    username: string;
    password: string|null;
    passwordRepeat: string|null;
    email: string;
    phone: string;
    gender: OlzUserData['gender'];
    birthdate: string;
    street: string;
    postalCode: string;
    city: string;
    region: string;
    countryCode: string;
    siCardNumber: string;
    solvNumber: string;
    avatarImageId: string|null;
}

const resolver: Resolver<OlzEditUserForm> = async (values) => {
    const errors: FieldErrors<OlzEditUserForm> = {};
    errors.firstName = validateNotEmpty(values.firstName);
    errors.lastName = validateNotEmpty(values.lastName);
    errors.username = validateNotEmpty(values.username);
    if (values.password) {
        errors.password = validatePassword(values.password);
        if (values.password !== values.passwordRepeat) {
            errors.passwordRepeat = {type: '', message: 'Das Passwort und die Wiederholung müssen übereinstimmen!'};
        }
    }
    if (values.parentUserId === null) {
        [errors.email, values.email] = validateEmail(values.email);
    } else {
        [errors.email, values.email] = validateEmailOrNull(values.email);
    }
    [errors.phone, values.phone] = validatePhoneOrNull(values.phone);
    [errors.gender, values.gender] = validateGender(values.gender);
    [errors.birthdate, values.birthdate] = validateDateOrNull(values.birthdate);
    [errors.countryCode, values.countryCode] = validateCountryCodeOrNull(values.countryCode);
    errors.siCardNumber = validateIntegerOrNull(values.siCardNumber);

    return getResolverResult(errors, values);
};

function getFormFromApi(apiData?: OlzUserData): OlzEditUserForm {
    return {
        parentUserId: apiData?.parentUserId ?? null,
        firstName: getFormString(apiData?.firstName),
        lastName: getFormString(apiData?.lastName),
        username: getFormString(apiData?.username),
        password: '',
        passwordRepeat: '',
        email: getFormString(apiData?.email),
        phone: getFormString(apiData?.phone),
        gender: apiData?.gender ?? null,
        birthdate: getFormString(apiData?.birthdate),
        street: getFormString(apiData?.street),
        postalCode: getFormString(apiData?.postalCode),
        city: getFormString(apiData?.city),
        region: getFormString(apiData?.region),
        countryCode: getFormString(apiData?.countryCode),
        siCardNumber: getFormNumber(apiData?.siCardNumber),
        solvNumber: getFormString(apiData?.solvNumber),
        avatarImageId: getFormString(apiData?.avatarImageId),
    };
}

function getApiFromForm(formData: OlzEditUserForm): OlzUserData {
    return {
        parentUserId: formData.parentUserId,
        firstName: getApiString(formData.firstName) ?? '',
        lastName: getApiString(formData.lastName) ?? '',
        username: getApiString(formData.username) ?? '',
        password: formData.password ? getApiString(formData.password) : null,
        email: getApiString(formData.email),
        phone: getApiString(formData.phone),
        gender: formData.gender,
        birthdate: getApiString(formData.birthdate),
        street: getApiString(formData.street),
        postalCode: getApiString(formData.postalCode),
        city: getApiString(formData.city),
        region: getApiString(formData.region),
        countryCode: getApiString(formData.countryCode),
        siCardNumber: getApiNumber(formData.siCardNumber),
        solvNumber: getApiString(formData.solvNumber),
        avatarImageId: formData.avatarImageId ? getApiString(formData.avatarImageId) : null,
    };
}

export function getUsernameSuggestion(firstName: string, lastName: string): string {
    return `${firstName} ${lastName}`.toLowerCase()
        .replace(/ä/g, 'ae').replace(/ö/g, 'oe').replace(/ü/g, 'ue')
        .replace(/ /g, '.').replace(/[^a-z0-9.-]/g, '');
}


// ---

interface OlzEditUserOptions {
    showPassword: boolean;
    isPasswordRequired: boolean;
    isEmailRequired: boolean;
}

interface OlzEditUserModalProps {
    id?: number;
    meta?: OlzMetaData;
    data?: OlzUserData;
    options: OlzEditUserOptions;
}

export const OlzEditUserModal = (props: OlzEditUserModalProps): React.ReactElement => {
    const options = props.options;
    const {register, handleSubmit, formState: {errors}, control, setValue, watch} = useForm<OlzEditUserForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [isSubmitting, setIsSubmitting] = React.useState<boolean>(false);
    const [recaptchaConsentGiven, setRecaptchaConsentGiven] = React.useState<boolean>(false);
    const [isWaitingForCaptcha, setIsWaitingForCaptcha] = React.useState<boolean>(false);
    const [cookieConsentGiven, setCookieConsentGiven] = React.useState<boolean>(false);
    const [isImagesLoading, setIsImagesLoading] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const firstName = watch('firstName');
    const lastName = watch('lastName');
    const username = watch('username');

    React.useEffect(() => {
        if (!recaptchaConsentGiven) {
            return;
        }
        setIsWaitingForCaptcha(true);
        loadRecaptcha().then(() => {
            window.setTimeout(() => {
                setIsWaitingForCaptcha(false);
            }, 1100);
        });
    }, [recaptchaConsentGiven]);

    const onSubmit: SubmitHandler<OlzEditUserForm> = async (values) => {
        setIsSubmitting(true);
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);
        let recaptchaToken: string|null = null;
        if (!user.id) {
            if (!cookieConsentGiven) {
                setIsSubmitting(false);
                setSuccessMessage('');
                setErrorMessage('Die Einwilligung für Cookies beim Login ist notwendig!');
                return;
            }
            if (recaptchaConsentGiven) {
                recaptchaToken = await loadRecaptchaToken();
            }
        }

        const [err, response] = await (props.id
            ? olzApi.getResult('updateUser', {id: props.id, meta, data})
            : olzApi.getResult('createUser', {meta, data, custom: {recaptchaToken}}));
        if (err || response.status !== 'OK') {
            setIsSubmitting(false);
            setSuccessMessage('');
            setErrorMessage(`Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`);
            return;
        }

        setSuccessMessage('Änderung erfolgreich. Bitte warten...');
        setErrorMessage('');
        // This could probably be done more smoothly!
        window.location.reload();
    };

    const dialogTitle = (props.id === undefined
        ? 'OLZ-Konto erstellen'
        : 'OLZ-Konto bearbeiten'
    );
    const passwordAsterisk = options.isPasswordRequired ? <span className='required-field-asterisk'>*</span> : null;
    const emailAsterisk = options.isEmailRequired ? <span className='required-field-asterisk'>*</span> : null;
    const isLoading = isImagesLoading;

    return (
        <OlzEditModal
            modalId='edit-user-modal'
            dialogTitle={dialogTitle}
            successMessage={successMessage}
            errorMessage={errorMessage}
            isLoading={isLoading}
            isWaitingForCaptcha={isWaitingForCaptcha}
            isSubmitting={isSubmitting}
            onSubmit={handleSubmit(onSubmit)}
        >
            {(user.id && user.id !== props.id) ? (
                <div className='alert alert-danger' role='alert'>
                    <b>Änderungen dürfen nur mit der Einwilligung der betreffenden Person vorgenommen werden!</b>
                </div>
            ) : null}
            <div className='data-protection-section'>
                <p>
                    <b>Wir behandeln deine Daten vertraulich und verwenden sie sparsam</b>:
                    <a href='/datenschutz' className='linkint' target='_blank'>Datenschutz</a>
                </p>
                <p>
                    <span className='required-field-asterisk'>*</span>&nbsp;
                    Zwingend notwendige Felder sind mit einem roten Sternchen gekennzeichnet.
                </p>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title={<>Vorname <span className='required-field-asterisk'>*</span></>}
                        name='firstName'
                        errors={errors}
                        register={register}
                    />
                </div>
                <div className='col mb-3'>
                    <OlzTextField
                        title={<>Nachname <span className='required-field-asterisk'>*</span></>}
                        name='lastName'
                        errors={errors}
                        register={register}
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title={<>Benutzername <span className='required-field-asterisk'>*</span></>}
                        name='username'
                        errors={errors}
                        register={register}
                        autoComplete='off'
                        onFocus={() => {
                            if (username !== '' || !firstName || !lastName) {
                                return;
                            }
                            const usernameSuggestion = getUsernameSuggestion(firstName, lastName);
                            setValue('username', usernameSuggestion);
                        }}
                    />
                </div>
                <div className='col'></div>
            </div>
            {options.showPassword ? (
                <div className='row'>
                    <div className='col mb-3'>
                        <OlzTextField
                            mode='password-input'
                            title={<>Passwort {passwordAsterisk}</>}
                            name='password'
                            errors={errors}
                            register={register}
                            autoComplete='off'
                        />
                    </div>
                    <div className='col mb-3'>
                        <OlzTextField
                            mode='password-input'
                            title={<>Passwort wiederholen  {passwordAsterisk}</>}
                            name='passwordRepeat'
                            errors={errors}
                            register={register}
                            autoComplete='off'
                        />
                    </div>
                </div>
            ) : null}
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title={<>E-Mail  {emailAsterisk}</>}
                        name='email'
                        errors={errors}
                        register={register}
                    />
                </div>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Telefonnummer (Format: +41XXXXXXXXX)'
                        name='phone'
                        errors={errors}
                        register={register}
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <label htmlFor='gender-input'>Geschlecht</label>
                    <select
                        className='form-control form-select'
                        id='gender-input'
                        {...register('gender')}
                    >
                        <option value=''>unbekannt</option>
                        <option value='M'>männlich</option>
                        <option value='F'>weiblich</option>
                        <option value='O'>andere</option>
                    </select>
                </div>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Geburtsdatum (Format: TT.MM.YYYY)'
                        name='birthdate'
                        errors={errors}
                        register={register}
                    />
                </div>
            </div>
            <div className='mb-3'>
                <OlzTextField
                    title='Adresse (mit Hausnummer)'
                    name='street'
                    errors={errors}
                    register={register}
                />
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title='PLZ'
                        name='postalCode'
                        errors={errors}
                        register={register}
                    />
                </div>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Wohnort'
                        name='city'
                        errors={errors}
                        register={register}
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Region / Kanton (2-Buchstaben-Code, z.B. ZH)'
                        name='region'
                        errors={errors}
                        register={register}
                    />
                </div>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Land (2-Buchstaben-Code, z.B. CH)'
                        name='countryCode'
                        errors={errors}
                        register={register}
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title='SI-Card-Nummer (Badge-Nummer)'
                        name='siCardNumber'
                        errors={errors}
                        register={register}
                    />
                </div>
                <div className='col mb-3'>
                    <OlzTextField
                        title={<>SOLV-Nummer (siehe <a href='https://www.o-l.ch/cgi-bin/solvdb' target='_blank'>SOLV-DB</a>)</>}
                        name='solvNumber'
                        errors={errors}
                        register={register}
                    />
                </div>
            </div>
            <div id='images-upload'>
                <OlzImageField
                    title='Profilbild'
                    name='avatarImageId'
                    errors={errors}
                    control={control}
                    setIsLoading={setIsImagesLoading}
                />
            </div>
            {user.id ? null : (<>
                <p>
                    <input
                        type='checkbox'
                        name='recaptcha-consent-given'
                        value='yes'
                        checked={recaptchaConsentGiven}
                        onChange={(e) => setRecaptchaConsentGiven(e.target.checked)}
                        id='recaptcha-consent-given-input'
                    />
                    &nbsp;
                    <span className='required-field-asterisk'>*</span>&nbsp;
                    Ich akzeptiere, dass beim Erstellen des Kontos einmalig Google reCaptcha verwendet wird, um Bot-Spam zu verhinden.
                </p>
                <p>
                    <input
                        type='checkbox'
                        name='cookie-consent-given'
                        value='yes'
                        checked={cookieConsentGiven}
                        onChange={(e) => setCookieConsentGiven(e.target.checked)}
                        id='cookie-consent-given-input'
                    />
                    &nbsp;
                    <span className='required-field-asterisk'>*</span>&nbsp;
                    Ich nehme zur Kenntnis, dass bei jedem Login notgedrungen ein Cookie in meinem Browser gesetzt wird.
                    &nbsp;
                    <a
                        href={`${codeHref}datenschutz`}
                        target='_blank'
                        className='linkint'
                    >
                        Weitere Informationen zum Datenschutz
                    </a>
                </p>
            </>)}
        </OlzEditModal>
    );
};

export function initOlzEditUserModal(
    options: OlzEditUserOptions,
    id?: number,
    meta?: OlzMetaData,
    data?: OlzUserData,
): boolean {
    return initOlzEditModal('edit-user-modal', () => (
        <OlzEditUserModal
            id={id}
            meta={meta}
            data={data}
            options={options}
        />
    ));
}
