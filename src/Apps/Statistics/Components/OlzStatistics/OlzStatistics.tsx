import React from 'react';
import {olzApi} from '../../../../Api/client';

import './OlzStatistics.scss';

function getOlzStatisticsUrl(username: string, password: string): string {
    const encUsername = encodeURIComponent(username);
    const encPassword = encodeURIComponent(password);
    return `https://${encUsername}:${encPassword}@olzimmerberg.ch/vstats/`;
}

export const OlzStatistics = () => {
    const [username, setUsername] = React.useState<string>('');
    const [password, setPassword] = React.useState<string>('');

    React.useEffect(() => {
        olzApi.call('getAppStatisticsCredentials', {}).then((data) => {
            setUsername(data.username);
            setPassword(data.password);
        });
    }, []);

    if (!username || !password) {
        return (
            <div>Lädt...</div>
        );
    }

    return (<>
        <iframe
            className='statistics-iframe'
            src={getOlzStatisticsUrl(username, password)}
        >
        </iframe>
    </>);
};
