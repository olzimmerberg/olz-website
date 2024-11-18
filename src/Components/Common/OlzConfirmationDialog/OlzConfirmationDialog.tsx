import * as bootstrap from 'bootstrap';
import React from 'react';
import {initReact} from '../../../Utils/reactUtils';

interface OlzConfirmationDialogProps {
    title: string;
    onConfirm: () => unknown;
    onCancel?: () => unknown;
    description?: string;
    cancelLabel?: string;
    confirmLabel?: string;
    confirmButtonStyle?: string;
}

export const OlzConfirmationDialog = (props: OlzConfirmationDialogProps): React.ReactElement => {
    const confirmButtonClassName = `btn ${props.confirmButtonStyle ?? 'btn-primary'}`;
    const confirmDialogBody = props.description ? (
        <div className='modal-body'>
            <div>
                {props.description ?? ''}
            </div>
        </div>
    ) : undefined;
    return (
        <div className='modal fade' id='confirmation-dialog-modal' tabIndex={-1} aria-labelledby='confirmation-dialog-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <div className='modal-header'>
                        <h5 className='modal-title' id='confirmation-dialog-modal-label'>
                            {props.title}
                        </h5>
                    </div>
                    {confirmDialogBody}
                    <div className='modal-footer'>
                        <button
                            type='button'
                            className='btn btn-secondary'
                            data-bs-dismiss='modal'
                        >
                            {props.cancelLabel ?? 'Abbrechen'}
                        </button>
                        <button
                            type='button'
                            className={confirmButtonClassName}
                            data-bs-dismiss='modal'
                            id='confirm-button'
                            onClick={props.onConfirm}
                        >
                            {props.confirmLabel ?? 'Bestätigen'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

interface OlzConfirmOptionalParams {
    description?: string;
    cancelLabel?: string;
    confirmLabel?: string;
    confirmButtonStyle?: string;
}

export async function olzConfirm(
    title: string,
    params?: OlzConfirmOptionalParams,
): Promise<true> {
    return new Promise((resolve, reject) => {
        initReact(
            'dialog-react-root',
            <OlzConfirmationDialog
                title={title}
                onCancel={() => reject(new Error('Abgebrochen'))}
                onConfirm={() => resolve(true)}
                {...params}
            />,
        );
        setTimeout(() => {
            const modal = document.getElementById('confirmation-dialog-modal');
            if (modal) {
                new bootstrap.Modal(modal, {backdrop: 'static'}).show();
            }
        }, 1);
    });
}
