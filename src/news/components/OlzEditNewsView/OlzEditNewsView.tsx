import React from 'react';
import ReactDOM from 'react-dom';
import {OlzMultiFileUploader} from '../../../components/upload/OlzMultiFileUploader/OlzMultiFileUploader';

export const OlzEditNewsView = () => {
    return (
        <div>
            <OlzMultiFileUploader />
        </div>
    );
};

export function initOlzEditNewsView() {
    ReactDOM.render(
        <OlzEditNewsView />,
        document.getElementById('edit-news-react-root'),
    );
    
}
