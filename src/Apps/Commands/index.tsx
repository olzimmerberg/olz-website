import React from 'react';
import {initReact} from '../../Utils/reactUtils';
import {OlzCommands} from './Components/index';

initReact('react-root', <OlzCommands />);

export function loaded(): void {
    console.log('olzCommands loaded');
}
