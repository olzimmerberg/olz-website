import React from 'react';
import {olzApi} from '../../../Api/client';
import {OlzApiRequests, OlzUserInfoData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {codeHref, user as authUser} from '../../../Utils/constants';
import {loadRecaptcha, loadRecaptchaToken} from '../../../Utils/recaptchaUtils';
import {isLocal, timeout} from '../../../Utils/generalUtils';

import './OlzUserInfoModal.scss';

function getImageSrcArray(imageHrefs: {[resolution: string]: string}): {src?: string, srcset?: string} {
    const keys = Object.keys(imageHrefs);
    if (keys.length < 1) {
        return {};
    }
    const defaultSrc = imageHrefs['1x'] ?? imageHrefs[keys[0]];
    if (keys.length < 2) {
        return {'src': defaultSrc};
    }
    const srcSet = Object.entries(imageHrefs).map(([key, value]) => `${value} ${key}`).join(',\n');
    return {src: defaultSrc, srcset: srcSet};
}

// ---

interface OlzUserInfoModalProps {
    id: number;
}

export const OlzUserInfoModal = (props: OlzUserInfoModalProps): React.ReactElement => {
    const [recaptchaConsentGiven, setRecaptchaConsentGiven] = React.useState<boolean>(false);
    const [recaptchaToken, setRecaptchaToken] = React.useState<string|null>(null);
    const [user, setUser] = React.useState<OlzUserInfoData|null>(null);
    const [error, setError] = React.useState<Error|null>(null);

    const isAnonymous = !authUser?.username;

    React.useEffect(() => {
        if (!recaptchaConsentGiven) {
            return;
        }
        const getToken = async () => {
            await loadRecaptcha();
            if (isLocal()) {
                await timeout(250);
            } else {
                await timeout(Math.random() * 2000 + 1000);
            }
            setRecaptchaToken(await loadRecaptchaToken());
        };
        getToken();
    }, [recaptchaConsentGiven]);

    React.useEffect(() => {
        const fetchData = async () => {
            const request: OlzApiRequests['getUserInfo'] = {id: props.id};
            if (isAnonymous) {
                if (!recaptchaToken) {
                    return;
                }
                request.custom = {recaptchaToken};
            }
            const [err, result] = await olzApi.getResult('getUserInfo', request);
            setError(err);
            setUser(result?.data ?? null);
        };
        fetchData();
    }, [recaptchaToken]);

    let content: React.ReactNode = '';
    if (user) {
        const email = user.email ? atob(user.email) : null;
        content = (
            <div className='user-data container'>
                {(user.avatarImageId ? (<div>
                    <img
                        {...getImageSrcArray(user.avatarImageId)}
                    />
                </div>) : null)}
                <h5>{user.firstName} {user.lastName}</h5>
                {email ? (
                    <div>
                        <a href={`mailto:${email}`}>
                            {email}
                        </a>
                    </div>
                ) : null}
            </div>
        );
    } else if (error) {
        content = (
            <div className='container'>
                {error.message}
            </div>
        );
    } else if (recaptchaToken) {
        content = <div className='container'>Lädt...</div>;
    } else if (recaptchaConsentGiven) {
        content = <div className='container'>Bitte warten...</div>;
    } else if (isAnonymous) {
        content = (
            <div className='mb-3 container'>
                <input
                    type='checkbox'
                    name='recaptcha-consent-given'
                    value='yes'
                    checked={recaptchaConsentGiven}
                    onChange={(e) => setRecaptchaConsentGiven(e.target.checked)}
                    id='recaptcha-consent-given-input'
                />
                Ich akzeptiere, dass Google reCaptcha verwendet wird, um Bot-Spam zu verhinden.
                &nbsp;
                <a
                    href={`${codeHref}datenschutz`}
                    target='_blank'
                >
                    Weitere Informationen zum Datenschutz
                </a>
            </div>
        );
    } else {
        content = <div className='container'>Lädt...</div>;
    }


    return (
        <div
            className='modal fade'
            id='user-info-modal'
            tabIndex={-1}
            aria-hidden='true'
        >
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <div className='modal-body'>
                        <button
                            type='button'
                            className='btn-close close-button'
                            data-bs-dismiss='modal'
                            aria-label='Schliessen'
                        >
                        </button>
                        {content}
                    </div>
                </div>
            </div>
        </div>
    );
};

export function initOlzUserInfoModal(id: number): boolean {
    return initOlzEditModal('user-info-modal', () => (
        <OlzUserInfoModal id={id}/>
    ));
}
