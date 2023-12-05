import React from 'react';
import ReactDOM from 'react-dom';
import {OlzSearchEngines} from './Components/index';

ReactDOM.render(
    <OlzSearchEngines />,
    document.getElementById('react-root'),
);

export function loaded(): void {
    console.log('olzSearchEngines loaded');
}
