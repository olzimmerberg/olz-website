import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzNewsData, OlzNewsFormat} from '../../../Api/client/generated_olz_api_types';
import {MARKDOWN_NOTICE, initOlzEditModal, OlzEditModal, OlzEditModalStatus} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzAuthenticatedUserRoleField} from '../../../Components/Common/OlzAuthenticatedUserRoleField/OlzAuthenticatedUserRoleField';
import {OlzMultiFileField} from '../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {OlzMultiImageField} from '../../../Components/Upload/OlzMultiImageField/OlzMultiImageField';
import {OlzCaptcha} from '../../../Captcha/Components/OlzCaptcha/OlzCaptcha';
import {codeHref} from '../../../Utils/constants';
import {assert} from '../../../Utils/generalUtils';

import './OlzEditNewsModal.scss';

type PublishAtOption = 'unchanged'|'now'|'custom';

interface OlzEditNewsForm {
    format: OlzNewsFormat|'UNDEFINED'|undefined;
    authorUserId: number|null;
    authorRoleId: number|null;
    authorName: string|null;
    authorEmail: string|null;
    publishAtOption: PublishAtOption;
    publishAtDateTime: string|null;
    publishedAt: string|null;
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
    const now = new Date();
    const offsetMs = now.getTimezoneOffset() * 60 * 1000;
    const msLocal = now.getTime() - offsetMs;
    const localNow = new Date(msLocal);
    const localIsoNow = localNow.toISOString();
    const nowDate = localIsoNow.substring(0, 10);
    const nowTime = localIsoNow.substring(11, 19);
    return {
        format: apiData?.format,
        authorUserId: apiData?.authorUserId ?? null,
        authorRoleId: apiData?.authorRoleId ?? null,
        authorName: apiData?.authorName ?? null,
        authorEmail: apiData?.authorEmail ?? null,
        publishAtOption: apiData ? 'unchanged' : 'now',
        publishAtDateTime: apiData?.publishAt ?? `${nowDate} ${nowTime}`,
        publishedAt: apiData?.publishAt ?? null,
        title: apiData?.title ?? '',
        teaser: apiData?.teaser ?? '',
        content: apiData?.content ?? '',
        externalUrl: apiData?.externalUrl ?? '',
        imageIds: apiData?.imageIds ?? [],
        fileIds: apiData?.fileIds ?? [],
    };
}

function getApiFromForm(config: OlzEditNewsModalConfig, formData: OlzEditNewsForm): OlzNewsData {
    if (formData?.format === 'UNDEFINED') {
        throw new Error('Format cannot be UNDEFINED');
    }
    return {
        format: assert(formData?.format),
        authorUserId: formData?.authorUserId,
        authorRoleId: formData?.authorRoleId,
        authorName: formData?.authorName,
        authorEmail: formData?.authorEmail,
        publishAt: formData?.publishAtOption === 'unchanged'
            ? formData?.publishedAt
            : formData?.publishAtOption === 'now'
                ? null
                : formData?.publishAtDateTime,
        title: formData?.title,
        teaser: config.hasTeaser ? formData?.teaser : '',
        content: formData?.content,
        externalUrl: config.hasExternalUrl ? (formData?.externalUrl || null) : null,
        tags: [],
        terminId: null,
        imageIds: formData?.imageIds,
        fileIds: formData?.fileIds,
    };
}

// ---

export type OlzEditNewsModalMode = 'anonymous'|'account'|'account_with_blog'|'account_with_aktuell'|'account_with_all';

interface OlzEditNewsModalConfig {
    name: string;
    hasFreeFormAuthor: boolean;
    hasTeaser: boolean;
    contentLabel: string|null;
    hasFormattingNotes: boolean;
    hasExternalUrl: boolean;
    hasImages: boolean;
    hasFiles: boolean;
    hasCaptcha: boolean;
}

const DEFAULT_CONFIG: OlzEditNewsModalConfig = {
    name: '?',
    hasFreeFormAuthor: false,
    hasTeaser: true,
    contentLabel: 'Inhalt',
    hasFormattingNotes: true,
    hasExternalUrl: true,
    hasImages: true,
    hasFiles: true,
    hasCaptcha: false,
};

const CONFIG_BY_FORMAT: {[format in OlzNewsFormat]: OlzEditNewsModalConfig} = {
    aktuell: {
        name: 'Aktuell',
        hasFreeFormAuthor: false,
        hasTeaser: true,
        contentLabel: 'Inhalt',
        hasFormattingNotes: true,
        hasExternalUrl: false,
        hasImages: true,
        hasFiles: true,
        hasCaptcha: false,
    },
    kaderblog: {
        name: 'Kaderblog',
        hasFreeFormAuthor: false,
        hasTeaser: false,
        contentLabel: 'Blogeintrag',
        hasFormattingNotes: true,
        hasExternalUrl: true,
        hasImages: true,
        hasFiles: true,
        hasCaptcha: false,
    },
    forum: {
        name: 'Forum',
        hasFreeFormAuthor: false,
        hasTeaser: false,
        contentLabel: 'Dein Beitrag',
        hasFormattingNotes: true,
        hasExternalUrl: false,
        hasImages: true,
        hasFiles: false,
        hasCaptcha: false,
    },
    galerie: {
        name: 'Galerie',
        hasFreeFormAuthor: false,
        hasTeaser: false,
        contentLabel: 'Kommentar',
        hasFormattingNotes: true,
        hasExternalUrl: false,
        hasImages: true,
        hasFiles: false,
        hasCaptcha: false,
    },
    video: {
        name: 'Video',
        hasFreeFormAuthor: false,
        hasTeaser: false,
        contentLabel: null,
        hasFormattingNotes: false,
        hasExternalUrl: true,
        hasImages: true,
        hasFiles: false,
        hasCaptcha: false,
    },
    anonymous: {
        name: 'Forum',
        hasFreeFormAuthor: true,
        hasTeaser: false,
        contentLabel: 'Dein Beitrag',
        hasFormattingNotes: true,
        hasExternalUrl: false,
        hasImages: false,
        hasFiles: false,
        hasCaptcha: true,
    },
};

