import React from 'react';
import {initReact} from '../../Common/Utils/reactUtils';
import {OlzStatistics} from './Components/index';

initReact('react-root', <OlzStatistics />);

export function loaded(): void {
    console.log('olzStatistics loaded');
}
