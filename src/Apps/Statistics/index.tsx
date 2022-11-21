import React from 'react';
import ReactDOM from 'react-dom';
import {OlzStatistics} from './Components/index';

ReactDOM.render(
    <OlzStatistics />,
    document.getElementById('react-root'),
);    

export function loaded(): void {
    console.log('olzStatistics loaded');
}
