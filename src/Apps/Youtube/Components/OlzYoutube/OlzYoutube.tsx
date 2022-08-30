import React from 'react';
import {olzApi} from '../../../../Api/client';
import {OlzCopyableCredential} from '../../../../Components/Auth/OlzCopyableCredential/OlzCopyableCredential';

import './OlzYoutube.scss';

const OLZ_YOUTUBE_CHANNEL_URL = 'https://studio.youtube.com/channel/UCMhMdPRJOqdXHlmB9kEpmXQ/analytics';
const OLZ_GOOGLE_LOGIN_URL = `https://accounts.google.com/signin/v2/identifier?hl=de&passive=true&continue=${encodeURIComponent(OLZ_YOUTUBE_CHANNEL_URL)}&flowName=GlifWebSignIn&flowEntry=ServiceLogin`;

export const OlzYoutube = () => {
    const [username, setUsername] = React.useState<string>('');
    const [password, setPassword] = React.useState<string>('');

    const openGoogleLoginWindow = () => {
        window.open(OLZ_GOOGLE_LOGIN_URL, '_blank', '');
    };

    React.useEffect(() => {
        olzApi.call('getAppYoutubeCredentials', {}).then((data) => {
            setUsername(data.username);
            setPassword(data.password);
        });
    }, []);

    return (<>
        <h1>YouTube-Kanal</h1>
        <div>Mit folgenden Daten bei Google einloggen:</div>
        <div>
            <OlzCopyableCredential
                label='Benutername'
                value={username}
            />
            <wbr />
            <OlzCopyableCredential
                label='Passwort'
                value={password}
            />
        </div>
        <div className='buttons'>
            <button
                onClick={openGoogleLoginWindow}
                className='btn btn-primary'
            >
                Login &amp; YouTube-Kanal ansehen
            </button>
        </div>
    </>);
};
