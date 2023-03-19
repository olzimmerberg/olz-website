import React from 'react';
import ReactDOM from 'react-dom';
import {OlzPanini2024} from './Components/index';

const elem = document.getElementById('react-root');
if (elem) {
    ReactDOM.render(
        <OlzPanini2024 />,
        elem,
    );
}

export function loaded(): void {
    console.log('olzPanini2024 loaded');
}
