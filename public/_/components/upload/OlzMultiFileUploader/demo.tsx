import React from 'react';
import ReactDOM from 'react-dom';
import {registerDemo} from '../../demo_common';
import {OlzMultiFileUploader} from './OlzMultiFileUploader';

const Demo = () => {
    return (<>
        <OlzMultiFileUploader />
    </>);
}

registerDemo('OlzMultiFileUploader', () => {
    ReactDOM.render(
        <Demo />,
        document.getElementById('demo-root'),
    );
});
