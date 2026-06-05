import React from 'react';
import {olzApi, OlzApiRequests, OlzRoleInfoData} from '../../../Api/client';
import {initOlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzRestrictedPublicModal} from '../../../Components/Common/OlzRestrictedPublicModal/OlzRestrictedPublicModal';
import {codeHref, dataHref} from '../../../Utils/constants';
import {getImageSrcArray} from '../../../Users/Components/OlzUserInfoModal/OlzUserInfoModal';

import './OlzRoleInfoModal.scss';

// ---

interface OlzRoleInfoModalProps {
    id: number;
}

export const OlzRoleInfoModal = (props: OlzRoleInfoModalProps): React.ReactElement => {
    const [role, setRole] = React.useState<OlzRoleInfoData | null>(null);
    const [error, setError] = React.useState<Error | null>(null);

    const onReady = async (captchaToken: string | null) => {
        const request: OlzApiRequests['getRoleInfo'] = {id: props.id};
        if (captchaToken) {
            request.captchaToken = captchaToken;
        }
        const [err, result] = await olzApi.getResult('getRoleInfo', request);
        setError(err);
        setRole(result);
    };

    let content: React.ReactNode = '';
    if (role) {
        const assigneesContent = role.assignees.map((assignee) => {
            const email = assignee.email ? assignee.email.map(atob) : null;
            return (
                <div className='user-data'>
                    {(assignee.avatarImageId ? (<div>
                        <img
                            {...getImageSrcArray(assignee.avatarImageId)}
                            className='avatar'
                        />
                    </div>) : null)}
                    <div>
                        <h4>{assignee.firstName} {assignee.lastName}</h4>
                        {email ? (
                            <div className='user-email'>
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
            );
        });
        const email = role.email ? role.email.map(atob) : null;
        content = (
            <div className='role-data container'>
                {role.username ? (<h3>
                    Ressort:
                    &nbsp;
                    <a
                        href={`${codeHref}verein/${role.username}`}
                        className='linkint'
                    >
                        {role.name}
                    </a>
                </h3>) : null}
                {email ? (
                    <div className='role-email'>
                        <span>
                            E-Mail:
                            &nbsp;
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
                        </span>
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
                <div className='assignee-container'>
                    <h5>Verantwortlich</h5>
                    {assigneesContent}
                </div>
            </div>
        );
    } else if (error) {
        content = <div>{error.message}</div>;
    } else {
        content = <div>Lädt...</div>;
    }

    return (
        <OlzRestrictedPublicModal
            id='role-info-modal'
            protectionReason='unsere Kontaktdaten'
            onReady={onReady}
        >
            {content}
        </OlzRestrictedPublicModal>
    );
};

export function initOlzRoleInfoModal(id: number): boolean {
    return initOlzEditModal('role-info-modal', () => (
        <OlzRoleInfoModal id={id}/>
    ));
}
