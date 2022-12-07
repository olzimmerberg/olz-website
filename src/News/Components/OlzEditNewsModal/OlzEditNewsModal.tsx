import * as bootstrap from 'bootstrap';
import React from 'react';
import ReactDOM from 'react-dom';
import {OlzApiResponses} from '../../../../src/Api/client';
import {OlzNewsData, OlzNewsFormat} from '../../../../src/Api/client/generated_olz_api_types';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, getStringOrEmpty, getStringOrNull, getFormField, validFieldResult, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData} from '../../../Components/Common/OlzDefaultForm/OlzDefaultForm';
import {OlzAuthenticatedUserRoleChooser} from '../../../Components/Common/OlzAuthenticatedUserRoleChooser/OlzAuthenticatedUserRoleChooser';
import {OlzMultiFileUploader} from '../../../Components/Upload/OlzMultiFileUploader/OlzMultiFileUploader';
import {OlzMultiImageUploader} from '../../../Components/Upload/OlzMultiImageUploader/OlzMultiImageUploader';

const FORMATTING_NOTES_FOR_USERS = (<>
    <div><b>Hinweise:</b></div>
    <div><b>1. Internet-Link in Text einbauen:</b> Internet-Adresse mit 'http://' beginnen, 
    Bsp.: 'http://www.olzimmerberg.ch' wird zu  <a href='http://www.olzimmerberg.ch' className='linkext' target='blank'><b>www.olzimmerberg.ch</b></a></div>
    <div><b>2. Text mit Fettschrift hervorheben:</b> Fetten Text mit '&lt;b&gt;' beginnen und mit '&lt;/b&gt;' beenden, 
    Bsp: '&lt;b&gt;dies ist fetter Text&lt;/b&gt;' wird zu '<b>dies ist fetter Text</b>'</div>
    <div><b>3. Bilder:</b></div>
    <table><tbody>
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
    </tbody></table>
    <div><b>4. Dateien:</b></div>
    <table><tbody>
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
    </tbody></table>
</>);

interface OlzEditNewsModalProps {
    id?: number;
    data?: OlzNewsData;
}

