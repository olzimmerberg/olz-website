import React from 'react';
import {olzApi} from '../../../../Api/client';
import {OlzCopyableCredential} from '../../../../Components/Auth/OlzCopyableCredential/OlzCopyableCredential';

import './OlzGoogleSearch.scss';

const OLZ_GOOGLE_SEARCH_URL = 'https://search.google.com/u/3/search-console?resource_id=sc-domain:olzimmerberg.ch';
const OLZ_GOOGLE_LOGIN_URL = `https://accounts.google.com/signin/v2/identifier?hl=de&passive=true&continue=${encodeURIComponent(OLZ_GOOGLE_SEARCH_URL)}&flowName=GlifWebSignIn&flowEntry=ServiceLogin`;

const OLZ_BING_SEARCH_URL = 'https://www.bing.com/webmasters/home?siteUrl=https://olzimmerberg.ch/';
const OLZ_LOGIN_URL = 'https://accounts.google.com/signin/v2/identifier?hl=de';

export const OlzGoogleSearch = () => {
    const [username, setUsername] = React.useState<string>('');
    const [password, setPassword] = React.useState<string>('');

    const openGoogleLoginWindow = () => {
        window.open(OLZ_GOOGLE_LOGIN_URL, '_blank', '');
    };

    const openLoginWindow = () => {
        window.open(OLZ_LOGIN_URL, '_blank', '');
    };

    const openBingWindow = () => {
        window.open(OLZ_BING_SEARCH_URL, '_blank', '');
    };

    React.useEffect(() => {
        olzApi.call('getAppGoogleSearchCredentials', {}).then((data) => {
            setUsername(data.username);
            setPassword(data.password);
        });
    }, []);

    return (<>
        <h1>Google</h1>
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
                Login &amp; Google Analyse
            </button>
        </div>

        <h1>Bing</h1>
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
                onClick={openLoginWindow}
                className='btn btn-secondary'
            >
                Login
            </button>
        </div>
        <div>Dann bei Bing "Mit Google anmelden":</div>
        <div className='buttons'>
            <button
                onClick={openBingWindow}
                className='btn btn-primary'
            >
                Bing Analyse
            </button>
        </div>
    </>);
};
