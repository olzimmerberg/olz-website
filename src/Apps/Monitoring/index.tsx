import React from 'react';
import {initReact} from '../../Utils/reactUtils';
import {OlzMonitoring} from './Components/index';

initReact('react-root', <OlzMonitoring />);

export function loaded(): void {
    console.log('olzMonitoring loaded');
}
