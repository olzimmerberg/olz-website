import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzUserData} from '../../../Api/client/generated_olz_api_types';
import {OlzCaptcha} from '../../../Captcha/Components/OlzCaptcha/OlzCaptcha';
import {initOlzEditModal, OlzEditModal, OlzEditModalStatus} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzImageField} from '../../../Components/Upload/OlzImageField/OlzImageField';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {user} from '../../../Utils/constants';
import {getApiNumber, getApiString, getFormNumber, getFormString, getResolverResult, validateAhvOrNull, validateCountryCodeOrNull, validateDateOrNull, validateEmail, validateEmailOrNull, validateGender, validateIntegerOrNull, validateNotEmpty, validatePassword, validatePhoneOrNull, validateStringRegex} from '../../../Utils/formUtils';
import {assert} from '../../../Utils/generalUtils';

import './OlzEditUserModal.scss';

interface OlzEditUserForm {
    parentUserId: number | null;
    firstName: string;
    lastName: string;
    username: string;
    password: string | null;
    passwordRepeat: string | null;
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
    ahvNumber: string;
    dressSize: string;
    avatarImageId: string | null;
}

const resolver: Resolver<OlzEditUserForm> = async (values) => {
    const errors: FieldErrors<OlzEditUserForm> = {};
    errors.firstName = validateNotEmpty(values.firstName);
    errors.lastName = validateNotEmpty(values.lastName);
    errors.username = validateStringRegex(values.username, /^[a-z0-9\-.]+$/,
        'Benutzername darf nur Kleinbuchstaben, Zahlen, "-" und "." enthalten, darf nicht leer sein.');
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
    [errors.ahvNumber, values.ahvNumber] = validateAhvOrNull(values.ahvNumber);

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
        ahvNumber: getFormString(apiData?.ahvNumber),
        dressSize: getFormString(apiData?.dressSize),
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
        ahvNumber: getApiString(formData.ahvNumber),
        dressSize: getApiString(formData.dressSize),
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

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});
    const [captchaToken, setCaptchaToken] = React.useState<string | null>(null);
    const [isImagesLoading, setIsImagesLoading] = React.useState<boolean>(false);

    const firstName = watch('firstName');
    const lastName = watch('lastName');
    const username = watch('username');

    const onSubmit: SubmitHandler<OlzEditUserForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);
        if (!user.id && !captchaToken) {
            setStatus({id: 'SUBMIT_FAILED', message: 'Die Einwilligung für Cookies beim Login ist notwendig!'});
            return;
        }

        const [err, response] = await (props.id
            ? olzApi.getResult('updateUser', {id: props.id, meta, data})
            : olzApi.getResult('createUser', {meta, data, custom: {captchaToken}}));
        if (err || response.custom?.status !== 'OK') {
            setStatus({id: 'SUBMIT_FAILED', message: `Anfrage fehlgeschlagen: ${JSON.stringify(err || response.custom?.status)}`});
            return;
        }
        setStatus({id: 'SUBMITTED'});
        // This could probably be done more smoothly!
        window.location.reload();
    };

    const onDelete = props.id ? async () => {
        setStatus({id: 'DELETING'});
        const [err, response] = await olzApi.getResult('deleteUser', {id: assert(props.id)});
        if (err) {
            setStatus({id: 'DELETE_FAILED', message: `Löschen fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'DELETED'});
        // This could probably be done more smoothly!
        window.location.reload();
    } : undefined;

    const dialogTitle = (props.id === undefined
        ? 'OLZ-Konto erstellen'
        : 'OLZ-Konto bearbeiten'
    );
    const passwordAsterisk = options.isPasswordRequired ? <span className='required-field-asterisk'>*</span> : null;
    const emailAsterisk = options.isEmailRequired ? <span className='required-field-asterisk'>*</span> : null;
    const editModalStatus: OlzEditModalStatus = isImagesLoading ? {id: 'LOADING'} : status;

    return (
        <OlzEditModal
            modalId='edit-user-modal'
            dialogTitle={dialogTitle}
            status={editModalStatus}
            onSubmit={handleSubmit(onSubmit)}
            onDelete={onDelete}
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
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title='AHV-Nummer'
                        name='ahvNumber'
                        errors={errors}
                        register={register}
                    />
                </div>
                <div className='col mb-3'>
                    <label htmlFor='dressSize-input'>Kleidergrösse</label>
                    <select
                        className='form-control form-select'
                        id='dressSize-input'
                        {...register('dressSize')}
                    >
                        <option value=''>unbekannt</option>
                        <option value='3XS'>3XS</option>
                        <option value='XXS'>XXS</option>
                        <option value='XS'>XS</option>
                        <option value='S'>S</option>
                        <option value='M'>M</option>
                        <option value='L'>L</option>
                        <option value='XL'>XL</option>
                        <option value='XXL'>XXL</option>
                        <option value='3XL'>3XL</option>
                    </select>
                </div>
            </div>
            <div id='images-upload' className='mb-3'>
                <OlzImageField
                    title='Profilbild'
                    name='avatarImageId'
                    errors={errors}
                    control={control}
                    setIsLoading={setIsImagesLoading}
                />
            </div>
            {user.id ? null : (<>
                <OlzCaptcha onToken={setCaptchaToken}/>
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
