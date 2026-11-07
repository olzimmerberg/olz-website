import React from 'react';
import {initReact} from '../../../../Common/Utils/reactUtils';
import {OlzTransportConnectionSearch} from '../OlzTransportConnectionSearch/OlzTransportConnectionSearch';

import './OlzOev.scss';

export function initOlzTransportConnectionSearch(): boolean {
    initReact('oev-root', <OlzTransportConnectionSearch />);
    return false;
}