const FORMATS_BY_MODE: {[mode in OlzEditNewsModalMode]: OlzNewsFormat[]} = {
    anonymous: ['anonymous'],
    account: ['forum', 'galerie', 'video'],
    account_with_blog: ['forum', 'kaderblog', 'galerie', 'video'],
    account_with_aktuell: ['forum', 'aktuell', 'galerie', 'video'],
    account_with_all: ['forum', 'kaderblog', 'aktuell', 'galerie', 'video'],
};

const PUBLISH_AT_OPTIONS: {id: PublishAtOption, title: string}[] = [
    {id: 'unchanged', title: '(unverändert)'},
    {id: 'now', title: 'Jetzt'},
    {id: 'custom', title: 'Um:'},
];

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

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});
    const [isRolesLoading, setIsRolesLoading] = React.useState<boolean>(false);
    const [isImagesLoading, setIsImagesLoading] = React.useState<boolean>(false);
    const [isFilesLoading, setIsFilesLoading] = React.useState<boolean>(false);
    const [captchaToken, setCaptchaToken] = React.useState<string|null>(null);

    const format = watch('format');
    const config = (format && format !== 'UNDEFINED') ? CONFIG_BY_FORMAT[format] : DEFAULT_CONFIG;
    const publishAtOption = watch('publishAtOption');
    const publishAtOptions = props?.id
        ? PUBLISH_AT_OPTIONS
        : PUBLISH_AT_OPTIONS.filter((option) => option.id !== 'unchanged');

    const onSubmit: SubmitHandler<OlzEditNewsForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(config, values);

        const [err, response] = await (props.id
            ? olzApi.getResult('updateNews', {id: props.id, meta, data})
            : olzApi.getResult('createNews', {meta, data, custom: {captchaToken}}));
        if (err) {
            setStatus({id: 'SUBMIT_FAILED', message: `Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'SUBMITTED'});
        window.location.reload();
    };

    const onDelete = props.id ? async () => {
        setStatus({id: 'DELETING'});
        const [err, response] = await olzApi.getResult('deleteNews', {id: assert(props.id)});
        if (err) {
            setStatus({id: 'DELETE_FAILED', message: `Löschen fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'DELETED'});
        // This could probably be done more smoothly!
        window.location.reload();
    } : undefined;

    const dialogTitle = props.mode === 'anonymous'
        ? 'Forumseintrag erstellen'
        : (props.id === undefined
            ? 'News-Eintrag erstellen'
            : 'News-Eintrag bearbeiten'
        );
    const markdownNotice = config.hasFormattingNotes ? MARKDOWN_NOTICE : '';
    const isLoading = isRolesLoading || isImagesLoading || isFilesLoading;
    const editModalStatus: OlzEditModalStatus = isLoading ? {id: 'LOADING'} : status;

    return (
        <OlzEditModal
            modalId='edit-news-modal'
            dialogTitle={dialogTitle}
            status={editModalStatus}
            onSubmit={handleSubmit(onSubmit)}
            onDelete={onDelete}
        >
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
                            setIsLoading={setIsRolesLoading}
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
            <div className='row'>
                <div className='col mb-3'>
                    <label htmlFor='publishAtOption-input'>Veröffentlichung</label>
                    <select
                        className='form-control form-select'
                        id='publishAtOption-input'
                        {...register('publishAtOption')}
                        defaultValue={publishAtOption ?? publishAtOptions[0]}
                    >
                        {publishAtOptions.map((option) => (
                            <option value={option.id} key={option.id}>
                                {option.title}
                            </option>
                        ))}
                    </select>
                </div>
                {publishAtOption === 'custom' ? (
                    <div className='col mb-3'>
                        <OlzTextField
                            title=''
                            name='publishAtDateTime'
                            errors={errors}
                            register={register}
                        />
                    </div>
                ) : (
                    <div className='col mb-3'></div>
                )}
            </div>
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
                        title={<>Teaser {markdownNotice}</>}
                        name='teaser'
                        errors={errors}
                        register={register}
                    />
                </div>
            ) : null}
            {config.contentLabel ? (
                <div className='mb-3'>
                    <OlzTextField
                        mode='textarea'
                        title={<>{config.contentLabel} {markdownNotice}</>}
                        name='content'
                        errors={errors}
                        register={register}
                    />
                </div>
            ) : null}
            {config.hasExternalUrl ? (
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
                        setIsLoading={setIsImagesLoading}
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
                        setIsLoading={setIsFilesLoading}
                    />
                </div>
            ) : null}
            {config.hasCaptcha ? (
                <OlzCaptcha onToken={setCaptchaToken}/>
            ) : null}
            <p>
                <span className='required-field-asterisk'>*</span>
                {' Mit dem Speichern erklärst du dich mit den '}
                <a
                    href={`${codeHref}fragen_und_antworten/forumsregeln`}
                    target='_blank'
                >
                    Forumsregeln
                </a>
                {' einverstanden'}
            </p>
        </OlzEditModal>
    );
};

export function initOlzEditNewsModal(
    mode: OlzEditNewsModalMode,
    id?: number,
    meta?: OlzMetaData,
    data?: OlzNewsData,
): boolean {
    return initOlzEditModal('edit-news-modal', () => (
        <OlzEditNewsModal
            mode={mode}
            id={id}
            meta={meta}
            data={data}
        />
    ));
}
