import React from 'react';
import {codeHref, user as authUser} from '../../../Utils/constants';
import {loadRecaptcha, loadRecaptchaToken} from '../../../Utils/recaptchaUtils';
import {isLocal, timeout} from '../../../Utils/generalUtils';

import './OlzRestrictedPublicModal.scss';

interface OlzRestrictedPublicModalProps {
    id: string;
    onReady: (recaptchaToken: string|null) => void;
    children: React.ReactNode;
}

export const OlzRestrictedPublicModal = (props: OlzRestrictedPublicModalProps): React.ReactElement => {
    const [recaptchaConsentGiven, setRecaptchaConsentGiven] = React.useState<boolean>(false);
    const [timeHasPassed, setTimeHasPassed] = React.useState<boolean>(false);
    const [isReady, setIsReady] = React.useState<boolean>(false);

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
                await timeout(Math.random() * 1500 + 500);
            }
            setTimeHasPassed(true);
        };
        getToken();
    }, [recaptchaConsentGiven]);

    React.useEffect(() => {
        if (!isAnonymous) {
            setIsReady(true);
            props.onReady(null);
        }
    }, []);

    let content: React.ReactNode = '';

    if (!isReady) {
        const checkbox = (
            <div className='mb-3'>
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
        if (timeHasPassed) {
            content = (
                <div className='container'>
                    {checkbox}
                    <button
                        type='button'
                        className='btn btn-secondary'
                        onClick={async () => {
                            setIsReady(true);
                            props.onReady(await loadRecaptchaToken());
                        }}
                    >
                        Anzeigen
                    </button>
                </div>
            );
        } else if (recaptchaConsentGiven) {
            content = (
                <div className='container'>
                    {checkbox}
                    <button
                        type='button'
                        className='btn btn-secondary'
                    >
                        Bitte warten...
                    </button>
                </div>
            );
        } else {
            content = (<div className='container'>{checkbox}</div>);
        }
    } else {
        content = <div className='container'>{props.children}</div>;
    }


    return (
        <div
            className='modal fade olz-restricted-public-modal'
            id={props.id}
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
