import * as bootstrap from 'bootstrap';
import React from 'react';
import ReactDOM from 'react-dom';
import {OlzApiResponses} from '../../../../src/Api/client';
import {OlzMetaData, OlzNewsData, OlzNewsFormat} from '../../../../src/Api/client/generated_olz_api_types';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, getRequired, getStringOrEmpty, getStringOrNull, getFormField, validFieldResult, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData} from '../../../Components/Common/OlzDefaultForm/OlzDefaultForm';
import {OlzAuthenticatedUserRoleChooser} from '../../../Components/Common/OlzAuthenticatedUserRoleChooser/OlzAuthenticatedUserRoleChooser';
import {OlzMultiFileUploader} from '../../../Components/Upload/OlzMultiFileUploader/OlzMultiFileUploader';
import {OlzMultiImageUploader} from '../../../Components/Upload/OlzMultiImageUploader/OlzMultiImageUploader';
import {loadRecaptchaToken, loadRecaptcha} from '../../../Utils/recaptchaUtils';

type OlzEditNewsModalMode = 'anonymous'|'account';

interface OlzEditNewsModalConfig {
    name: string;
    hasFreeFormAuthor: boolean;
    hasTeaser: boolean;
    hasContent: boolean;
    contentLabel: string;
    hasFormattingNotes: boolean;
    hasExternalLink: boolean;
    hasImages: boolean;
    hasFiles: boolean;
    hasCaptcha: boolean;
}

const DEFAULT_CONFIG: OlzEditNewsModalConfig = {
    name: '?',
    hasFreeFormAuthor: false,
    hasTeaser: true,
    hasContent: true,
    contentLabel: 'Inhalt',
    hasFormattingNotes: true,
    hasExternalLink: true,
    hasImages: true,
    hasFiles: true,
    hasCaptcha: false,
};

const CONFIG_BY_FORMAT: {[format in OlzNewsFormat]: OlzEditNewsModalConfig} = {
    aktuell: {
        name: 'Aktuell',
        hasFreeFormAuthor: false,
        hasTeaser: true,
        hasContent: true,
        contentLabel: 'Inhalt',
        hasFormattingNotes: true,
        hasExternalLink: true,
        hasImages: true,
        hasFiles: true,
        hasCaptcha: false,
    },
    forum: {
        name: 'Forum',
        hasFreeFormAuthor: false,
        hasTeaser: false,
        hasContent: true,
        contentLabel: 'Dein Beitrag',
        hasFormattingNotes: false,
        hasExternalLink: false,
        hasImages: true,
        hasFiles: false,
        hasCaptcha: false,
    },
    galerie: {
        name: 'Galerie',
        hasFreeFormAuthor: false,
        hasTeaser: false,
        hasContent: false,
        contentLabel: 'Inhalt',
        hasFormattingNotes: false,
        hasExternalLink: false,
        hasImages: true,
        hasFiles: false,
        hasCaptcha: false,
    },
    video: {
        name: 'Video',
        hasFreeFormAuthor: false,
        hasTeaser: false,
        hasContent: true,
        contentLabel: 'YouTube URL',
        hasFormattingNotes: false,
        hasExternalLink: false,
        hasImages: true,
        hasFiles: false,
        hasCaptcha: false,
    },
    anonymous: {
        name: 'Forum',
        hasFreeFormAuthor: true,
        hasTeaser: false,
        hasContent: true,
        contentLabel: 'Dein Beitrag',
        hasFormattingNotes: false,
        hasExternalLink: false,
        hasImages: false,
        hasFiles: false,
        hasCaptcha: true,
    },
};

const isValidFromat = (value: unknown): value is OlzNewsFormat =>
    CONFIG_BY_FORMAT[value as OlzNewsFormat] !== undefined;

const FORMATS_BY_MODE: {[mode in OlzEditNewsModalMode]: OlzNewsFormat[]} = {
    anonymous: ['anonymous'],
    account: ['forum', 'aktuell', 'galerie', 'video'],
};

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
    mode: OlzEditNewsModalMode;
    id?: number;
    meta?: OlzMetaData;
    data?: OlzNewsData;
}

