import React from 'react';
import {initReact} from '../../Utils/reactUtils';
import {OlzMembers} from './Components/index';

initReact('react-root', <OlzMembers />);

export function loaded(): void {
    console.log('olzMembers loaded');
}
