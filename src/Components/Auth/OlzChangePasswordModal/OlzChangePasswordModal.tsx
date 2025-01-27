import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzApiRequests} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal, OlzEditModalStatus} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {user} from '../../../Utils/constants';
import {getApiString, getResolverResult, validateNotEmpty, validatePassword} from '../../../Utils/formUtils';
import {assert} from '../../../Utils/generalUtils';

import './OlzChangePasswordModal.scss';

interface OlzChangePasswordForm {
    oldPassword: string;
    newPassword: string;
    newPasswordRepeat: string;
}

const resolver: Resolver<OlzChangePasswordForm> = async (values) => {
    const errors: FieldErrors<OlzChangePasswordForm> = {};
    // Do not validate password here. Could be legacy or test password.
    errors.oldPassword = validateNotEmpty(values.oldPassword);
    errors.newPassword = validatePassword(values.newPassword);
    errors.newPasswordRepeat = values.newPassword !== values.newPasswordRepeat
        ? {type: 'validate', message: 'Das Passwort und die Wiederholung müssen übereinstimmen!'}
        : undefined;
    return getResolverResult(errors, values);
};

function getApiFromForm(formData: OlzChangePasswordForm): OlzApiRequests['updatePassword'] {
    return {
        id: assert(user.id),
        oldPassword: getApiString(formData.oldPassword) ?? '',
        newPassword: getApiString(formData.newPassword) ?? '',
    };
}

// ---

export const OlzChangePasswordModal = (): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}} = useForm<OlzChangePasswordForm>({
        resolver,
        defaultValues: {
            oldPassword: '',
            newPassword: '',
            newPasswordRepeat: '',
        },
    });

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});

    const onSubmit: SubmitHandler<OlzChangePasswordForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const data = getApiFromForm(values);

        const [err, response] = await olzApi.getResult('updatePassword', data);
        if (response?.status === 'INVALID_OLD') {
            setStatus({id: 'SUBMIT_FAILED', message: 'Das bisherige Passwort wurde nicht korrekt eingegeben.'});
            return;
        } else if (response?.status === 'OTHER_USER') {
            setStatus({id: 'SUBMIT_FAILED', message: 'Jeder Benutzer kann nur sein eigenes Passwort ändern.'});
            return;
        } else if (response?.status !== 'OK') {
            setStatus({id: 'SUBMIT_FAILED', message: `Fehler: ${err?.message} (Antwort: ${response?.status}).`});
            return;
        }
        setStatus({id: 'SUBMITTED', message: 'Passwort erfolgreich aktualisiert. Bitte warten...'});
        // This could probably be done more smoothly!
        window.location.href = '#';
        window.location.reload();
    };

    const dialogTitle = 'Passwort ändern';

    return (
        <OlzEditModal
            modalId='change-password-modal'
            dialogTitle={dialogTitle}
            status={status}
            submitLabel='Passwort ändern'
            onSubmit={handleSubmit(onSubmit)}
        >
            <div className='mb-3'>
                <label htmlFor='username-input'>Benutzername</label>
                <input
                    type='text'
                    name='username'
                    value={user.username}
                    id='username-input'
                    autoComplete='username'
                    disabled
                    className='form-control'
                />
            </div>
            <div className='mb-3'>
                <OlzTextField
                    mode='password-input'
                    title='Bisheriges Passwort'
                    name='oldPassword'
                    errors={errors}
                    register={register}
                    autoComplete='current-password'
                />
            </div>
            <div className='mb-3'>
                <OlzTextField
                    mode='password-input'
                    title='Neues Passwort'
                    name='newPassword'
                    errors={errors}
                    register={register}
                />
            </div>
            <div className='mb-3'>
                <OlzTextField
                    mode='password-input'
                    title='Neues Passwort wiederholen'
                    name='newPasswordRepeat'
                    errors={errors}
                    register={register}
                />
            </div>
        </OlzEditModal>
    );
};

export function initOlzChangePasswordModal(): boolean {
    return initOlzEditModal('change-password-modal', () => (
        <OlzChangePasswordModal/>
    ), (modal) => {
        modal.addEventListener('shown.bs.modal', () => {
            document.getElementById('oldPassword-input')?.focus();
        });
    });
}
