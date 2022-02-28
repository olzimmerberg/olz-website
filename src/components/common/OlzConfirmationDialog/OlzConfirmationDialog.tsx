import React from 'react';
import ReactDOM from 'react-dom';

interface OlzConfirmationDialogProps {
    title: string;
    onConfirm: () => any;
    onCancel?: () => any;
    description?: string;
    cancelLabel?: string;
    confirmLabel?: string;
    confirmButtonStyle?: string;
}

export const OlzConfirmationDialog = (props: OlzConfirmationDialogProps) => {
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
                            data-dismiss='modal'
                        >
                            {props.cancelLabel ?? 'Abbrechen'}
                        </button>
                        <button
                            type='button'
                            className={confirmButtonClassName}
                            data-dismiss='modal'
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
        ReactDOM.render(
            <OlzConfirmationDialog
                title={title}
                onCancel={() => reject(new Error('Abgebrochen'))}
                onConfirm={() => resolve(true)}
                {...params}
            />,
            document.getElementById('confirmation-dialog-react-root'),
        );
        $('#confirmation-dialog-modal').modal({backdrop: 'static'});
    });
}
