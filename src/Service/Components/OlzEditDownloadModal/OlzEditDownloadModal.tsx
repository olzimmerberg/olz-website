import * as bootstrap from 'bootstrap';
import React from 'react';
import {createRoot} from 'react-dom/client';
import {OlzApiResponses} from '../../../../src/Api/client';
import {OlzMetaData, OlzDownloadData} from '../../../../src/Api/client/generated_olz_api_types';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, getRequired, getStringOrEmpty, getFormField, validFieldResult, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData, getInteger} from '../../../Components/Common/OlzDefaultForm/OlzDefaultForm';
import {OlzMultiFileUploader} from '../../../Components/Upload/OlzMultiFileUploader/OlzMultiFileUploader';

import './OlzEditDownloadModal.scss';

interface OlzEditDownloadModalProps {
    id?: number;
    meta?: OlzMetaData;
    data?: OlzDownloadData;
}

export const OlzEditDownloadModal = (props: OlzEditDownloadModalProps): React.ReactElement => {
    const [name, setName] = React.useState<string>(props.data?.name ?? '');
    const [position, setPosition] = React.useState<string>(props.data?.position !== undefined ? String(props.data.position) : '');
    const [fileIds, setFileIds] = React.useState<string[]>(props.data?.fileId ? [props.data.fileId] : []);

    const onSubmit = React.useCallback(async (event: React.FormEvent<HTMLFormElement>): Promise<boolean> => {
        event.preventDefault();
        const form = event.currentTarget;

        if (props.id) {
            const getDataForRequestFn: GetDataForRequestFunction<'updateDownload'> = (f) => {
                const fieldResults: OlzRequestFieldResult<'updateDownload'> = {
                    id: getRequired(validFieldResult('', props.id)),
                    meta: {
                        ownerUserId: validFieldResult('', null),
                        ownerRoleId: validFieldResult('', null),
                        onOff: validFieldResult('', true),
                    },
                    data: {
                        name: getStringOrEmpty(getFormField(f, 'name')),
                        position: getInteger(getFormField(f, 'position')),
                        fileId: getRequired(validFieldResult('', fileIds[0])),
                    },
                };
                if (!isFieldResultOrDictThereofValid(fieldResults)) {
                    return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
                }
                return validFormData(getFieldResultOrDictThereofValue(fieldResults));
            };

            const handleUpdateResponse = (_response: OlzApiResponses['updateDownload']): string|void => {
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.reload();
                }, 1000);
                return 'Download erfolgreich geändert. Bitte warten...';
            };

            olzDefaultFormSubmit(
                'updateDownload',
                getDataForRequestFn,
                form,
                handleUpdateResponse,
            );
        } else {
            const getDataForRequestFn: GetDataForRequestFunction<'createDownload'> = (f) => {
                const fieldResults: OlzRequestFieldResult<'createDownload'> = {
                    meta: {
                        ownerUserId: validFieldResult('', null),
                        ownerRoleId: validFieldResult('', null),
                        onOff: validFieldResult('', true),
                    },
                    data: {
                        name: getStringOrEmpty(getFormField(f, 'name')),
                        position: getInteger(getFormField(f, 'position')),
                        fileId: getRequired(validFieldResult('', fileIds[0])),
                    },
                };
                if (!isFieldResultOrDictThereofValid(fieldResults)) {
                    return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
                }
                return validFormData(getFieldResultOrDictThereofValue(fieldResults));
            };

            const handleCreateResponse = (response: OlzApiResponses['createDownload']): string|void => {
                if (response.status === 'ERROR') {
                    throw new Error(`Fehler beim Erstellen des Downloads: ${response.status}`);
                } else if (response.status !== 'OK') {
                    throw new Error(`Antwort: ${response.status}`);
                }
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.reload();
                }, 1000);
                return 'Download erfolgreich erstellt. Bitte warten...';
            };

            olzDefaultFormSubmit(
                'createDownload',
                getDataForRequestFn,
                form,
                handleCreateResponse,
            );
        }

        return false;
    }, [fileIds]);

    const dialogTitle = props.id === undefined ? 'Download erstellen' : 'Download bearbeiten';

    return (
        <div className='modal fade' id='edit-download-modal' tabIndex={-1} aria-labelledby='edit-download-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={onSubmit}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-download-modal-label'>
                                {dialogTitle}
                            </h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
                            <div className='mb-3'>
                                <label htmlFor='download-name-input'>Name (--- für Trennlinie)</label>
                                <input
                                    type='text'
                                    name='name'
                                    value={name}
                                    onChange={(e) => setName(e.target.value)}
                                    className='form-control'
                                    id='download-name-input'
                                />
                            </div>
                            <div className='mb-3'>
                                <label htmlFor='download-position-input'>Position</label>
                                <input
                                    type='text'
                                    name='position'
                                    value={position}
                                    onChange={(e) => setPosition(e.target.value)}
                                    className='form-control'
                                    id='download-position-input'
                                />
                            </div>
                            <div id='download-file-upload'>
                                <b>Dateien</b>
                                <OlzMultiFileUploader
                                    initialUploadIds={fileIds}
                                    onUploadIdsChange={setFileIds}
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

let editDownloadModalRoot: ReturnType<typeof createRoot>|null = null;

export function initOlzEditDownloadModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzDownloadData,
): boolean {
    const rootElem = document.getElementById('edit-download-react-root');
    if (!rootElem) {
        return false;
    }
    if (editDownloadModalRoot) {
        editDownloadModalRoot.unmount();
    }
    editDownloadModalRoot = createRoot(rootElem);
    editDownloadModalRoot.render(
        <OlzEditDownloadModal
            id={id}
            meta={meta}
            data={data}
        />,
    );
    window.setTimeout(() => {
        const modal = document.getElementById('edit-download-modal');
        if (modal) {
            new bootstrap.Modal(modal, {backdrop: 'static'}).show();
        }
    }, 1);
    return false;
}
