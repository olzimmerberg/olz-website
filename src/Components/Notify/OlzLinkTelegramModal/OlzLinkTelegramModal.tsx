import * as bootstrap from 'bootstrap';
import React from 'react';
import {olzApi} from '../../../Api/client';
import {codeHref} from '../../../Utils/constants';
import {initReact} from '../../../Utils/reactUtils';

import './OlzLinkTelegramModal.scss';

// ---

export const OlzLinkTelegramModal = (): React.ReactElement => {
    const [chatLinkDesktop, setChatLinkDesktop] = React.useState<string | null>(null);
    const [chatLinkMobile, setChatLinkMobile] = React.useState<string | null>(null);
    const [chatMessage, setChatMessage] = React.useState<string | null>(null);
    const [errorMessage, setErrorMessage] = React.useState<string | null>(null);

    React.useEffect(() => {
        olzApi.call(
            'linkTelegram',
            {},
        )
            .then((response) => {
                setChatLinkDesktop(`https://web.telegram.org/k/#@${response.botName}`);
                setChatLinkMobile(`https://t.me/${response.botName}?start=${response.pin}`);
                setChatMessage(`/start ${response.pin}`);
            })
            .catch((err) => {
                setErrorMessage(`Ein Fehler ist aufgetreten: ${err.message}`);
            });
    }, []);

    let installationInstructions = (<>
        <li>Installiere die Telegram-App auf deinem Smartphone</li>
        <li>
            Logge dich in&nbsp;
            <a href='https://web.telegram.org' rel='noopener noreferrer' target='_blank'>
                Telegram Web
            </a>
            &nbsp;ein
        </li>
        <li>
            {chatLinkDesktop ? (<>
                Öffne&nbsp;
                <a href={chatLinkDesktop ?? '#'} rel='noopener noreferrer' target='_blank'>
                    deinen persönlichen OLZ-Info-Chat
                </a>
            </>) : 'Bitte warten...'}
        </li>
        <li>Klicke auf "START"</li>
        <li>
            {chatMessage ? (<>
                Sende dem OLZ Bot folgende Nachricht:
                <input
                    type='text'
                    value={chatMessage ?? 'Bitte warten...'}
                    className='form-control'
                    readOnly
                />
            </>) : 'Bitte warten...'}
        </li>
    </>);

    if (/Android|iPhone|iPad/.exec(navigator.userAgent)) {
        installationInstructions = (<>
            <li><a href='https://telegram.org/dl/' rel='noopener noreferrer' target='_blank'>
                Installiere die Telegram-App
            </a></li>
            <li>
                {chatLinkMobile ? (<>
                    Öffne&nbsp;
                    <a href={chatLinkMobile ?? '#'} rel='noopener noreferrer' target='_blank'>
                        deinen persönlichen OLZ-Info-Chat
                    </a>
                </>) : 'Bitte warten...'}
            </li>
            <li>Klicke auf "SEND MESSAGE"</li>
            <li>...und dann auf "START"</li>
        </>);
    }

    if (errorMessage) {
        installationInstructions = (<li>
            <div className='error-message alert alert-danger' role='alert'>
                {errorMessage}
            </div>
        </li>);
    }

    return (
        <div
            className='modal fade'
            id='link-telegram-modal'
            tabIndex={-1}
            aria-labelledby='link-telegram-modal-label'
            aria-hidden='true'
        >
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <div className='modal-header'>
                        <h3 className='modal-title' id='link-telegram-modal-label'>
                            OLZ-Telegram-Bot aktivieren
                        </h3>
                        <button
                            type='button'
                            className='btn-close'
                            data-bs-dismiss='modal'
                            aria-label='Schliessen'
                        >
                        </button>
                    </div>
                    <div className='modal-body'>
                        <div className='mb-3 telegram-circle-container'>
                            <div className='telegram-circle'>
                                <img src={`${codeHref}assets/icns/login_telegram.svg`} alt=''/>
                            </div>
                        </div>
                        <ol className='todo-list'>
                            {installationInstructions}
                        </ol>
                    </div>
                    <div className='modal-footer'>
                        <button
                            type='button'
                            className='btn btn-secondary'
                            data-bs-dismiss='modal'
                        >
                            Schliessen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export function initOlzLinkTelegramModal(): boolean {
    initReact('dialog-react-root', (
        <OlzLinkTelegramModal/>
    ));
    window.setTimeout(() => {
        const modal = document.getElementById('link-telegram-modal');
        if (!modal) {
            return;
        }
        new bootstrap.Modal(modal, {backdrop: 'static'}).show();
    }, 1);
    return false;
}
