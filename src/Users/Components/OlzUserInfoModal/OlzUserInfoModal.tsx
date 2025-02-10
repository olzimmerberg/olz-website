import React from 'react';
import {olzApi} from '../../../Api/client';
import {OlzApiRequests, OlzUserInfoData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzRestrictedPublicModal} from '../../../Components/Common/OlzRestrictedPublicModal/OlzRestrictedPublicModal';
import {dataHref} from '../../../Utils/constants';

import './OlzUserInfoModal.scss';

export function getImageSrcArray(imageHrefs: {[resolution: string]: string}): {src?: string, srcSet?: string} {
    const keys = Object.keys(imageHrefs);
    if (keys.length < 1) {
        return {};
    }
    const src = imageHrefs['1x'] ?? imageHrefs[keys[0]];
    if (keys.length < 2) {
        return {src};
    }
    const srcSet = Object.entries(imageHrefs).map(([key, value]) => `${value} ${key}`).join(',\n');
    return {src, srcSet};
}

// ---

interface OlzUserInfoModalProps {
    id: number;
}

export const OlzUserInfoModal = (props: OlzUserInfoModalProps): React.ReactElement => {
    const [user, setUser] = React.useState<OlzUserInfoData|null>(null);
    const [error, setError] = React.useState<Error|null>(null);

    const onReady = async (recaptchaToken: string|null) => {
        const request: OlzApiRequests['getUserInfo'] = {id: props.id};
        if (recaptchaToken) {
            request.recaptchaToken = recaptchaToken;
        }
        const [err, result] = await olzApi.getResult('getUserInfo', request);
        setError(err);
        setUser(result);
    };

    let content: React.ReactNode = '';
    if (user) {
        const email = user.email ? user.email.map(atob) : null;
        content = (
            <div className='user-data'>
                {(user.avatarImageId ? (<div>
                    <img
                        {...getImageSrcArray(user.avatarImageId)}
                        className='avatar'
                    />
                </div>) : null)}
                <h3>{user.firstName} {user.lastName}</h3>
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
            id='user-info-modal'
            onReady={onReady}
        >
            {content}
        </OlzRestrictedPublicModal>
    );
};

export function initOlzUserInfoModal(id: number): boolean {
    return initOlzEditModal('user-info-modal', () => (
        <OlzUserInfoModal id={id}/>
    ));
}
