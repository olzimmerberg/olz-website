import React from 'react';
import ReactDOM from 'react-dom';
import {OlzMonitoring} from './Components/index';

ReactDOM.render(
    <OlzMonitoring />,
    document.getElementById('react-root'),
);

export function loaded(): void {
    console.log('olzMonitoring loaded');
}
