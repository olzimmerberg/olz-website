import React from 'react';
import {initReact} from '../../Utils/reactUtils';
import {OlzLogs} from './Components/index';

initReact('react-root', <OlzLogs />);

export function loaded(): void {
    console.log('olzLogs loaded');
}
