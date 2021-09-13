import React from 'react';
import ReactDOM from 'react-dom';
import {OlzApiEndpoint, OlzApiResponses} from '../../../api/client';
import {olzDefaultFormSubmit, GetDataForRequestDict, getFormField} from '../../../components/common/olz_default_form/olz_default_form';
import {OlzMultiFileUploader} from '../../../components/upload/OlzMultiFileUploader/OlzMultiFileUploader';
import {OlzMultiImageUploader} from '../../../components/upload/OlzMultiImageUploader/OlzMultiImageUploader';

export const OlzEditNewsModal = () => {
    const [fileIds, setFileIds] = React.useState<string[]>([]);
    const [imageIds, setImageIds] = React.useState<string[]>([]);

    const onSubmit = React.useCallback((event: React.FormEvent<HTMLFormElement>): boolean => {
        const getDataForRequestDict: GetDataForRequestDict<OlzApiEndpoint.createNews> = {
            ownerUserId: () => null,
            ownerRoleId: () => null,
            author: (f) => getFormField(f, 'author'),
            authorUserId: () => null,
            authorRoleId: () => null,
            title: (f) => getFormField(f, 'title'),
            teaser: (f) => getFormField(f, 'teaser'),
            content: (f) => getFormField(f, 'content'),
            externalUrl: (f) => getFormField(f, 'external-url') || null,
            tags: () => [],
            terminId: () => null,
            onOff: () => true,
            imageIds: () => imageIds,
            fileIds: () => fileIds,
        };

        function handleResponse(response: OlzApiResponses[OlzApiEndpoint.createNews]): string|void {
            if (response.status !== 'OK') {
                throw new Error(`Fehler beim Erstellen des News-Eintrags: ${response.status}`);
            }
            window.setTimeout(() => {
                $('#edit-news-modal').modal('hide');
            }, 3000);
            return 'News-Eintrag erfolgreich erstellt. Bitte warten...';
        }

        event.preventDefault();

        return olzDefaultFormSubmit(
            OlzApiEndpoint.createNews,
            getDataForRequestDict,
            event.currentTarget,
            handleResponse,
        );
    }, [fileIds, imageIds]);

    return (
        <div className='modal fade' id='edit-news-modal' tabIndex={-1} aria-labelledby='edit-news-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={onSubmit}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-news-modal-label'>News bearbeiten</h5>
                            <button type='button' className='close' data-dismiss='modal' aria-label='Schliessen'>
                                <span aria-hidden='true'>&times;</span>
                            </button>
                        </div>
                        <div className='modal-body'>
                            <div className='form-group'>
                                <label htmlFor='news-date-input'>Datum</label>
                                <input
                                    type='text'
                                    name='date'
                                    className='form-control'
                                    id='news-date-input'
                                />
                            </div>
                            <div className='form-group'>
                                <label htmlFor='news-title-input'>Titel</label>
                                <input
                                    type='text'
                                    name='title'
                                    className='form-control'
                                    id='news-title-input'
                                />
                            </div>
                            <div className='form-group'>
                                <label htmlFor='news-teaser-input'>Teaser</label>
                                <textarea
                                    name='teaser'
                                    className='form-control'
                                    id='news-teaser-input'
                                />
                            </div>
                            <div className='form-group'>
                                <label htmlFor='news-content-input'>Inhalt</label>
                                <textarea
                                    name='content'
                                    className='form-control'
                                    id='news-content-input'
                                />
                            </div>
                            <div className='form-group'>
                                <label htmlFor='news-author-input'>Autor</label>
                                <input
                                    type='text'
                                    name='author'
                                    className='form-control'
                                    id='news-author-input'
                                />
                            </div>
                            <div className='form-group'>
                                <label htmlFor='news-external-url-input'>Externer Link</label>
                                <input
                                    type='text'
                                    name='external-url'
                                    className='form-control'
                                    id='news-external-url-input'
                                />
                            </div>
                            <div>
                                <b>Bilder</b>
                                <OlzMultiImageUploader
                                    onUploadIdsChange={setImageIds}
                                />
                            </div>
                            <div>
                                <b>Dateien</b>
                                <OlzMultiFileUploader
                                    onUploadIdsChange={setFileIds}
                                />
                            </div>
                            <div className='success-message alert alert-success' role='alert'></div>
                            <div className='error-message alert alert-danger' role='alert'></div>
                        </div>
                        <div className='modal-footer'>
                            <button type='button' className='btn btn-secondary' data-dismiss='modal'>Abbrechen</button>
                            <button type='submit' className='btn btn-primary' id='submit-button'>Speichern</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
};

export function initOlzEditNewsModal() {
    ReactDOM.render(
        <OlzEditNewsModal />,
        document.getElementById('edit-news-react-root'),
    );
    $('#edit-news-modal').modal({backdrop: 'static'});
    return false;
}
