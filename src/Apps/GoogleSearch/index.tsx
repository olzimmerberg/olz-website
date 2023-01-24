import React from 'react';
import ReactDOM from 'react-dom';
import {OlzGoogleSearch} from './Components/index';

ReactDOM.render(
    <OlzGoogleSearch />,
    document.getElementById('react-root'),
);

export function loaded(): void {
    console.log('olzGoogleSearch loaded');
}
