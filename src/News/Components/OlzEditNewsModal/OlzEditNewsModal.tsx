import * as bootstrap from 'bootstrap';
import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzNewsData, OlzNewsFormat} from '../../../../src/Api/client/generated_olz_api_types';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzAuthenticatedUserRoleField} from '../../../Components/Common/OlzAuthenticatedUserRoleField/OlzAuthenticatedUserRoleField';
import {OlzMultiFileField} from '../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {OlzMultiImageField} from '../../../Components/Upload/OlzMultiImageField/OlzMultiImageField';
import {loadRecaptchaToken, loadRecaptcha} from '../../../Utils/recaptchaUtils';
import {codeHref, dataHref} from '../../../Utils/constants';
import {assert, timeout} from '../../../Utils/generalUtils';
import {initReact} from '../../../Utils/reactUtils';

import './OlzEditNewsModal.scss';

interface OlzEditNewsForm {
    format: OlzNewsFormat|undefined;
    authorUserId: number|null;
    authorRoleId: number|null;
    authorName: string|null;
    authorEmail: string|null;
    title: string;
    teaser: string;
    content: string;
    externalUrl: string;
    imageIds: string[];
    fileIds: string[];
}

const resolver: Resolver<OlzEditNewsForm> = async (values) => {
    const errors: FieldErrors<OlzEditNewsForm> = {};
    if (!values.format) {
        errors.format = {type: 'required', message: 'Darf nicht leer sein.'};
    }
    return {
        values: Object.keys(errors).length > 0 ? {} : values,
        errors,
    };
};

function getFormFromApi(apiData?: OlzNewsData): OlzEditNewsForm {
    return {
        format: apiData?.format,
        authorUserId: apiData?.authorUserId ?? null,
        authorRoleId: apiData?.authorRoleId ?? null,
        authorName: apiData?.authorName ?? null,
        authorEmail: apiData?.authorEmail ?? null,
        title: apiData?.title ?? '',
        teaser: apiData?.teaser ?? '',
        content: apiData?.content ?? '',
        externalUrl: apiData?.externalUrl ?? '',
        imageIds: apiData?.imageIds ?? [],
        fileIds: apiData?.fileIds ?? [],
    };
}

function getApiFromForm(formData: OlzEditNewsForm): OlzNewsData {
    return {
        format: assert(formData?.format),
        authorUserId: formData?.authorUserId,
        authorRoleId: formData?.authorRoleId,
        authorName: formData?.authorName,
        authorEmail: formData?.authorEmail,
        title: formData?.title,
        teaser: formData?.format === 'aktuell' ? formData?.teaser : '',
        content: formData?.format !== 'galerie' ? formData?.content : '',
        externalUrl: formData?.format === 'aktuell' ? (formData?.externalUrl || null) : null,
        tags: [],
        terminId: null,
        imageIds: formData?.imageIds,
        fileIds: formData?.fileIds,
    };
}

// ---

