import React from 'react';
import ReactDOM from 'react-dom';
import {OlzPanini2024, OlzPanini2024Masks} from './Components/index';

const elem = document.getElementById('react-root');
if (elem) {
    ReactDOM.render(
        <OlzPanini2024 />,
        elem,
    );
}

const elemMasks = document.getElementById('react-root-masks');
if (elemMasks) {
    const olzPanini2024Mask = (window as unknown as {olzPanini2024Mask: string}).olzPanini2024Mask;
    ReactDOM.render(
        <OlzPanini2024Masks mask={olzPanini2024Mask} />,
        elemMasks,
    );
}

export function loaded(): void {
    console.log('olzPanini2024 loaded');
}
