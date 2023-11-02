import React from 'react';

import './OlzCopyableCredential.scss';

interface OlzCopyableCredentialProps {
    label: string;
    value: string;
    className?: string;
}

export const OlzCopyableCredential = (props: OlzCopyableCredentialProps): React.ReactElement => (
    <span className='olz-copyable-credential'>
        {props.label}:&nbsp;
        <input
            type='text'
            value={props.value}
            readOnly={true}
            onClick={(e: React.MouseEvent<HTMLInputElement>) => {
                e.currentTarget.select();
            }}
            className={`credential-output ${props.className ?? ''}`}
        />
    </span>
);
