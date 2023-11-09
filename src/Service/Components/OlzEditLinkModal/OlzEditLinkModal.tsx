import * as bootstrap from 'bootstrap';
import React from 'react';
import {createRoot} from 'react-dom/client';
import {OlzApiResponses} from '../../../../src/Api/client';
import {OlzMetaData, OlzLinkData} from '../../../../src/Api/client/generated_olz_api_types';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, getRequired, getStringOrEmpty, getFormField, validFieldResult, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData, getInteger} from '../../../Components/Common/OlzDefaultForm/OlzDefaultForm';

import './OlzEditLinkModal.scss';

interface OlzEditLinkModalProps {
    id?: number;
    meta?: OlzMetaData;
    data?: OlzLinkData;
}

export const OlzEditLinkModal = (props: OlzEditLinkModalProps): React.ReactElement => {
    const [name, setName] = React.useState<string>(props.data?.name ?? '');
    const [position, setPosition] = React.useState<string>(props.data?.position !== undefined ? String(props.data.position) : '');
    const [url, setUrl] = React.useState<string>(props.data?.url ?? '');

    const onSubmit = React.useCallback(async (event: React.FormEvent<HTMLFormElement>): Promise<boolean> => {
        event.preventDefault();
        const form = event.currentTarget;

        if (props.id) {
            const getDataForRequestFn: GetDataForRequestFunction<'updateLink'> = (f) => {
                const fieldResults: OlzRequestFieldResult<'updateLink'> = {
                    id: getRequired(validFieldResult('', props.id)),
                    meta: {
                        ownerUserId: validFieldResult('', null),
                        ownerRoleId: validFieldResult('', null),
                        onOff: validFieldResult('', true),
                    },
                    data: {
                        name: getStringOrEmpty(getFormField(f, 'name')),
                        position: getInteger(getFormField(f, 'position')),
                        url: getStringOrEmpty(getFormField(f, 'url')),
                    },
                };
                if (!isFieldResultOrDictThereofValid(fieldResults)) {
                    return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
                }
                return validFormData(getFieldResultOrDictThereofValue(fieldResults));
            };

            const handleUpdateResponse = (_response: OlzApiResponses['updateLink']): string|void => {
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.reload();
                }, 1000);
                return 'Link erfolgreich geändert. Bitte warten...';
            };

            olzDefaultFormSubmit(
                'updateLink',
                getDataForRequestFn,
                form,
                handleUpdateResponse,
            );
        } else {
            const getDataForRequestFn: GetDataForRequestFunction<'createLink'> = (f) => {
                const fieldResults: OlzRequestFieldResult<'createLink'> = {
                    meta: {
                        ownerUserId: validFieldResult('', null),
                        ownerRoleId: validFieldResult('', null),
                        onOff: validFieldResult('', true),
                    },
                    data: {
                        name: getStringOrEmpty(getFormField(f, 'name')),
                        position: getInteger(getFormField(f, 'position')),
                        url: getStringOrEmpty(getFormField(f, 'url')),
                    },
                };
                if (!isFieldResultOrDictThereofValid(fieldResults)) {
                    return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
                }
                return validFormData(getFieldResultOrDictThereofValue(fieldResults));
            };

            const handleCreateResponse = (response: OlzApiResponses['createLink']): string|void => {
                if (response.status === 'ERROR') {
                    throw new Error(`Fehler beim Erstellen des Links: ${response.status}`);
                } else if (response.status !== 'OK') {
                    throw new Error(`Antwort: ${response.status}`);
                }
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.reload();
                }, 1000);
                return 'Link erfolgreich erstellt. Bitte warten...';
            };

            olzDefaultFormSubmit(
                'createLink',
                getDataForRequestFn,
                form,
                handleCreateResponse,
            );
        }

        return false;
    }, []);

    const dialogTitle = props.id === undefined ? 'Link erstellen' : 'Link bearbeiten';

    return (
        <div className='modal fade' id='edit-link-modal' tabIndex={-1} aria-labelledby='edit-link-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={onSubmit}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-link-modal-label'>
                                {dialogTitle}
                            </h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
                            <div className='mb-3'>
                                <label htmlFor='link-name-input'>Name (--- für Trennlinie)</label>
                                <input
                                    type='text'
                                    name='name'
                                    value={name}
                                    onChange={(e) => setName(e.target.value)}
                                    className='form-control'
                                    id='link-name-input'
                                />
                            </div>
                            <div className='mb-3'>
                                <label htmlFor='link-position-input'>Position</label>
                                <input
                                    type='text'
                                    name='position'
                                    value={position}
                                    onChange={(e) => setPosition(e.target.value)}
                                    className='form-control'
                                    id='link-position-input'
                                />
                            </div>
                            <div className='mb-3'>
                                <label htmlFor='link-url-input'>URL (--- für Trennlinie)</label>
                                <input
                                    type='text'
                                    name='url'
                                    value={url}
                                    onChange={(e) => setUrl(e.target.value)}
                                    className='form-control'
                                    id='link-url-input'
                                />
                            </div>
                            <div className='success-message alert alert-success' role='alert'></div>
                            <div className='error-message alert alert-danger' role='alert'></div>
                        </div>
                        <div className='modal-footer'>
                            <button type='button' className='btn btn-secondary' data-bs-dismiss='modal'>Abbrechen</button>
                            <button
                                type='submit'
                                className='btn btn-primary'
                                id='submit-button'
                            >
                                Speichern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
};

let editLinkModalRoot: ReturnType<typeof createRoot>|null = null;

export function initOlzEditLinkModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzLinkData,
): boolean {
    const rootElem = document.getElementById('edit-link-react-root');
    if (!rootElem) {
        return false;
    }
    if (editLinkModalRoot) {
        editLinkModalRoot.unmount();
    }
    editLinkModalRoot = createRoot(rootElem);
    editLinkModalRoot.render(
        <OlzEditLinkModal
            id={id}
            meta={meta}
            data={data}
        />,
    );
    window.setTimeout(() => {
        const modal = document.getElementById('edit-link-modal');
        if (modal) {
            new bootstrap.Modal(modal, {backdrop: 'static'}).show();
        }
    }, 1);
    return false;
}
