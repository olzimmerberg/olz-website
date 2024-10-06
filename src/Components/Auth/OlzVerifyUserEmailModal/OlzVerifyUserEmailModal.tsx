import React from 'react';
import {olzApi} from '../../../Api/client';
import {initOlzEditModal, OlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {codeHref} from '../../../Utils/constants';
import {loadRecaptchaToken, loadRecaptcha} from '../../../Utils/recaptchaUtils';

import './OlzVerifyUserEmailModal.scss';

// ---

export const OlzVerifyUserEmailModal = (): React.ReactElement => {
    const [isSubmitting, setIsSubmitting] = React.useState<boolean>(false);
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

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>): Promise<void> => {
        e.preventDefault();
        setIsSubmitting(true);
        const recaptchaToken = await loadRecaptchaToken();

        const [err, response] = await olzApi.getResult('verifyUserEmail', {recaptchaToken});
        if (response?.status === 'DENIED') {
            setSuccessMessage('');
            setErrorMessage('Der reCaptcha-Token wurde abgelehnt.');
            setIsSubmitting(false);
            return;
        } else if (response?.status !== 'OK') {
            setSuccessMessage('');
            setErrorMessage(`Fehler: ${err?.message} (Antwort: ${response?.status}).`);
            setIsSubmitting(false);
            return;
        }
        setSuccessMessage('E-Mail versendet. Bitte warten...');
        setErrorMessage('');
        // This removes Google's injected reCaptcha script again
        window.location.href = `${codeHref}benutzer/ich`;
    };

    const dialogTitle = 'E-Mail-Adresse bestätigen';

    return (
        <OlzEditModal
            modalId='verify-user-email-modal'
            dialogTitle={dialogTitle}
            successMessage={successMessage}
            errorMessage={errorMessage}
            isWaitingForCaptcha={isWaitingForCaptcha}
            isSubmitting={isSubmitting}
            submitLabel='E-Mail senden'
            onSubmit={handleSubmit}
        >
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
        </OlzEditModal>
    );
};

export function initOlzVerifyUserEmailModal(): boolean {
    return initOlzEditModal('verify-user-email-modal', () => (
        <OlzVerifyUserEmailModal/>
    ));
}