export const OlzEditNewsModal = (props: OlzEditNewsModalProps) => {
    const [format, setFormat] = React.useState<OlzNewsFormat>(props.data?.format ?? 'aktuell');
    const [authorUserId, setAuthorUserId] = React.useState<number|null>(props.data?.authorUserId ?? null);
    const [authorRoleId, setAuthorRoleId] = React.useState<number|null>(props.data?.authorRoleId ?? null);
    const [title, setTitle] = React.useState<string>(props.data?.title ?? '');
    const [teaser, setTeaser] = React.useState<string>(props.data?.teaser ?? '');
    const [content, setContent] = React.useState<string>(props.data?.content ?? '');
    const [externalUrl, setExternalUrl] = React.useState<string>(props.data?.externalUrl ?? '');
    const [fileIds, setFileIds] = React.useState<string[]>(props.data?.fileIds ?? []);
    const [imageIds, setImageIds] = React.useState<string[]>(props.data?.imageIds ?? []);

    const onSubmit = React.useCallback((event: React.FormEvent<HTMLFormElement>): boolean => {
        event.preventDefault();
        
        if (props.id) {
            const getDataForRequestFn: GetDataForRequestFunction<'updateNews'> = (f) => {
                const fieldResults: OlzRequestFieldResult<'updateNews'> = {
                    id: validFieldResult('', props.id),
                    meta: {
                        ownerUserId: validFieldResult('', null),
                        ownerRoleId: validFieldResult('', null),
                        onOff: validFieldResult('', true),
                    },
                    data: {
                        format: validFieldResult('format', format),
                        author: validFieldResult('author', null),
                        authorUserId: validFieldResult('', authorUserId),
                        authorRoleId: validFieldResult('', authorRoleId),
                        title: getStringOrEmpty(getFormField(f, 'title')),
                        teaser: format === 'aktuell' ? getStringOrEmpty(getFormField(f, 'teaser')) : validFieldResult('teaser', ''),
                        content: format !== 'galerie' ? getStringOrEmpty(getFormField(f, 'content')) : validFieldResult('content', ''),
                        externalUrl: format === 'aktuell' ? getStringOrNull(getFormField(f, 'external-url')) : validFieldResult('external-url', null),
                        tags: validFieldResult('', []),
                        terminId: validFieldResult('', null),
                        imageIds: validFieldResult('', imageIds),
                        fileIds: validFieldResult('', fileIds),
                    },
                };
                if (!isFieldResultOrDictThereofValid(fieldResults)) {
                    return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
                }
                return validFormData(getFieldResultOrDictThereofValue(fieldResults));
            };

            const handleUpdateResponse = (response: OlzApiResponses['updateNews']): string|void => {
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.reload();
                }, 3000);
                return 'News-Eintrag erfolgreich geändert. Bitte warten...';
            }

            olzDefaultFormSubmit(
                'updateNews',
                getDataForRequestFn,
                event.currentTarget,
                handleUpdateResponse,
            );
        } else {
            const getDataForRequestFn: GetDataForRequestFunction<'createNews'> = (f) => {
                const fieldResults: OlzRequestFieldResult<'createNews'> = {
                    meta: {
                        ownerUserId: validFieldResult('', null),
                        ownerRoleId: validFieldResult('', null),
                        onOff: validFieldResult('', true),
                    },
                    data: {
                        format: validFieldResult('format', format),
                        author: validFieldResult('author', null),
                        authorUserId: validFieldResult('', authorUserId),
                        authorRoleId: validFieldResult('', authorRoleId),
                        title: getStringOrEmpty(getFormField(f, 'title')),
                        teaser: format === 'aktuell' ? getStringOrEmpty(getFormField(f, 'teaser')) : validFieldResult('teaser', ''),
                        content: format !== 'galerie' ? getStringOrEmpty(getFormField(f, 'content')) : validFieldResult('content', ''),
                        externalUrl: format === 'aktuell' ? getStringOrNull(getFormField(f, 'external-url')) : validFieldResult('external-url', null),
                        tags: validFieldResult('', []),
                        terminId: validFieldResult('', null),
                        imageIds: validFieldResult('', imageIds),
                        fileIds: validFieldResult('', fileIds),
                    },
                };
                if (!isFieldResultOrDictThereofValid(fieldResults)) {
                    return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
                }
                return validFormData(getFieldResultOrDictThereofValue(fieldResults));
            };

            const handleCreateResponse = (response: OlzApiResponses['createNews']): string|void => {
                if (response.status !== 'OK') {
                    throw new Error(`Fehler beim Erstellen des News-Eintrags: ${response.status}`);
                }
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.reload();
                }, 3000);
                return 'News-Eintrag erfolgreich erstellt. Bitte warten...';
            }

            olzDefaultFormSubmit(
                'createNews',
                getDataForRequestFn,
                event.currentTarget,
                handleCreateResponse,
            );
        }
        
        return false;
    }, [authorUserId, authorRoleId, fileIds, imageIds]);

    return (
        <div className='modal fade' id='edit-news-modal' tabIndex={-1} aria-labelledby='edit-news-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={onSubmit}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-news-modal-label'>News bearbeiten</h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
                            <div className='row'>
                                <div className='col mb-3'>
                                    <label htmlFor='news-author-input'>Autor</label>
                                    <div id='news-author-input'>
                                        <OlzAuthenticatedUserRoleChooser
                                            userId={authorUserId}
                                            roleId={authorRoleId}
                                            onUserIdChange={e => setAuthorUserId(e.detail)}  
                                            onRoleIdChange={e => setAuthorRoleId(e.detail)}  
                                        />
                                    </div>
                                </div>
                                <div className='col mb-3'>
                                    <label htmlFor='news-format-input'>Format</label>
                                    <select
                                        name='format'
                                        className='form-control form-select'
                                        id='news-format-input'
                                        onChange={(e) => {
                                            const select = e.target;
                                            const newFormatString = select.options[select.selectedIndex].value;
                                            let newFormat: OlzNewsFormat = 'aktuell';
                                            if (newFormatString === 'galerie') {
                                                newFormat = newFormatString;
                                            }
                                            setFormat(newFormat);
                                        }}
                                    >
                                        <option value='aktuell' selected={format === 'aktuell'}>
                                            Aktuell
                                        </option>
                                        <option value='galerie' selected={format === 'galerie'}>
                                            Galerie
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div className='mb-3'>
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
                            {format === 'aktuell' ? (
                                <div className='mb-3'>
                                    <label htmlFor='news-teaser-input'>Teaser</label>
                                    <textarea
                                        name='teaser'
                                        value={teaser}
                                        onChange={e => setTeaser(e.target.value)}
                                        className='form-control'
                                        id='news-teaser-input'
                                    />
                                </div>
                            ) : null}
                            {format !== 'galerie' ? (
                                <div className='mb-3'>
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
                            ) : null}
                            {format === 'aktuell' ? (
                                <div className='mb-3'>
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
                            ) : null}
                            <div id='news-images-upload'>
                                <b>Bilder</b>
                                <OlzMultiImageUploader
                                    initialUploadIds={imageIds}
                                    onUploadIdsChange={setImageIds}
                                />
                            </div>
                            {format !== 'galerie' ? (
                                <div id='news-files-upload'>
                                    <b>Dateien</b>
                                    <OlzMultiFileUploader
                                        initialUploadIds={fileIds}
                                        onUploadIdsChange={setFileIds}
                                    />
                                </div>
                            ) : null}
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

export function initOlzEditNewsModal(id?: number, data?: OlzNewsData) {
    ReactDOM.render(
        <OlzEditNewsModal id={id} data={data} />,
        document.getElementById('edit-news-react-root'),
    );
    new bootstrap.Modal(
        document.getElementById('edit-news-modal'),
        {backdrop: 'static'},
    ).show();
    return false;
}
