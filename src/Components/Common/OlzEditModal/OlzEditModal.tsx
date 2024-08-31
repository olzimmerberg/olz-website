import * as bootstrap from 'bootstrap';
import React from 'react';
import {initReact} from '../../../Utils/reactUtils';

interface OlzEditModalProps {
    modalId: string;
    dialogTitle: React.ReactNode;
    children: React.ReactNode;
    successMessage: React.ReactNode;
    errorMessage: React.ReactNode;
    isLoading?: boolean;
    isWaitingForCaptcha?: boolean;
    isSubmitting: boolean;
    submitLabel?: string;
    onSubmit: React.FormEventHandler<HTMLFormElement>;
}

export const OlzEditModal = (props: OlzEditModalProps): React.ReactElement => {
    const submitLabel = props.submitLabel ?? 'Speichern';
    const isPleaseWait = props.isLoading || props.isWaitingForCaptcha || props.isSubmitting;
    return (
        <div
            className='modal fade'
            id={props.modalId}
            tabIndex={-1}
            aria-labelledby={`${props.modalId}-label`}
            aria-hidden='true'
        >
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={props.onSubmit}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id={`${props.modalId}-label`}>
                                {props.dialogTitle}
                            </h5>
                            <button
                                type='button'
                                className='btn-close'
                                data-bs-dismiss='modal'
                                aria-label='Schliessen'
                            >
                            </button>
                        </div>
                        <div className='modal-body'>
                            {props.children}
                            <div className='success-message alert alert-success' role='alert'>
                                {props.successMessage}
                            </div>
                            <div className='error-message alert alert-danger' role='alert'>
                                {props.errorMessage}
                            </div>
                        </div>
                        <div className='modal-footer'>
                            <button
                                type='button'
                                className='btn btn-secondary'
                                data-bs-dismiss='modal'
                            >
                                Abbrechen
                            </button>
                            <button
                                type='submit'
                                className={isPleaseWait ? 'btn btn-secondary' : 'btn btn-primary'}
                                id='submit-button'
                                disabled={props.isLoading || props.isSubmitting}
                            >
                                {isPleaseWait ? 'Bitte warten...' : submitLabel}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
};

export function initOlzEditModal(
    modalId: string,
    getElement: () => React.ReactElement,
    configureModal?: (modal: HTMLElement) => void,
): boolean {
    initReact('edit-entity-react-root', getElement());
    window.setTimeout(() => {
        const modalElem = document.getElementById(modalId);
        if (!modalElem) {
            return;
        }
        const modal = new bootstrap.Modal(modalElem, {backdrop: 'static'});
        if (!modal) {
            return;
        }
        modal.show();
        if (configureModal) {
            configureModal(modalElem);
        }
    }, 1);
    return false;
}
