import React from 'react';
import {initReact} from '../../Utils/reactUtils';
import {OlzYoutube} from './Components/index';

initReact('react-root', <OlzYoutube />);

export function loaded(): void {
    console.log('olzYoutube loaded');
}
