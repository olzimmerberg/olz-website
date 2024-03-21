import * as bootstrap from 'bootstrap';
import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzApiRequests} from '../../../Api/client/generated_olz_api_types';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {codeHref} from '../../../Utils/constants';
import {getApiString, getResolverResult, validateNotEmpty} from '../../../Utils/formUtils';
import {initReact} from '../../../Utils/reactUtils';
import {loadRecaptchaToken, loadRecaptcha} from '../../../Utils/recaptchaUtils';

import './OlzResetPasswordModal.scss';

interface OlzResetPasswordForm {
    usernameOrEmail: string;
}

const resolver: Resolver<OlzResetPasswordForm> = async (values) => {
    const errors: FieldErrors<OlzResetPasswordForm> = {};
    errors.usernameOrEmail = validateNotEmpty(values.usernameOrEmail);
    return getResolverResult(errors, values);
};

function getApiFromForm(formData: OlzResetPasswordForm): OlzApiRequests['resetPassword'] {
    return {
        usernameOrEmail: getApiString(formData.usernameOrEmail) ?? '',
        recaptchaToken: '',
    };
}

// ---

export const OlzResetPasswordModal = (): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}} = useForm<OlzResetPasswordForm>({
        resolver,
        defaultValues: {
            usernameOrEmail: '',
        },
    });

    const [recaptchaConsentGiven, setRecaptchaConsentGiven] = React.useState<boolean>(false);
    const [isWaitingForCaptcha, setIsWaitingForCaptcha] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

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

    const onSubmit: SubmitHandler<OlzResetPasswordForm> = async (values) => {
        const data = getApiFromForm(values);
        const recaptchaToken = await loadRecaptchaToken();

        const [err, response] = await olzApi.getResult('resetPassword', {...data, recaptchaToken});
        if (response?.status === 'DENIED') {
            setSuccessMessage('');
            setErrorMessage('Der reCaptcha-Token wurde abgelehnt.');
            return;
        } else if (response?.status !== 'OK') {
            setSuccessMessage('');
            setErrorMessage(`Fehler: ${err?.message} (Antwort: ${response?.status}).`);
            return;
        }
        setSuccessMessage('E-Mail versendet. Bitte warten...');
        setErrorMessage('');
        // TODO: This could probably be done more smoothly!
        window.location.reload();
    };

    return (
        <div
            className='modal fade'
            id='reset-password-modal'
            tabIndex={-1}
            aria-labelledby='reset-password-modal-label'
            aria-hidden='true'
        >
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form
                        className='default-form'
                        onSubmit={handleSubmit(onSubmit)}
                    >
                        <div className='modal-header'>
                            <h5 className='modal-title' id='reset-password-modal-label'>
                                Passwort zurücksetzen
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
                            <div className='mb-3 instructions'>
                                Wir schicken dir ein E-Mail mit dem Betreff "[OLZ] Passwort zurücksetzen". Es enthält ein Passwort und einen Link, mit dem du dieses dann als dein neues Passwort setzen kannst.
                            </div>
                            <div className='mb-3'>
                                <OlzTextField
                                    title={<>
                                        Benutzername oder E-Mail
                                        <a
                                            href={`${codeHref}fragen_und_antworten#benutzername-email-herausfinden`}
                                            className='help-link'
                                        >
                                            Vergessen?
                                        </a>
                                    </>}
                                    name='usernameOrEmail'
                                    errors={errors}
                                    register={register}
                                />
                            </div>
                            <div className='mb-3'>
                                <input
                                    type='checkbox'
                                    name='recaptcha-consent-given'
                                    value='yes'
                                    checked={recaptchaConsentGiven}
                                    onChange={(e) => setRecaptchaConsentGiven(e.target.checked)}
                                    id='recaptcha-consent-given-input'
                                />
                                Ich akzeptiere, dass beim Zurücksetzen des Passworts einmalig Google reCaptcha verwendet wird, um Bot-Spam zu verhinden.
                                &nbsp;
                                <a
                                    href='{$code_href}datenschutz'
                                    target='_blank'
                                >
                                    Weitere Informationen zum Datenschutz
                                </a>
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
                                className={isWaitingForCaptcha ? 'btn btn-secondary' : 'btn btn-primary'}
                                id='submit-button'
                            >
                                {isWaitingForCaptcha ? 'Bitte warten...' : 'E-Mail senden'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
};

export function initOlzResetPasswordModal(): boolean {
    initReact('dialog-react-root', (
        <OlzResetPasswordModal/>
    ));
    window.setTimeout(() => {
        const modal = document.getElementById('reset-password-modal');
        if (!modal) {
            return;
        }
        new bootstrap.Modal(modal, {backdrop: 'static'}).show();

        modal.addEventListener('shown.bs.modal', () => {
            document.getElementById('usernameOrEmail-input')?.focus();
        });
    }, 1);
    return false;
}
