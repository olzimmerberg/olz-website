import React from 'react';
import ReactDOM from 'react-dom';

export const OlzEditNewsView = () => {
    return (<div>OlzEditNewsView</div>);
};

export function initOlzEditNewsView() {
    ReactDOM.render(
        <OlzEditNewsView />,
        document.getElementById('edit-news-react-root'),
    );
    
}
