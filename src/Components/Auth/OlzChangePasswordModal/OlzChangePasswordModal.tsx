import * as bootstrap from 'bootstrap';
import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzApiRequests} from '../../../Api/client/generated_olz_api_types';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {user} from '../../../Utils/constants';
import {getApiString, getResolverResult, validateNotEmpty, validatePassword} from '../../../Utils/formUtils';
import {assert} from '../../../Utils/generalUtils';
import {initReact} from '../../../Utils/reactUtils';

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

    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const onSubmit: SubmitHandler<OlzChangePasswordForm> = async (values) => {
        const data = getApiFromForm(values);

        const [err, response] = await olzApi.getResult('updatePassword', data);
        if (response?.status === 'INVALID_OLD') {
            setSuccessMessage('');
            setErrorMessage('Das bisherige Passwort wurde nicht korrekt eingegeben.');
            return;
        } else if (response?.status === 'OTHER_USER') {
            setSuccessMessage('');
            setErrorMessage('Jeder Benutzer kann nur sein eigenes Passwort ändern.');
            return;
        } else if (response?.status !== 'OK') {
            setSuccessMessage('');
            setErrorMessage(`Fehler: ${err?.message} (Antwort: ${response?.status}).`);
            return;
        }
        setSuccessMessage('Passwort erfolgreich aktualisiert. Bitte warten...');
        setErrorMessage('');
        // TODO: This could probably be done more smoothly!
        window.location.href = '#';
        window.location.reload();
    };

    return (
        <div
            className='modal fade'
            id='change-password-modal'
            tabIndex={-1}
            aria-labelledby='change-password-modal-label'
            aria-hidden='true'
        >
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form
                        className='default-form'
                        onSubmit={handleSubmit(onSubmit)}
                    >
                        <div className='modal-header'>
                            <h5 className='modal-title' id='change-password-modal-label'>
                                Passwort ändern
                            </h5>
                            <button
                                type='button'
                                className='btn-close'
                                data-bs-dismiss='modal'
                                aria-label='Schliessen'
                            >
                            </button>
                        </div>
                        <div className='modal-body'>
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
                            <div className='success-message alert alert-success' role='alert'>
                                {successMessage}
                            </div>
                            <div className='error-message alert alert-danger' role='alert'>
                                {errorMessage}
                            </div>
                        </div>
                        <div className='modal-footer'>
                            <button
                                type='button'
                                className='btn btn-secondary'
                                data-bs-dismiss='modal'
                            >
                                Abbrechen
                            </button>
                            <button
                                type='submit'
                                className='btn btn-primary'
                                id='submit-button'
                            >
                                Passwort ändern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
};

export function initOlzChangePasswordModal(): boolean {
    initReact('dialog-react-root', (
        <OlzChangePasswordModal/>
    ));
    window.setTimeout(() => {
        const modal = document.getElementById('change-password-modal');
        if (!modal) {
            return;
        }
        new bootstrap.Modal(modal, {backdrop: 'static'}).show();

        modal.addEventListener('shown.bs.modal', () => {
            document.getElementById('oldPassword-input')?.focus();
        });
    }, 1);
    return false;
}
