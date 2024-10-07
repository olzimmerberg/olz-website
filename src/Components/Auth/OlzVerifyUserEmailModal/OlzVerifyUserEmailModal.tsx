import React from 'react';
import {olzApi} from '../../../Api/client';
import {initOlzEditModal, OlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {codeHref} from '../../../Utils/constants';

import './OlzVerifyUserEmailModal.scss';

// ---

export const OlzVerifyUserEmailModal = (): React.ReactElement => {
    const [isSubmitting, setIsSubmitting] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>): Promise<void> => {
        e.preventDefault();
        setIsSubmitting(true);

        const [err, response] = await olzApi.getResult('verifyUserEmail', {});
        if (response?.status !== 'OK') {
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
            isSubmitting={isSubmitting}
            submitLabel='E-Mail senden'
            onSubmit={handleSubmit}
        >
            <div className='mb-3 instructions'>
                Wir schicken dir ein E-Mail mit dem Betreff "[OLZ] E-Mail bestätigen".
                Es enthält einen Link, mit dem du dann deine E-Mail-Adresse bestätigen kannst.
            </div>
        </OlzEditModal>
    );
};

export function initOlzVerifyUserEmailModal(): boolean {
    return initOlzEditModal('verify-user-email-modal', () => (
        <OlzVerifyUserEmailModal/>
    ));
}
