import React from 'react';
import {olzApi} from '../../../../Api/client';
import {OlzCopyableCredential} from '../../../../Components/Auth/OlzCopyableCredential/OlzCopyableCredential';

import './OlzMonitoring.scss';

const OLZ_MONITORING_URL = 'https://status.olzimmerberg.ch/';

export const OlzMonitoring = (): React.ReactElement => {
    const [username, setUsername] = React.useState<string>('');
    const [password, setPassword] = React.useState<string>('');

    React.useEffect(() => {
        olzApi.call('getAppMonitoringCredentials', {}).then((data) => {
            setUsername(data.username);
            setPassword(data.password);
        });
    }, []);

    return (<>
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
        <iframe
            className='monitoring-iframe'
            src={OLZ_MONITORING_URL}
        >
        </iframe>
    </>);
};
