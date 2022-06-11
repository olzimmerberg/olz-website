import React from 'react';
import ReactDOM from 'react-dom';
import {OlzTransportConnectionSearch} from '../OlzTransportConnectionSearch/OlzTransportConnectionSearch';

import './OlzOev.scss';

export function initOlzTransportConnectionSearch() {
    ReactDOM.render(
        <OlzTransportConnectionSearch />,
        document.getElementById('oev-root'),
    );
    return false;
}
