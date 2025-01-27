import * as bootstrap from 'bootstrap';
import React from 'react';
import {initReact} from '../../../Utils/reactUtils';
import {codeHref} from '../../../Utils/constants';
import {olzConfirm} from '../OlzConfirmationDialog/OlzConfirmationDialog';

import './OlzEditModal.scss';

export type OlzEditModalStatus = {id: 'IDLE'}
    |{id: 'LOADING'}
    |{id: 'WAITING_FOR_CAPTCHA'}
    |{id: 'SUBMITTING'}
    |{id: 'SUBMITTED', message?: string}
    |{id: 'SUBMIT_FAILED', message: string}
    |{id: 'DELETING'}
    |{id: 'DELETED', message?: string}
    |{id: 'DELETE_FAILED', message: string};

interface OlzEditModalProps {
    modalId: string;
    dialogTitle: React.ReactNode;
    children: React.ReactNode;
    status: OlzEditModalStatus;
    submitLabel?: string;
    onSubmit: React.FormEventHandler<HTMLFormElement>;
    onDelete?: () => unknown;
}

const IS_PLEASE_WAIT_STATUS: {[status in OlzEditModalStatus['id']]?: true} = {
    'LOADING': true,
    'WAITING_FOR_CAPTCHA': true,
    'SUBMITTING': true,
    'DELETING': true,
};

const IS_SUBMIT_DISABLED_BY_STATUS: {[status in OlzEditModalStatus['id']]?: true} = {
    'LOADING': true,
    'SUBMITTING': true,
    'SUBMITTED': true,
    'DELETING': true,
    'DELETED': true,
};

export const OlzEditModal = (props: OlzEditModalProps): React.ReactElement => {
    const submitLabel = props.submitLabel ?? 'Speichern';
    const isPleaseWait = IS_PLEASE_WAIT_STATUS[props.status.id] ?? false;
    const successMessage = props.status.id === 'SUBMITTED'
        ? props.status.message ?? 'Änderung erfolgreich. Bitte warten...'
        : (props.status.id === 'DELETED'
            ? props.status.message ?? 'Löschen erfolgreich. Bitte warten...'
            : '');
    const errorMessage = props.status.id === 'SUBMIT_FAILED' || props.status.id === 'DELETE_FAILED'
        ? props.status.message : '';
    const deleteButton = props.onDelete ? (
        <button
            type='button'
            id='delete-button'
            className='btn btn-danger btn-sm'
            onClick={() => olzConfirm('Wirklich löschen?').then(props.onDelete)}
        >
            <img src={`${codeHref}assets/icns/delete_white_16.svg`} className='noborder' />
            Löschen
        </button>
    ) : null;
    return (
        <div
            className='modal fade olz-edit-modal'
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
                            {deleteButton}
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
                                {successMessage}
                            </div>
                            <div className='error-message alert alert-danger' role='alert'>
                                {errorMessage}
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
                                disabled={IS_SUBMIT_DISABLED_BY_STATUS[props.status.id] ?? false}
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
