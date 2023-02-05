import React from 'react';
import ReactDOM from 'react-dom';
import {OlzLogs} from './Components/index';

const elem = document.getElementById('react-root');
if (elem) {
    ReactDOM.render(
        <OlzLogs />,
        elem,
    );
}

export function loaded(): void {
    console.log('olzLogs loaded');
}
