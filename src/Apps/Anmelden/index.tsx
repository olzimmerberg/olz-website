import React from 'react';
import ReactDOM from 'react-dom';
import {OlzRegistrationView} from './Components/index';

ReactDOM.render(
    <OlzRegistrationView />,
    document.getElementById('react-root'),
);

export function loaded(): void {
    console.log('olzAnmelden loaded');
}
