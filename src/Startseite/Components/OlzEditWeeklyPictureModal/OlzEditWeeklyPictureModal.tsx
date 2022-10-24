import * as bootstrap from 'bootstrap';
import React from 'react';
import ReactDOM from 'react-dom';
import {OlzApiResponses} from '../../../../src/Api/client';
import {OlzWeeklyPictureData} from '../../../../src/Api/client/generated_olz_api_types';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, getStringOrEmpty, getFormField, validFieldResult, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData} from '../../../Components/Common/OlzDefaultForm/OlzDefaultForm';
import {OlzImageUploader} from '../../../Components/Upload/OlzImageUploader/OlzImageUploader';

import './OlzEditWeeklyPictureModal.scss';

interface OlzEditWeeklyPictureModalProps {
    id?: number;
    data?: OlzWeeklyPictureData;
}

export const OlzEditWeeklyPictureModal = (props: OlzEditWeeklyPictureModalProps) => {
    const [text, setText] = React.useState<string>(props.data?.text ?? '');
    const [imageId, setImageId] = React.useState<string|null>(props.data?.imageId ?? null);
    const [alternativeImageId, setAlternativeImageId] = React.useState<string|null>(props.data?.alternativeImageId ?? null);

    const onSubmit = React.useCallback((event: React.FormEvent<HTMLFormElement>): boolean => {
        event.preventDefault();
        
        if (props.id) {
            console.error('Not implemented');
            // const getDataForRequestFn: GetDataForRequestFunction<'updateNews'> = (f) => {
            //     const fieldResults: OlzRequestFieldResult<'updateNews'> = {
            //         id: validFieldResult('', props.id),
            //         meta: {
            //             ownerUserId: validFieldResult('', null),
            //             ownerRoleId: validFieldResult('', null),
            //             onOff: validFieldResult('', true),
            //         },
            //         data: {
            //             author: getStringOrNull(getFormField(f, 'author')),
            //             authorUserId: validFieldResult('', null),
            //             authorRoleId: validFieldResult('', null),
            //             title: getStringOrEmpty(getFormField(f, 'title')),
            //             teaser: getStringOrEmpty(getFormField(f, 'teaser')),
            //             content: getStringOrEmpty(getFormField(f, 'content')),
            //             externalUrl: getStringOrNull(getFormField(f, 'external-url')),
            //             tags: validFieldResult('', []),
            //             terminId: validFieldResult('', null),
            //             imageIds: validFieldResult('', imageIds),
            //             fileIds: validFieldResult('', fileIds),
            //         },
            //     };
            //     if (!isFieldResultOrDictThereofValid(fieldResults)) {
            //         return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
            //     }
            //     return validFormData(getFieldResultOrDictThereofValue(fieldResults));
            // };

            // const handleUpdateResponse = (response: OlzApiResponses['updateNews']): string|void => {
            //     window.setTimeout(() => {
            //         bootstrap.Modal.getInstance(
            //             document.getElementById('edit-weekly-picture-modal')
            //         ).hide();
            //     }, 3000);
            //     return 'weekly-picture-Eintrag erfolgreich geändert. Bitte warten...';
            // }

            // olzDefaultFormSubmit(
            //     'updateNews',
            //     getDataForRequestFn,
            //     event.currentTarget,
            //     handleUpdateResponse,
            // );
        } else {
            const getDataForRequestFn: GetDataForRequestFunction<'createWeeklyPicture'> = (f) => {
                const fieldResults: OlzRequestFieldResult<'createWeeklyPicture'> = {
                    meta: {
                        ownerUserId: validFieldResult('', null),
                        ownerRoleId: validFieldResult('', null),
                        onOff: validFieldResult('', true),
                    },
                    data: {
                        text: getStringOrEmpty(getFormField(f, 'text')),
                        imageId: validFieldResult('', imageId),
                        alternativeImageId: validFieldResult('', alternativeImageId),
                    },
                };
                if (!isFieldResultOrDictThereofValid(fieldResults)) {
                    return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
                }
                return validFormData(getFieldResultOrDictThereofValue(fieldResults));
            };

            const handleCreateResponse = (response: OlzApiResponses['createWeeklyPicture']): string|void => {
                if (response.status !== 'OK') {
                    throw new Error(`Fehler beim Erstellen des weekly-picture-Eintrags: ${response.status}`);
                }
                window.setTimeout(() => {
                    bootstrap.Modal.getInstance(
                        document.getElementById('edit-weekly-picture-modal'),
                    ).hide();
                }, 3000);
                return 'weekly-picture-Eintrag erfolgreich erstellt. Bitte warten...';
            }

            olzDefaultFormSubmit(
                'createWeeklyPicture',
                getDataForRequestFn,
                event.currentTarget,
                handleCreateResponse,
            );
        }
        
        return false;
    }, [imageId, alternativeImageId]);

    return (
        <div className='modal fade' id='edit-weekly-picture-modal' tabIndex={-1} aria-labelledby='edit-weekly-picture-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={onSubmit}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-weekly-picture-modal-label'>Bild der Woche bearbeiten</h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
                            <div className='mb-3'>
                                <label htmlFor='weekly-picture-text-input'>Text</label>
                                <input
                                    type='text'
                                    name='text'
                                    value={text}
                                    onChange={e => setText(e.target.value)}
                                    className='form-control'
                                    id='weekly-picture-text-input'
                                />
                            </div>
                            <div id='weekly-picture-images-upload'>
                                <b>Bild</b>
                                <OlzImageUploader
                                    initialUploadId={imageId}
                                    onUploadIdChange={setImageId}
                                />
                            </div>
                            <div id='weekly-picture-files-upload'>
                                <b>Alternatives Bild</b>
                                <OlzImageUploader
                                    initialUploadId={alternativeImageId}
                                    onUploadIdChange={setAlternativeImageId}
                                />
                            </div>
                            <div className='success-message alert alert-success' role='alert'></div>
                            <div className='error-message alert alert-danger' role='alert'></div>
                        </div>
                        <div className='modal-footer'>
                            <button type='button' className='btn btn-secondary' data-bs-dismiss='modal'>Abbrechen</button>
                            <button type='submit' className='btn btn-primary' id='submit-button'>Speichern</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
};

export function initOlzEditWeeklyPictureModal(id?: number, data?: OlzWeeklyPictureData) {
    ReactDOM.render(
        <OlzEditWeeklyPictureModal id={id} data={data} />,
        document.getElementById('edit-weekly-picture-react-root'),
    );
    new bootstrap.Modal(
        document.getElementById('edit-weekly-picture-modal'),
        {backdrop: 'static'},
    ).show();
    return false;
}
