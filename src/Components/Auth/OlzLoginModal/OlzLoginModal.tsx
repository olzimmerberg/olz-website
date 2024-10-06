import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzApiRequests} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {initOlzEditUserModal} from '../../../Users/Components/OlzEditUserModal/OlzEditUserModal';
import {user} from '../../../Utils/constants';
import {getApiBoolean, getApiString, getResolverResult, validateNotEmpty} from '../../../Utils/formUtils';
import {initOlzResetPasswordModal} from '../OlzResetPasswordModal/OlzResetPasswordModal';

import './OlzLoginModal.scss';

interface OlzLoginForm {
    usernameOrEmail: string;
    password: string;
    rememberMe: string|boolean;
}

const resolver: Resolver<OlzLoginForm> = async (values) => {
    const errors: FieldErrors<OlzLoginForm> = {};
    errors.usernameOrEmail = validateNotEmpty(values.usernameOrEmail);
    // Do not validate password here. Could be legacy or test password.
    errors.password = validateNotEmpty(values.password);
    return getResolverResult(errors, values);
};

function getApiFromForm(formData: OlzLoginForm): OlzApiRequests['login'] {
    return {
        usernameOrEmail: getApiString(formData.usernameOrEmail) ?? '',
        password: getApiString(formData.password) ?? '',
        rememberMe: getApiBoolean(formData.rememberMe) ?? 0,
    };
}

// ---

interface OlzLoginModalProps {
    autoSubmitAutoFilled?: boolean;
}

export const OlzLoginModal = (props: OlzLoginModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, setValue} = useForm<OlzLoginForm>({
        resolver,
        defaultValues: {
            usernameOrEmail: '',
            password: '',
            rememberMe: false,
        },
    });

    const [isSubmitting, setIsSubmitting] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const onSubmit: SubmitHandler<OlzLoginForm> = async (values) => {
        setIsSubmitting(true);
        const data = getApiFromForm(values);

        const [err, response] = await olzApi.getResult('login', data);
        if (response?.status === 'INVALID_CREDENTIALS') {
            const attempts = response.numRemainingAttempts;
            setSuccessMessage('');
            setErrorMessage(`Falsche Login-Daten. Verbleibende Versuche: ${attempts}.`);
            setIsSubmitting(false);
            return;
        } else if (response?.status === 'BLOCKED') {
            setSuccessMessage('');
            setErrorMessage('Zu viele erfolglose Login-Versuche. Du bist vorübergehend gesperrt.');
            setIsSubmitting(false);
            return;
        } else if (response?.status !== 'AUTHENTICATED') {
            setSuccessMessage('');
            setErrorMessage(`Fehler: ${err?.message} (Antwort: ${response?.status}).`);
            setIsSubmitting(false);
            return;
        }
        if (data.rememberMe) {
            localStorage.setItem('OLZ_AUTO_LOGIN', data.usernameOrEmail);
        } else {
            localStorage.removeItem('OLZ_AUTO_LOGIN');
        }
        setSuccessMessage('Login erfolgreich. Bitte warten...');
        setErrorMessage('');
        // This could probably be done more smoothly!
        window.location.href = '#';
        window.location.reload();
    };

    React.useEffect(() => {
        if (props.autoSubmitAutoFilled) {
            const usernameOrEmail = localStorage.getItem('OLZ_AUTO_LOGIN');
            setValue('rememberMe', 'yes');
            setValue('usernameOrEmail', usernameOrEmail ?? '');
            const timeoutId = setTimeout(() => {
                // Necessary, because react-hook-form's `watch` does not work.
                const passwordElem = document.getElementById('password-input');
                const passwordValue = (passwordElem as HTMLInputElement).value;
                if (passwordValue) {
                    onSubmit({
                        usernameOrEmail: usernameOrEmail ?? '',
                        password: passwordValue,
                        rememberMe: 'yes',
                    });
                }
            }, 100);
            return () => {
                clearTimeout(timeoutId);
            };
        }
        return () => undefined;
    }, [props.autoSubmitAutoFilled]);

    const dialogTitle = 'Login';
    const isLoading = false;

    return (
        <OlzEditModal
            modalId='login-modal'
            dialogTitle={dialogTitle}
            successMessage={successMessage}
            errorMessage={errorMessage}
            isLoading={isLoading}
            isSubmitting={isSubmitting}
            submitLabel='Login'
            onSubmit={handleSubmit(onSubmit)}
        >
            <div className='mb-3'>
                <OlzTextField
                    title='Benutzername oder E-Mail'
                    name='usernameOrEmail'
                    errors={errors}
                    register={register}
                    autoComplete='username'
                />
            </div>
            <div className='mb-3'>
                <OlzTextField
                    mode='password-input'
                    title='Passwort'
                    name='password'
                    errors={errors}
                    register={register}
                    autoComplete='current-password'
                />
            </div>
            <div className='mb-3 rememberMe-row'>
                <input
                    type='checkbox'
                    value='yes'
                    {...register('rememberMe')}
                    id='rememberMe-input'
                />
                <label htmlFor='rememberMe-input'>
                    Eingeloggt bleiben
                </label>
            </div>
            <div className='mb-3'>
                <a
                    id='reset-password-link'
                    href='#'
                    data-bs-dismiss='modal'
                    onClick={() => initOlzResetPasswordModal()}
                >
                    Passwort vergessen?
                </a>
            </div>
            <div className='mb-3'>
                <a
                    id='sign-up-link'
                    href='#'
                    data-bs-dismiss='modal'
                    onClick={() => openSignUpModal()}
                >
                    Noch kein OLZ-Konto?
                </a>
            </div>
        </OlzEditModal>
    );
};

function openSignUpModal() {
    const options = {
        showPassword: true,
        isPasswordRequired: true,
        isEmailRequired: true,
    };
    initOlzEditUserModal(options);
}

export function initOlzLoginModal(props: OlzLoginModalProps): boolean {
    return initOlzEditModal('login-modal', () => (
        <OlzLoginModal {...props}/>
    ), (modal) => {
        modal.addEventListener('shown.bs.modal', () => {
            document.getElementById('usernameOrEmail-input')?.focus();
            window.location.href = '#login-dialog';
        });
        modal.addEventListener('hidden.bs.modal', () => {
            window.location.href = '#';
            localStorage.removeItem('OLZ_AUTO_LOGIN');
        });
    });
}

window.addEventListener('load', () => {
    const usernameOrEmail = localStorage.getItem('OLZ_AUTO_LOGIN');
    if (!user?.username && usernameOrEmail) {
        initOlzLoginModal({autoSubmitAutoFilled: true});
    }

    const openLoginDialogIfHash = () => {
        if (
            window.location.hash === '#login-dialog'
            && document.getElementById('login-modal')?.style.display !== 'block'
        ) {
            initOlzLoginModal({});
        }
    };
    window.addEventListener('hashchange', openLoginDialogIfHash);
    openLoginDialogIfHash();
});
