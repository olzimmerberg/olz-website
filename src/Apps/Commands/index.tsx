import React from 'react';
import {initReact} from '../../Common/Utils/reactUtils';
import {OlzCommands} from './Components/index';

initReact('react-root', <OlzCommands />);

export function loaded(): void {
    console.log('olzCommands loaded');
}
