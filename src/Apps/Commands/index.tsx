import React from 'react';
import ReactDOM from 'react-dom';
import {OlzCommands} from './Components/index';

ReactDOM.render(
    <OlzCommands />,
    document.getElementById('react-root'),
);

export function loaded(): void {
    console.log('olzCommands loaded');
}
