import React from 'react';
import {OlzCaptcha} from '../../../Captcha/Components/OlzCaptcha/OlzCaptcha';
import {user as authUser} from '../../../Utils/constants';

import './OlzRestrictedPublicModal.scss';

interface OlzRestrictedPublicModalProps {
    id: string;
    onReady: (captchaToken: string|null) => void;
    children: React.ReactNode;
}

export const OlzRestrictedPublicModal = (props: OlzRestrictedPublicModalProps): React.ReactElement => {
    const [isReady, setIsReady] = React.useState<boolean>(false);

    const isAnonymous = !authUser?.username;

    React.useEffect(() => {
        if (!isAnonymous) {
            setIsReady(true);
            props.onReady(null);
        }
    }, []);

    let content: React.ReactNode = '';

    if (!isReady) {
        content = (
            <div className='mb-3'>
                <OlzCaptcha onToken={(token) => {
                    setIsReady(true);
                    props.onReady(token);
                }} />
            </div>
        );
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
