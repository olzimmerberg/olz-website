import React from 'react';
import {olzApi} from '../../../Api/client';
import {OlzApiRequests, OlzAuthorInfoData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzRestrictedPublicModal} from '../../../Components/Common/OlzRestrictedPublicModal/OlzRestrictedPublicModal';
import {getImageSrcArray} from '../../../Users/Components/OlzUserInfoModal/OlzUserInfoModal';
import {codeHref, dataHref} from '../../../Utils/constants';

import './OlzAuthorBadge.scss';

interface OlzAuthorBadgeProps {
    id: number;
}

export const OlzAuthorBadge = (props: OlzAuthorBadgeProps): React.ReactElement => {
    const [authorInfo, setAuthorInfo] = React.useState<OlzAuthorInfoData|null>(null);
    const [error, setError] = React.useState<Error|null>(null);

    const onReady = async (captchaToken: string|null) => {
        const request: OlzApiRequests['getAuthorInfo'] = {id: props.id};
        if (captchaToken) {
            request.captchaToken = captchaToken;
        }
        const [err, result] = await olzApi.getResult('getAuthorInfo', request);
        setError(err);
        setAuthorInfo(result);
    };

    let content: React.ReactNode = '';
    if (authorInfo) {
        const email = authorInfo.email ? authorInfo.email.map(atob) : null;
        content = (
            <div className='author-data container'>
                {authorInfo.roleUsername ? (<h4>
                    <a
                        href={`${codeHref}verein/${authorInfo.roleUsername}`}
                        className='linkint'
                    >
                        {authorInfo.roleName}
                    </a>
                </h4>) : null}
                <div className='user-data'>
                    {(authorInfo.avatarImageId ? (<div>
                        <img
                            {...getImageSrcArray(authorInfo.avatarImageId)}
                            className='avatar'
                        />
                    </div>) : null)}
                    <div>
                        <h3>{authorInfo.firstName} {authorInfo.lastName}</h3>
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
                </div>
            </div>
        );
    } else if (error) {
        content = (
            <div className='container'>
                {error.message}
            </div>
        );
    } else {
        content = <div className='container'>Lädt...</div>;
    }

    return (
        <OlzRestrictedPublicModal
            id='author-badge-modal'
            onReady={onReady}
        >
            {content}
        </OlzRestrictedPublicModal>
    );
};

export function initOlzAuthorBadge(id: number): boolean {
    return initOlzEditModal('author-badge-modal', () => (
        <OlzAuthorBadge id={id}/>
    ));
}
