import React from 'react';
import {olzApi, OlzApiRequests, OlzEmailInfoData} from '../../../Api/client';
import {initOlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzRestrictedPublicModal} from '../../../Components/Common/OlzRestrictedPublicModal/OlzRestrictedPublicModal';
import {dataHref} from '../../../Utils/constants';

import './OlzEmailModal.scss';

// ---

interface OlzEmailModalProps {
    token: string;
}

export const OlzEmailModal = (props: OlzEmailModalProps): React.ReactElement => {
    const [emailInfo, setEmailInfo] = React.useState<OlzEmailInfoData | null>(null);
    const [error, setError] = React.useState<Error | null>(null);

    const onReady = async (captchaToken: string | null) => {
        const request: OlzApiRequests['decryptEmailToken'] = {emailToken: props.token};
        if (captchaToken) {
            request.captchaToken = captchaToken;
        }
        const [err, result] = await olzApi.getResult('decryptEmailToken', request);
        setError(err);
        setEmailInfo(result);
    };

    let content: React.ReactNode = '';
    if (emailInfo) {
        const email = emailInfo.email ? emailInfo.email.map(atob) : null;
        content = (
            <div className='email-data'>
                <h3>{emailInfo.text}</h3>
                {email ? (
                    <div>
                        <a
                            href='#'
                            onClick={() => {
                                location.href = `mailto:${email.join('')}`;
                            }}
                            className='linkmail'
                        >
                            {email.map((chunk) => (
                                <span className='chunk'>{chunk}&nbsp;</span>
                            ))}
                        </a>
                        <button
                            id='copy-button'
                            className='button'
                            type='button'
                            onClick={() => {
                                navigator.clipboard.writeText(email.join(''));
                            }}
                        >
                            <img src={`${dataHref}assets/icns/copy_16.svg`} alt='Cp' />
                        </button>
                    </div>
                ) : null}
            </div>
        );
    } else if (error) {
        content = <div>{error.message}</div>;
    } else {
        content = <div>Lädt...</div>;
    }

    return (
        <OlzRestrictedPublicModal
            id='olz-email-modal'
            onReady={onReady}
        >
            {content}
        </OlzRestrictedPublicModal>
    );
};

export function initOlzEmailModal(token: string): boolean {
    return initOlzEditModal('olz-email-modal', () => (
        <OlzEmailModal token={token}/>
    ));
}
