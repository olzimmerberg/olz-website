import React from 'react';
import {initReact} from '../../Common/Utils/reactUtils';
import {OlzSearchEngines} from './Components/index';


initReact('react-root', <OlzSearchEngines />);

export function loaded(): void {
    console.log('olzSearchEngines loaded');
}
