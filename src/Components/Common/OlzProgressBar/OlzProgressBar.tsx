import React from 'react';

import './OlzProgressBar.scss';

interface OlzProgressBarProps {
    progress?: number; // number between 0 and 1
}

export const OlzProgressBar = (props: OlzProgressBarProps) => {
    const sanitizedProgress = Math.max(0, Math.min(1, props.progress || 0));
    const width = `${(sanitizedProgress * 100).toLocaleString('en-US')}%`;
    return (
        <div className='olz-progress-bar-container'>
            <div className='olz-progress-bar' style={{width}}></div>
        </div>
    );
};
