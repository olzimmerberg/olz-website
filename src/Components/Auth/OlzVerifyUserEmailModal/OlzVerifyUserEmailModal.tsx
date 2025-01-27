import React from 'react';
import {olzApi} from '../../../Api/client';
import {initOlzEditModal, OlzEditModal, OlzEditModalStatus} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {codeHref} from '../../../Utils/constants';

import './OlzVerifyUserEmailModal.scss';

// ---

export const OlzVerifyUserEmailModal = (): React.ReactElement => {
    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>): Promise<void> => {
        e.preventDefault();
        setStatus({id: 'SUBMITTING'});

        const [err, response] = await olzApi.getResult('verifyUserEmail', {});
        if (response?.status !== 'OK') {
            setStatus({id: 'SUBMIT_FAILED', message: `Fehler: ${err?.message} (Antwort: ${response?.status}).`});
            return;
        }
        setStatus({id: 'SUBMITTED', message: 'E-Mail versendet. Bitte warten...'});
        // This removes Google's injected reCaptcha script again
        window.location.href = `${codeHref}benutzer/ich`;
    };

    const dialogTitle = 'E-Mail-Adresse bestätigen';

    return (
        <OlzEditModal
            modalId='verify-user-email-modal'
            dialogTitle={dialogTitle}
            status={status}
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
