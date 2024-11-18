import React from 'react';
import {OlzRegistrationView} from './Components/index';
import {initReact} from '../../Utils/reactUtils';

initReact('react-root', <OlzRegistrationView />);

export function loaded(): void {
    console.log('olzAnmelden loaded');
}
