import React from 'react';
import {olzApi} from '../../../../Api/client';

import './OlzMonitoring.scss';

const OLZ_MONITORING_URL = 'https://status.olzimmerberg.ch/';

export const OlzMonitoring = () => {
    const [username, setUsername] = React.useState<string>('');
    const [password, setPassword] = React.useState<string>('');

    React.useEffect(() => {
        olzApi.call('getAppMonitoringCredentials', {}).then((data) => {
            setUsername(data.username);
            setPassword(data.password);
        });
    }, []);

    return (<>
        <div>Username: {username} &mdash; Password: {password}</div>
        <iframe
            className='monitoring-iframe'
            src={OLZ_MONITORING_URL}
        >
        </iframe>
    </>);
};
