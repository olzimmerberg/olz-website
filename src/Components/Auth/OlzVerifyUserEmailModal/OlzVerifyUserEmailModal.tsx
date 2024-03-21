import * as bootstrap from 'bootstrap';
import React from 'react';
import {olzApi} from '../../../Api/client';
import {codeHref} from '../../../Utils/constants';
import {initReact} from '../../../Utils/reactUtils';
import {loadRecaptchaToken, loadRecaptcha} from '../../../Utils/recaptchaUtils';

import './OlzVerifyUserEmailModal.scss';

// ---

export const OlzVerifyUserEmailModal = (): React.ReactElement => {
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

    const onSubmit = async (): Promise<void> => {
        const recaptchaToken = await loadRecaptchaToken();

        const [err, response] = await olzApi.getResult('verifyUserEmail', {recaptchaToken});
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
        // This removes Google's injected reCaptcha script again
        window.location.href = `${codeHref}profil`;
    };

    return (
        <div
            className='modal fade'
            id='verify-user-email-modal'
            tabIndex={-1}
            aria-labelledby='verify-user-email-modal-label'
            aria-hidden='true'
        >
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <div className='modal-header'>
                        <h5 className='modal-title' id='verify-user-email-modal-label'>
                            E-Mail-Adresse bestätigen
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
                            Wir schicken dir ein E-Mail mit dem Betreff "[OLZ] E-Mail bestätigen".
                            Es enthält einen Link, mit dem du dann deine E-Mail-Adresse bestätigen kannst.
                        </div>
                        <div className='mb-3'>
                            <input
                                type='checkbox'
                                name='recaptcha-consent-given-input'
                                value='yes'
                                checked={recaptchaConsentGiven}
                                onChange={(e) => setRecaptchaConsentGiven(e.target.checked)}
                                id='recaptcha-consent-given-input'
                            />
                            Ich akzeptiere, dass beim Zurücksetzen des Passworts einmalig Google reCaptcha verwendet wird, um Bot-Spam zu verhinden.
                            &nbsp;
                            <a
                                href={`${codeHref}datenschutz`}
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
                            type='button'
                            onClick={onSubmit}
                            className={isWaitingForCaptcha ? 'btn btn-secondary' : 'btn btn-primary'}
                            id='submit-button'
                        >
                            {isWaitingForCaptcha ? 'Bitte warten...' : 'E-Mail senden'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export function initOlzVerifyUserEmailModal(): boolean {
    initReact('dialog-react-root', (
        <OlzVerifyUserEmailModal/>
    ));
    window.setTimeout(() => {
        const modal = document.getElementById('verify-user-email-modal');
        if (!modal) {
            return;
        }
        new bootstrap.Modal(modal, {backdrop: 'static'}).show();
    }, 1);
    return false;
}
