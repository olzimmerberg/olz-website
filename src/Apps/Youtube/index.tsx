import React from 'react';
import ReactDOM from 'react-dom';
import {OlzYoutube} from './Components/index';

ReactDOM.render(
    <OlzYoutube />,
    document.getElementById('react-root'),
);

export function loaded(): void {
    console.log('olzYoutube loaded');
}
