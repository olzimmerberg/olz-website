import React from 'react';
import {olzApi} from '../../../Api/client';
import {OlzApiRequests, OlzRoleInfoData} from '../../../Api/client/generated_olz_api_types';
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
    const [role, setRole] = React.useState<OlzRoleInfoData|null>(null);
    const [error, setError] = React.useState<Error|null>(null);

    const onReady = async (recaptchaToken: string|null) => {
        const request: OlzApiRequests['getRoleInfo'] = {id: props.id};
        if (recaptchaToken) {
            request.recaptchaToken = recaptchaToken;
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
                        <h3>{assignee.firstName} {assignee.lastName}</h3>
                        {email ? (
                            <div className='user-data'>
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
        content = (
            <div className='role-data container'>
                {role.username ? (<h4>
                    <a
                        href={`${codeHref}verein/${role.username}`}
                        className='linkint'
                    >
                        {role.name}
                    </a>
                </h4>) : null}
                {assigneesContent}
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