export type OlzEditNewsModalMode = 'anonymous'|'account'|'account_with_blog';

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
    kaderblog: {
        name: 'Kaderblog',
        hasFreeFormAuthor: false,
        hasTeaser: false,
        hasContent: true,
        contentLabel: 'Blogeintrag',
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

const FORMATS_BY_MODE: {[mode in OlzEditNewsModalMode]: OlzNewsFormat[]} = {
    anonymous: ['anonymous'],
    account: ['forum', 'aktuell', 'galerie', 'video'],
    account_with_blog: ['forum', 'kaderblog', 'aktuell', 'galerie', 'video'],
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
                backgroundImage: `url(${dataHref}assets/icns/link_image_16.svg)`,
                backgroundRepeat: 'no-repeat',
            }}>OL Karte</a></td>
        </tr>
        <tr>
            <td>LT61cBGv7p77I7fY1undEkwP.pdf</td>
            <td>&lt;DATEI=LT61cBGv7p77I7fY1undEkwP.pdf text=&quot;Ausschreibung als PDF&quot;&gt;</td>
            <td><a style={{
                paddingLeft: '17px',
                backgroundImage: `url(${dataHref}assets/icns/link_pdf_16.svg)`,
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
    const defaultFormat = availableFormats.length === 1 ? availableFormats[0] : undefined;

    const defaultValues = getFormFromApi(props.data);
    const {register, handleSubmit, formState: {errors}, control, watch} = useForm<OlzEditNewsForm>({
        resolver,
        defaultValues: {
            ...defaultValues,
            format: defaultValues.format ?? defaultFormat,
        },
    });

    const [isLoading, setIsLoading] = React.useState<boolean>(false);
    const [recaptchaConsentGiven, setRecaptchaConsentGiven] = React.useState<boolean>(false);
    const [isWaitingForCaptcha, setIsWaitingForCaptcha] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

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

    const format = watch('format');
    const config = format ? CONFIG_BY_FORMAT[format] : DEFAULT_CONFIG;

    const onSubmit: SubmitHandler<OlzEditNewsForm> = async (values) => {
        const meta: OlzMetaData = {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);

        let recaptchaToken: string|null = null;
        if (config.hasCaptcha && recaptchaConsentGiven) {
            recaptchaToken = await loadRecaptchaToken();
        }

        const [err, response] = await (props.id
            ? olzApi.getResult('updateNews', {id: props.id, meta, data})
            : olzApi.getResult('createNews', {meta, data, custom: {recaptchaToken}}));
        if (err || response.status !== 'OK') {
            setErrorMessage(`Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`);
            return;
        }

        // TODO: This could probably be done more smoothly!
        setSuccessMessage('Änderung erfolgreich. Bitte warten...');
        await timeout(1000);
        window.location.reload();
    };

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
                    <form className='default-form' onSubmit={handleSubmit(onSubmit)}>
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
                                        <OlzTextField
                                            title='Autor'
                                            name='authorName'
                                            errors={errors}
                                            register={register}
                                        />
                                    </div>
                                </div>
                                <div className='row'>
                                    <div className='col mb-3'>
                                        <OlzTextField
                                            title='E-Mail'
                                            name='authorEmail'
                                            errors={errors}
                                            register={register}
                                        />
                                    </div>
                                </div>
                            </>) : (
                                <div className='row'>
                                    <div className='col mb-3'>
                                        <OlzAuthenticatedUserRoleField
                                            title='Autor'
                                            userName='authorUserId'
                                            roleName='authorRoleId'
                                            errors={errors}
                                            userControl={control}
                                            roleControl={control}
                                            setIsLoading={setIsLoading}
                                            nullLabel={props.id ? '(unverändert)' : 'Bitte wählen...'}
                                        />
                                    </div>
                                    {availableFormats.length > 1 ? (
                                        <div className='col mb-3'>
                                            <label htmlFor='format-input'>Format</label>
                                            <select
                                                className='form-control form-select'
                                                id='format-input'
                                                {...register('format')}
                                                defaultValue={format ?? 'UNDEFINED'}
                                            >
                                                <option disabled value='UNDEFINED'>
                                                    Bitte wählen...
                                                </option>
                                                {availableFormats.map((formatOption) => (
                                                    <option value={formatOption} key={formatOption}>
                                                        {CONFIG_BY_FORMAT[formatOption].name}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                    ) : null}
                                </div>
                            )}
                            <div className='mb-3'>
                                <OlzTextField
                                    title='Titel'
                                    name='title'
                                    errors={errors}
                                    register={register}
                                />
                            </div>
                            {config.hasTeaser ? (
                                <div className='mb-3'>
                                    <OlzTextField
                                        mode='textarea'
                                        title='Teaser'
                                        name='teaser'
                                        errors={errors}
                                        register={register}
                                    />
                                </div>
                            ) : null}
                            {config.hasContent ? (
                                <div className='mb-3'>
                                    <OlzTextField
                                        mode='textarea'
                                        title={config.contentLabel}
                                        name='content'
                                        errors={errors}
                                        register={register}
                                    />
                                    {config.hasFormattingNotes ? FORMATTING_NOTES_FOR_USERS : ''}
                                </div>
                            ) : null}
                            {config.hasExternalLink ? (
                                <div className='mb-3'>
                                    <OlzTextField
                                        title='Externer Link'
                                        name='externalUrl'
                                        errors={errors}
                                        register={register}
                                    />
                                </div>
                            ) : null}
                            {config.hasImages ? (
                                <div id='images-upload'>
                                    <OlzMultiImageField
                                        title='Bilder'
                                        name='imageIds'
                                        errors={errors}
                                        control={control}
                                        setIsLoading={setIsLoading}
                                    />
                                </div>
                            ) : null}
                            {config.hasFiles ? (
                                <div id='files-upload'>
                                    <OlzMultiFileField
                                        title='Dateien'
                                        name='fileIds'
                                        errors={errors}
                                        control={control}
                                        setIsLoading={setIsLoading}
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
                                        id='recaptcha-consent-given-input'
                                    />
                                    &nbsp;
                                    <span className='required-field-asterisk'>*</span>
                                    Ich akzeptiere, dass beim Erstellen des Kontos einmalig Google reCaptcha verwendet wird, um Bot-Spam zu verhinden.
                                </p>
                            ) : null}
                            <p>
                                <span className='required-field-asterisk'>*</span>
                                {' Mit dem Speichern erklärst du dich mit den '}
                                <a
                                    href={`${codeHref}fragen_und_antworten#forumsregeln`}
                                    target='_blank'
                                >
                                    Forumsregeln
                                </a>
                                {' einverstanden'}
                            </p>
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
                                disabled={isLoading}
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
    initReact('edit-news-react-root', (
        <OlzEditNewsModal
            mode={mode}
            id={id}
            meta={meta}
            data={data}
        />
    ));
    window.setTimeout(() => {
        const modal = document.getElementById('edit-news-modal');
        if (modal) {
            new bootstrap.Modal(modal, {backdrop: 'static'}).show();
        }
    }, 1);
    return false;
}