export const OlzEditNewsModal = (props: OlzEditNewsModalProps): React.ReactElement => {
    const availableFormats = FORMATS_BY_MODE[props.mode];
    const defaultFormat = availableFormats[0];

    const [format, setFormat] = React.useState<OlzNewsFormat>(props.data?.format ?? defaultFormat);
    const [authorUserId, setAuthorUserId] = React.useState<number|null>(props.data?.authorUserId ?? null);
    const [authorRoleId, setAuthorRoleId] = React.useState<number|null>(props.data?.authorRoleId ?? null);
    const [authorName, setAuthorName] = React.useState<string|null>(props.data?.authorName ?? null);
    const [authorEmail, setAuthorEmail] = React.useState<string|null>(props.data?.authorEmail ?? null);
    const [title, setTitle] = React.useState<string>(props.data?.title ?? '');
    const [teaser, setTeaser] = React.useState<string>(props.data?.teaser ?? '');
    const [content, setContent] = React.useState<string>(props.data?.content ?? '');
    const [externalUrl, setExternalUrl] = React.useState<string>(props.data?.externalUrl ?? '');
    const [fileIds, setFileIds] = React.useState<string[]>(props.data?.fileIds ?? []);
    const [imageIds, setImageIds] = React.useState<string[]>(props.data?.imageIds ?? []);
    const [recaptchaConsentGiven, setRecaptchaConsentGiven] = React.useState<boolean>(false);
    const [isWaitingForCaptcha, setIsWaitingForCaptcha] = React.useState<boolean>(false);

    React.useEffect(() => {
        if (!recaptchaConsentGiven) {
            return;
        }
        setIsWaitingForCaptcha(true);
        loadRecaptcha().then(() => {
            window.setTimeout(() => {
                setIsWaitingForCaptcha(false);
            }, 1100);
        });
    }, [recaptchaConsentGiven]);

    const config = CONFIG_BY_FORMAT[format] ?? DEFAULT_CONFIG;

    const onSubmit = React.useCallback(async (event: React.FormEvent<HTMLFormElement>): Promise<boolean> => {
        event.preventDefault();
        const form = event.currentTarget;

        let token: string|null = null;
        if (config.hasCaptcha && recaptchaConsentGiven) {
            token = await loadRecaptchaToken();
        }

        if (props.id) {
            const getDataForRequestFn: GetDataForRequestFunction<'updateNews'> = (f) => {
                const fieldResults: OlzRequestFieldResult<'updateNews'> = {
                    id: getRequired(validFieldResult('', props.id)),
                    meta: {
                        ownerUserId: validFieldResult('', null),
                        ownerRoleId: validFieldResult('', null),
                        onOff: validFieldResult('', true),
                    },
                    data: {
                        format: validFieldResult('format', format),
                        authorUserId: validFieldResult('', authorUserId),
                        authorRoleId: validFieldResult('', authorRoleId),
                        authorName: validFieldResult('author-name', authorName),
                        authorEmail: validFieldResult('author-email', authorEmail),
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

            const handleUpdateResponse = (_response: OlzApiResponses['updateNews']): string|void => {
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.reload();
                }, 3000);
                return 'News-Eintrag erfolgreich geändert. Bitte warten...';
            };

            olzDefaultFormSubmit(
                'updateNews',
                getDataForRequestFn,
                form,
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
                        authorUserId: validFieldResult('', authorUserId),
                        authorRoleId: validFieldResult('', authorRoleId),
                        authorName: validFieldResult('author-name', authorName),
                        authorEmail: validFieldResult('author-email', authorEmail),
                        title: getStringOrEmpty(getFormField(f, 'title')),
                        teaser: format === 'aktuell' ? getStringOrEmpty(getFormField(f, 'teaser')) : validFieldResult('teaser', ''),
                        content: format !== 'galerie' ? getStringOrEmpty(getFormField(f, 'content')) : validFieldResult('content', ''),
                        externalUrl: format === 'aktuell' ? getStringOrNull(getFormField(f, 'external-url')) : validFieldResult('external-url', null),
                        tags: validFieldResult('', []),
                        terminId: validFieldResult('', null),
                        imageIds: validFieldResult('', imageIds),
                        fileIds: validFieldResult('', fileIds),
                    },
                    custom: {
                        recaptchaToken: validFieldResult('', token),
                    },
                };
                if (!isFieldResultOrDictThereofValid(fieldResults)) {
                    return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
                }
                return validFormData(getFieldResultOrDictThereofValue(fieldResults));
            };

            const handleCreateResponse = (response: OlzApiResponses['createNews']): string|void => {
                if (response.status === 'ERROR') {
                    throw new Error(`Fehler beim Erstellen des News-Eintrags: ${response.status}`);
                } else if (response.status !== 'OK') {
                    throw new Error(`Antwort: ${response.status}`);
                }
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.reload();
                }, 3000);
                return 'News-Eintrag erfolgreich erstellt. Bitte warten...';
            };

            olzDefaultFormSubmit(
                'createNews',
                getDataForRequestFn,
                form,
                handleCreateResponse,
            );
        }

        return false;
    }, [config, format, authorName, authorEmail, authorUserId, authorRoleId, fileIds, imageIds, recaptchaConsentGiven]);

    const dialogTitle = props.mode === 'anonymous'
        ? 'Forumseintrag erstellen'
        : (props.id === undefined
            ? 'News-Eintrag erstellen'
            : 'News-Eintrag bearbeiten'
        );

    return (
        <div className='modal fade' id='edit-news-modal' tabIndex={-1} aria-labelledby='edit-news-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={onSubmit}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-news-modal-label'>
                                {dialogTitle}
                            </h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
                            {config.hasFreeFormAuthor ? (<>
                                <div className='row'>
                                    <div className='col mb-3'>
                                        <label htmlFor='news-author-name-input'>Autor</label>
                                        <input
                                            type='text'
                                            name='author-name'
                                            value={authorName || ''}
                                            onChange={(e) => setAuthorName(e.target.value)}
                                            className='form-control'
                                            id='news-author-name-input'
                                        />
                                    </div>
                                </div>
                                <div className='row'>
                                    <div className='col mb-3'>
                                        <label htmlFor='news-author-email-input'>E-Mail</label>
                                        <input
                                            type='text'
                                            name='author-email'
                                            value={authorEmail || ''}
                                            onChange={(e) => setAuthorEmail(e.target.value)}
                                            className='form-control'
                                            id='news-author-email-input'
                                        />
                                    </div>
                                </div>
                            </>) : (
                                <div className='row'>
                                    <div className='col mb-3'>
                                        <label htmlFor='news-author-input'>Autor</label>
                                        <div id='news-author-input'>
                                            <OlzAuthenticatedUserRoleChooser
                                                nullLabel={authorUserId ? '(unverändert)' : 'Bitte wählen...'}
                                                userId={authorUserId}
                                                roleId={authorRoleId}
                                                onUserIdChange={(e) => setAuthorUserId(e.detail)}
                                                onRoleIdChange={(e) => setAuthorRoleId(e.detail)}
                                            />
                                        </div>
                                    </div>
                                    {availableFormats.length > 1 ? (
                                        <div className='col mb-3'>
                                            <label htmlFor='news-format-input'>Format</label>
                                            <select
                                                name='format'
                                                className='form-control form-select'
                                                id='news-format-input'
                                                defaultValue={format}
                                                onChange={(e) => {
                                                    const select = e.target;
                                                    const newFormatString = select.options[select.selectedIndex].value;
                                                    let newFormat: OlzNewsFormat = defaultFormat;
                                                    if (isValidFromat(newFormatString)) {
                                                        newFormat = newFormatString;
                                                    }
                                                    setFormat(newFormat);
                                                }}
                                            >
                                                {availableFormats.map((formatOption) => (
                                                    <option value={formatOption}>
                                                        {CONFIG_BY_FORMAT[formatOption].name}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                    ) : null}
                                </div>
                            )}
                            <div className='mb-3'>
                                <label htmlFor='news-title-input'>Titel</label>
                                <input
                                    type='text'
                                    name='title'
                                    value={title}
                                    onChange={(e) => setTitle(e.target.value)}
                                    className='form-control'
                                    id='news-title-input'
                                />
                            </div>
                            {config.hasTeaser ? (
                                <div className='mb-3'>
                                    <label htmlFor='news-teaser-input'>Teaser</label>
                                    <textarea
                                        name='teaser'
                                        value={teaser}
                                        onChange={(e) => setTeaser(e.target.value)}
                                        className='form-control'
                                        id='news-teaser-input'
                                    />
                                </div>
                            ) : null}
                            {config.hasContent ? (
                                <div className='mb-3'>
                                    <label htmlFor='news-content-input'>{config.contentLabel}</label>
                                    <textarea
                                        name='content'
                                        value={content}
                                        onChange={(e) => setContent(e.target.value)}
                                        className='form-control'
                                        id='news-content-input'
                                    />
                                    {config.hasFormattingNotes ? FORMATTING_NOTES_FOR_USERS : ''}
                                </div>
                            ) : null}
                            {config.hasExternalLink ? (
                                <div className='mb-3'>
                                    <label htmlFor='news-external-url-input'>Externer Link</label>
                                    <input
                                        type='text'
                                        name='external-url'
                                        value={externalUrl}
                                        onChange={(e) => setExternalUrl(e.target.value)}
                                        className='form-control'
                                        id='news-external-url-input'
                                    />
                                </div>
                            ) : null}
                            {config.hasImages ? (
                                <div id='news-images-upload'>
                                    <b>Bilder</b>
                                    <OlzMultiImageUploader
                                        initialUploadIds={imageIds}
                                        onUploadIdsChange={setImageIds}
                                    />
                                </div>
                            ) : null}
                            {config.hasFiles ? (
                                <div id='news-files-upload'>
                                    <b>Dateien</b>
                                    <OlzMultiFileUploader
                                        initialUploadIds={fileIds}
                                        onUploadIdsChange={setFileIds}
                                    />
                                </div>
                            ) : null}
                            {config.hasCaptcha ? (
                                <p>
                                    <input
                                        type='checkbox'
                                        name='recaptcha-consent-given'
                                        value='yes'
                                        checked={recaptchaConsentGiven}
                                        onChange={(e) => setRecaptchaConsentGiven(e.target.checked)}
                                        id='news-recaptcha-consent-given-input'
                                    />
                                    &nbsp;
                                    <span className='required-field-asterisk'>*</span>
                                    Ich akzeptiere, dass beim Erstellen des Kontos einmalig Google reCaptcha verwendet wird, um Bot-Spam zu verhinden.
                                </p>
                            ) : null}
                            <p>
                                <span className='required-field-asterisk'>*</span>
                                {' Mit dem Speichern erklärst du dich mit den '}
                                <a href='fragen_und_antworten.php#forumsregeln' target='_blank'>
                                    Forumsregeln
                                </a>
                                {' einverstanden'}
                            </p>
                            <div className='success-message alert alert-success' role='alert'></div>
                            <div className='error-message alert alert-danger' role='alert'></div>
                        </div>
                        <div className='modal-footer'>
                            <button type='button' className='btn btn-secondary' data-bs-dismiss='modal'>Abbrechen</button>
                            <button
                                type='submit'
                                className={isWaitingForCaptcha ? 'btn btn-secondary' : 'btn btn-primary'}
                                id='submit-button'
                            >
                                {isWaitingForCaptcha ? 'Bitte warten...' : 'Speichern'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
};

export function initOlzEditNewsModal(
    mode: OlzEditNewsModalMode,
    id?: number,
    meta?: OlzMetaData,
    data?: OlzNewsData,
): boolean {
    ReactDOM.render(
        <OlzEditNewsModal
            mode={mode}
            id={id}
            meta={meta}
            data={data}
        />,
        document.getElementById('edit-news-react-root'),
    );
    const modal = document.getElementById('edit-news-modal');
    if (modal) {
        new bootstrap.Modal(modal, {backdrop: 'static'}).show();
    }
    return false;
}
