import React from 'react';

import './OlzCopyableCredential.scss';

interface OlzCopyableCredentialProps {
    label: string;
    value: string;
}

export const OlzCopyableCredential = (props: OlzCopyableCredentialProps) => {
    return (
        <span className='olz-copyable-credential'>
            {props.label}:&nbsp;
            <input
                type='text'
                value={props.value}
                readOnly={true}
                onClick={(e: React.MouseEvent<HTMLInputElement>) => {
                    e.currentTarget.select();
                }}
                className='credential-output'
            />
        </span>
    );
};
