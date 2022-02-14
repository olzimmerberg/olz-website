import React from 'react';
import ReactDOM from 'react-dom';
import {OlzApiResponses} from '../../../api/client';
import {OlzNewsData} from '../../../api/client/generated_olz_api_types';
import {olzDefaultFormSubmit, GetDataForRequestDict, getFormField} from '../../../components/common/olz_default_form/olz_default_form';
import {OlzMultiFileUploader} from '../../../components/upload/OlzMultiFileUploader/OlzMultiFileUploader';
import {OlzMultiImageUploader} from '../../../components/upload/OlzMultiImageUploader/OlzMultiImageUploader';

const FORMATTING_NOTES_FOR_USERS = (<>
    <div><b>Hinweise:</b></div>
    <div><b>1. Internet-Link in Text einbauen:</b> Internet-Adresse mit 'http://' beginnen, 
    Bsp.: 'http://www.olzimmerberg.ch' wird zu  <a href='http://www.olzimmerberg.ch' className='linkext' target='blank'><b>www.olzimmerberg.ch</b></a></div>
    <div><b>2. Text mit Fettschrift hervorheben:</b> Fetten Text mit '&lt;b&gt;' beginnen und mit '&lt;/b&gt;' beenden, 
    Bsp: '&lt;b&gt;dies ist fetter Text&lt;/b&gt;' wird zu '<b>dies ist fetter Text</b>'</div>
    <div><b>3. Bilder:</b></div>
    <table>
        <tr className='tablebar'>
            <td><b>Bildnummer</b></td>
            <td><b>Wie einbinden?</b></td>
        </tr>
        <tr>
            <td>1. Bild</td>
            <td>&lt;BILD1&gt;</td>
        </tr>
        <tr>
            <td>2. Bild</td>
            <td>&lt;BILD2&gt;</td>
        </tr>
    </table>
    <div><b>4. Dateien:</b></div>
    <table>
        <tr className='tablebar'>
            <td><b>Dateiname</b></td>
            <td><b>Wie einbinden?</b></td>
            <td><b>Wie wird's aussehen?</b></td>
        </tr>
        <tr>
            <td>xTVapfgrlx4U5Mgv90tyYb6C.pdf</td>
            <td>&lt;DATEI=xTVapfgrlx4U5Mgv90tyYb6C.pdf text=&quot;OL Karte&quot;&gt;</td>
            <td><a style={{
                paddingLeft: '17px',
                backgroundImage: 'url(icns/link_image_16.svg)',
                backgroundRepeat: 'no-repeat',
            }}>OL Karte</a></td>
        </tr>
        <tr>
            <td>LT61cBGv7p77I7fY1undEkwP.pdf</td>
            <td>&lt;DATEI=LT61cBGv7p77I7fY1undEkwP.pdf text=&quot;Ausschreibung als PDF&quot;&gt;</td>
            <td><a style={{
                paddingLeft: '17px',
                backgroundImage: 'url(icns/link_pdf_16.svg)',
                backgroundRepeat: 'no-repeat',
            }}>Ausschreibung als PDF</a></td>
        </tr>
    </table>
</>);

interface OlzEditNewsModalProps {
    id?: number;
    data?: OlzNewsData;
}

export const OlzEditNewsModal = (props: OlzEditNewsModalProps) => {
    const [title, setTitle] = React.useState<string>(props.data?.title ?? '');
    const [teaser, setTeaser] = React.useState<string>(props.data?.teaser ?? '');
    const [content, setContent] = React.useState<string>(props.data?.content ?? '');
    const [author, setAuthor] = React.useState<string>(props.data?.author ?? '');
    const [externalUrl, setExternalUrl] = React.useState<string>(props.data?.externalUrl ?? '');
    const [fileIds, setFileIds] = React.useState<string[]>([]);
    const [imageIds, setImageIds] = React.useState<string[]>([]);

    const onSubmit = React.useCallback((event: React.FormEvent<HTMLFormElement>): boolean => {
        const getDataForRequestDict: GetDataForRequestDict<'createNews'> = {
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

        function handleResponse(response: OlzApiResponses['createNews']): string|void {
            if (response.status !== 'OK') {
                throw new Error(`Fehler beim Erstellen des News-Eintrags: ${response.status}`);
            }
            window.setTimeout(() => {
                $('#edit-news-modal').modal('hide');
            }, 3000);
            return 'News-Eintrag erfolgreich erstellt. Bitte warten...';
        }

        event.preventDefault();

        olzDefaultFormSubmit(
            'createNews',
            getDataForRequestDict,
            event.currentTarget,
            handleResponse,
        );
        return false;
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
                                <label htmlFor='news-title-input'>Titel</label>
                                <input
                                    type='text'
                                    name='title'
                                    value={title}
                                    onChange={e => setTitle(e.target.value)}
                                    className='form-control'
                                    id='news-title-input'
                                />
                            </div>
                            <div className='form-group'>
                                <label htmlFor='news-teaser-input'>Teaser</label>
                                <textarea
                                    name='teaser'
                                    value={teaser}
                                    onChange={e => setTeaser(e.target.value)}
                                    className='form-control'
                                    id='news-teaser-input'
                                />
                            </div>
                            <div className='form-group'>
                                <label htmlFor='news-content-input'>Inhalt</label>
                                <textarea
                                    name='content'
                                    value={content}
                                    onChange={e => setContent(e.target.value)}
                                    className='form-control'
                                    id='news-content-input'
                                />
                                {FORMATTING_NOTES_FOR_USERS}
                            </div>
                            <div className='form-group'>
                                <label htmlFor='news-author-input'>Autor</label>
                                <input
                                    type='text'
                                    name='author'
                                    value={author}
                                    onChange={e => setAuthor(e.target.value)}
                                    className='form-control'
                                    id='news-author-input'
                                />
                            </div>
                            <div className='form-group'>
                                <label htmlFor='news-external-url-input'>Externer Link</label>
                                <input
                                    type='text'
                                    name='external-url'
                                    value={externalUrl}
                                    onChange={e => setExternalUrl(e.target.value)}
                                    className='form-control'
                                    id='news-external-url-input'
                                />
                            </div>
                            <div id='news-images-upload'>
                                <b>Bilder</b>
                                <OlzMultiImageUploader
                                    onUploadIdsChange={setImageIds}
                                />
                            </div>
                            <div id='news-files-upload'>
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

export function initOlzEditNewsModal(id?: number, data?: OlzNewsData) {
    ReactDOM.render(
        <OlzEditNewsModal id={id} data={data} />,
        document.getElementById('edit-news-react-root'),
    );
    $('#edit-news-modal').modal({backdrop: 'static'});
    return false;
}
