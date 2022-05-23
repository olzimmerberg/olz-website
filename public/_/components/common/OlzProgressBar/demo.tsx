import React from 'react';
import ReactDOM from 'react-dom';
import {registerDemo} from '../../demo_common';
import {OlzProgressBar} from './OlzProgressBar';

const Demo = () => {
    const [progress, setProgress] = React.useState(0);
    return (<>
        <OlzProgressBar progress={progress} />
        <OlzProgressBar progress={1/3} />
        <OlzProgressBar progress={1} />
        <button onClick={() => setProgress(progress + 0.1)}>Animate</button>
    </>);
}

registerDemo('OlzProgressBar', () => {
    ReactDOM.render(
        <Demo />,
        document.getElementById('demo-root'),
    );
});
