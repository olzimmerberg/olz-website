import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi, OlzApiRequests} from '../../../Api/client';
import {initOlzEditModal, OlzEditModal, OlzEditModalStatus} from '../../../Common/Components/OlzEditModal/OlzEditModal';
import {initOlzEditUserModal} from '../../../Users/Components/OlzEditUserModal/OlzEditUserModal';
import {user} from '../../../Utils/constants';
import {getApiString, getResolverResult, validateNotEmpty} from '../../../Utils/formUtils';
import {initOlzResetPasswordModal} from '../OlzResetPasswordModal/OlzResetPasswordModal';

import './OlzLoginModal.scss';

interface OlzLoginForm {
    usernameOrEmail: string;
    password: string;
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
    };
}

// ---

interface OlzLoginModalProps {
    onSubmit?: () => void;
}

export const OlzLoginModal = (props: OlzLoginModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, setValue} = useForm<OlzLoginForm>({
        resolver,
        defaultValues: {
            usernameOrEmail: '',
            password: '',
        },
    });

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});

    const onSubmit: SubmitHandler<OlzLoginForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const data = getApiFromForm(values);

        const [err, response] = await olzApi.getResult('login', data);
        if (response?.status === 'INVALID_CREDENTIALS') {
            const attempts = response.numRemainingAttempts;
            setStatus({id: 'SUBMIT_FAILED', message: `Falsche Login-Daten. Verbleibende Versuche: ${attempts}.`});
            return;
        } else if (response?.status === 'BLOCKED') {
            setStatus({id: 'SUBMIT_FAILED', message: 'Zu viele erfolglose Login-Versuche. Du bist vorübergehend gesperrt.'});
            return;
        } else if (response?.status !== 'AUTHENTICATED') {
            setStatus({id: 'SUBMIT_FAILED', message: `Fehler: ${err?.message} (Antwort: ${response?.status}).`});
            return;
        }
        localStorage.setItem('OLZ_AUTO_LOGIN', data.usernameOrEmail);
        setStatus({id: 'SUBMITTED', message: 'Login erfolgreich. Bitte warten...'});
        // This could probably be done more smoothly!
        props.onSubmit?.();
    };

    React.useEffect(() => {
        const usernameOrEmail = localStorage.getItem('OLZ_AUTO_LOGIN');
        if (usernameOrEmail) {
            setValue('usernameOrEmail', usernameOrEmail);
        }
    }, [setValue]);

    const dialogTitle = 'Login';

    const usernameErrorMessage = errors.usernameOrEmail?.message;
    const usernameErrorClassName = usernameErrorMessage ? ' is-invalid' : '';
    const usernameErrorComponent = usernameErrorMessage && <p className='error'>{String(usernameErrorMessage)}</p>;

    const passwordErrorMessage = errors.password?.message;
    const passwordErrorClassName = passwordErrorMessage ? ' is-invalid' : '';
    const passwordErrorComponent = passwordErrorMessage && <p className='error'>{String(passwordErrorMessage)}</p>;

    return (
        <OlzEditModal
            modalId='login-modal'
            dialogTitle={dialogTitle}
            status={status}
            submitLabel='Login'
            onSubmit={handleSubmit(onSubmit)}
        >
            <div className='mb-3'>
                <label htmlFor='username'>Benutzername oder E-Mail</label>
                <input
                    type='text'
                    {...register('usernameOrEmail')}
                    className={`form-control${usernameErrorClassName}`}
                    id='username'
                    autoComplete='username'
                />
                {usernameErrorComponent}
            </div>
            <div className='mb-3'>
                <label htmlFor='current-password'>
                    Passwort
                    <a
                        id='reset-password-link'
                        href='#'
                        data-bs-dismiss='modal'
                        onClick={() => initOlzResetPasswordModal()}
                    >
                        Vergessen?
                    </a>
                </label>
                <input
                    type='password'
                    {...register('password')}
                    className={`form-control${passwordErrorClassName}`}
                    id='current-password'
                    autoComplete='current-password'
                />
                {passwordErrorComponent}
            </div>
            <div className='sign-up-container'>
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
        simplified: false,
    };
    initOlzEditUserModal(options);
}

export function loginAndReload(props: OlzLoginModalProps): Promise<void> {
    return login(props).then(() => {
        maybeRemoveLoginModalHash();
        window.location.reload();
    });
}

export function login(props: OlzLoginModalProps): Promise<void> {
    return new Promise((resolve, reject) => {
        let isResolved = false;
        let hideFn: (() => void) | null = null;
        initOlzEditModal('login-modal', () => (
            <OlzLoginModal {...props} onSubmit={() => {
                isResolved = true;
                resolve();
                maybeRemoveLoginModalHash();
                hideFn?.();
            }}/>
        ), (modalElem, modal) => {
            hideFn = () => modal.hide();
            modalElem.addEventListener('shown.bs.modal', () => {
                document.getElementById('usernameOrEmail-input')?.focus();
                window.location.href = '#login-dialog';
            });
            modalElem.addEventListener('hidden.bs.modal', () => {
                maybeRemoveLoginModalHash();
                if (!isResolved) {
                    reject(new Error('Login abgebrochen'));
                }
            });
        });
    });
}

function maybeRemoveLoginModalHash() {
    if (window.location.hash === '#login-dialog') {
        history.pushState(null, document.title, ' ');
    }
}

window.addEventListener('load', () => {
    const usernameOrEmail = localStorage.getItem('OLZ_AUTO_LOGIN');
    if (!user?.username && usernameOrEmail) {
        loginAndReload({});
    }

    const openLoginDialogIfHash = () => {
        if (
            window.location.hash === '#login-dialog'
            && document.getElementById('login-modal')?.style.display !== 'block'
        ) {
            loginAndReload({});
        }
    };
    window.addEventListener('hashchange', openLoginDialogIfHash);
    openLoginDialogIfHash();
});
