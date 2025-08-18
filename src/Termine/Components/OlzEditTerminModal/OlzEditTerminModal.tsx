import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzTerminData, OlzTerminLabelData, OlzTerminTemplateData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, MARKDOWN_NOTICE, OlzEditModal, OlzEditModalStatus} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzEntityChooser} from '../../../Components/Common/OlzEntityChooser/OlzEntityChooser';
import {OlzEntityField} from '../../../Components/Common/OlzEntityField/OlzEntityField';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzMultiFileField} from '../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {OlzMultiImageField} from '../../../Components/Upload/OlzMultiImageField/OlzMultiImageField';
import {isoNow, codeHref} from '../../../Utils/constants';
import {getApiBoolean, getApiNumber, getApiString, getDateFeedback, getDateTimeFeedback, getFormBoolean, getFormNumber, getFormString, getResolverResult, validateDate, validateDateOrNull, validateDateTimeOrNull, validateIntegerOrNull, validateNotEmpty, validateTimeOrNull} from '../../../Utils/formUtils';
import {isDefined, Entity, assert} from '../../../Utils/generalUtils';
import {getTerminUpdateFromTemplate} from '../../Utils/termineUtils';

import './OlzEditTerminModal.scss';

export const TERMIN_LOCATION_NOTICE = (<>
    <span className='termin-location-notice'>
        <a
            href={`${codeHref}termine/orte`}
            target='_blank'
            className='linkint'
        >
            Termin-Orte bearbeiten
        </a>
    </span>
</>);

interface OlzEditTerminForm {
    solvId: number | null;
    startDate: string;
    startTime: string;
    endDate: string;
    endTime: string;
    title: string;
    text: string;
    deadline: string;
    shouldPromote: string;
    types: (string | boolean)[];
    locationId: number | null;
    coordinateX: string;
    coordinateY: string;
    fileIds: string[];
    imageIds: string[];
    hasNewsletter: string | boolean;
}

const resolver: Resolver<OlzEditTerminForm> = async (values) => {
    const errors: FieldErrors<OlzEditTerminForm> = {};
    if (values.solvId === null) {
        [errors.startDate, values.startDate] = validateDate(values.startDate);
    }
    [errors.startTime, values.startTime] = validateTimeOrNull(values.startTime);
    [errors.endDate, values.endDate] = validateDateOrNull(values.endDate);
    [errors.endTime, values.endTime] = validateTimeOrNull(values.endTime);
    if (values.solvId === null) {
        errors.title = validateNotEmpty(values.title);
    }
    [errors.deadline, values.deadline] = validateDateTimeOrNull(getDeadlineDateTime(values.deadline));
    errors.coordinateX = validateIntegerOrNull(values.coordinateX);
    errors.coordinateY = validateIntegerOrNull(values.coordinateY);
    return getResolverResult(errors, values);
};

function getFormFromApi(labels: Entity<OlzTerminLabelData>[], apiData?: OlzTerminData): OlzEditTerminForm {
    const typesSet = new Set(apiData?.types ?? []);
    return {
        solvId: apiData?.solvId ?? null,
        startDate: getFormString(apiData?.startDate ?? isoNow.substring(0, 10)),
        startTime: getFormString(apiData?.startTime),
        endDate: getFormString(apiData?.endDate),
        endTime: getFormString(apiData?.endTime),
        title: getFormString(apiData?.title),
        text: getFormString(apiData?.text),
        deadline: getFormString(apiData?.deadline),
        shouldPromote: getFormBoolean(apiData?.shouldPromote),
        types: labels.map((label) => getFormBoolean(typesSet.has(label.data.ident))),
        locationId: apiData?.locationId ?? null,
        coordinateX: getFormNumber(apiData?.coordinateX),
        coordinateY: getFormNumber(apiData?.coordinateY),
        fileIds: apiData?.fileIds ?? [],
        imageIds: apiData?.imageIds ?? [],
        hasNewsletter: getFormBoolean(apiData?.newsletter),
    };
}

function getApiFromForm(labels: Entity<OlzTerminLabelData>[], templateId: number | undefined, formData: OlzEditTerminForm): OlzTerminData {
    const typesSet = new Set(labels
        .map((label, index) => (
            getApiBoolean(formData.types[index]) ? label.data.ident : undefined
        ))
        .filter(isDefined));
    return {
        fromTemplateId: templateId ?? null,
        solvId: formData.solvId,
        startDate: getApiString(formData.startDate) ?? '',
        startTime: getApiString(formData.startTime),
        endDate: getApiString(formData.endDate),
        endTime: getApiString(formData.endTime),
        title: getApiString(formData.title),
        text: getApiString(formData.text) ?? '',
        deadline: getApiString(formData.deadline) || null,
        shouldPromote: getApiBoolean(formData.shouldPromote),
        types: Array.from(typesSet),
        locationId: formData.locationId,
        coordinateX: formData.locationId ? null : getApiNumber(formData.coordinateX),
        coordinateY: formData.locationId ? null : getApiNumber(formData.coordinateY),
        fileIds: formData.fileIds,
        imageIds: formData.imageIds,
        newsletter: getApiBoolean(formData.hasNewsletter),
        go2olId: null,
    };
}

function getDeadlineDateTime(valueArg: string): string {
    return valueArg.length === 10 ? `${valueArg} 23:59:59` : valueArg;
}

// ---

interface OlzEditTerminModalProps {
    id?: number;
    templateId?: number;
    labels: Entity<OlzTerminLabelData>[];
    meta?: OlzMetaData;
    data?: OlzTerminData;
}

export const OlzEditTerminModal = (props: OlzEditTerminModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, control, setValue, watch} = useForm<OlzEditTerminForm>({
        resolver,
        defaultValues: getFormFromApi(props.labels, props.data),
    });

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});
    const [isTemplateLoading, setIsTemplateLoading] = React.useState<boolean>(false);
    const [isSolvLoading, setIsSolvLoading] = React.useState<boolean>(false);
    const [isLocationLoading, setIsLocationLoading] = React.useState<boolean>(false);
    const [isImagesLoading, setIsImagesLoading] = React.useState<boolean>(false);
    const [isFilesLoading, setIsFilesLoading] = React.useState<boolean>(false);
    const [templateId, setTemplateId] = React.useState<number | null>(props.templateId ?? null);
    const [templateData, setTemplateData] = React.useState<OlzTerminTemplateData | null>(null);

    const startDate = watch('startDate');
    const startTime = watch('startTime');
    const endDate = watch('endDate');
    const deadline = watch('deadline');
    const solvId = watch('solvId');
    const locationId = watch('locationId');
    const imageIds = watch('imageIds');

    React.useEffect(() => {
        if (!templateId) {
            return;
        }
        setIsTemplateLoading(true);
        // We use edit (not get) in order to copy the images & files to temp/
        olzApi.call('editTerminTemplate', {
            id: templateId,
        })
            .then((response) => {
                setIsTemplateLoading(false);
                setTemplateData(response.data);
            });
    }, [templateId]);

    React.useEffect(() => {
        if (!templateData) {
            return;
        }
        if (props.id) {
            // Existing entry, do not prefill!
            return;
        }
        // Ignore (overwrite) the start time in this case
        const terminUpdate = getTerminUpdateFromTemplate(templateData, startDate, '', props.labels);
        if (!terminUpdate) {
            return;
        }
        setValue('startTime', terminUpdate.startTime);
        setValue('endDate', terminUpdate.endDate);
        setValue('endTime', terminUpdate.endTime);
        setValue('title', terminUpdate.title);
        setValue('text', terminUpdate.text);
        setValue('deadline', terminUpdate.deadline);
        setValue('shouldPromote', terminUpdate.shouldPromote);
        setValue('hasNewsletter', terminUpdate.hasNewsletter);
        setValue('types', terminUpdate.types);
        setValue('locationId', terminUpdate.locationId);
        setValue('imageIds', terminUpdate.imageIds);
        setValue('fileIds', terminUpdate.fileIds);
    }, [templateData]);

    React.useEffect(() => {
        if (!templateData || !startTime) {
            return;
        }
        const terminUpdate = getTerminUpdateFromTemplate(templateData, startDate, startTime, props.labels);
        if (!terminUpdate) {
            return;
        }
        setValue('endDate', terminUpdate.endDate);
        setValue('endTime', terminUpdate.endTime);
        setValue('deadline', terminUpdate.deadline);
    }, [startDate, startTime]);

    const onSubmit: SubmitHandler<OlzEditTerminForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(props.labels, props.templateId, values);

        const [err, response] = await (props.id
            ? olzApi.getResult('updateTermin', {id: props.id, meta, data})
            : olzApi.getResult('createTermin', {meta, data}));
        if (err) {
            setStatus({id: 'SUBMIT_FAILED', message: `Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'SUBMITTED'});
        // This could probably be done more smoothly!
        window.location.reload();
    };

    const onDelete = props.id ? async () => {
        setStatus({id: 'DELETING'});
        const [err, response] = await olzApi.getResult('deleteTermin', {id: assert(props.id)});
        if (err) {
            setStatus({id: 'DELETE_FAILED', message: `Löschen fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'DELETED'});
        // This could probably be done more smoothly!
        window.location.reload();
    } : undefined;

    const solvDisabledClass = solvId !== null ? ' solv-disabled' : '';
    const dialogTitle = (props.id === undefined
        ? 'Termin-Eintrag erstellen'
        : 'Termin-Eintrag bearbeiten'
    );
    const startDateInfo = getDateFeedback(startDate);
    const endDateInfo = getDateFeedback(endDate);
    const deadlineInfo = getDateTimeFeedback(getDeadlineDateTime(deadline));
    const isShouldPromoteEnabled = imageIds.length > 0;
    const isLoading = isTemplateLoading || isSolvLoading || isLocationLoading || isImagesLoading || isFilesLoading;
    const editModalStatus: OlzEditModalStatus = isLoading ? {id: 'LOADING'} : status;

    return (
        <OlzEditModal
            modalId='edit-termin-modal'
            dialogTitle={dialogTitle}
            status={editModalStatus}
            onSubmit={handleSubmit(onSubmit)}
            onDelete={onDelete}
        >
            <div className='row'>
                <div className='col mb-3 template-chooser'>
                    <label>Vorlage</label>
                    <OlzEntityChooser
                        entityType={'TerminTemplate'}
                        entityId={templateId}
                        onEntityIdChange={(e) => setTemplateId(e.detail)}
                        nullLabel={'Ohne Vorlage'}
                    />
                </div>
                <div className='col mb-3'>
                    <OlzEntityField
                        title='SOLV-Termin (mit autom. Updates)'
                        entityType='SolvEvent'
                        name='solvId'
                        errors={errors}
                        control={control}
                        setIsLoading={setIsSolvLoading}
                        nullLabel={'Kein SOLV-Termin ausgewählt'}
                    />
                </div>
            </div>
            <div className='row'>
                <div className={`col mb-3${solvDisabledClass}`}>
                    <OlzTextField
                        title='Beginn Datum'
                        name='startDate'
                        errors={errors}
                        register={register}
                        disabled={solvId !== null}
                        placeholder={solvId ? 'Wird von SOLV übernommen' : ''}
                    />
                    {startDateInfo}
                </div>
                <div className={`col mb-3${solvDisabledClass}`}>
                    <OlzTextField
                        title='Beginn Zeit'
                        name='startTime'
                        errors={errors}
                        register={register}
                        disabled={solvId !== null}
                        placeholder={solvId ? 'Wird von SOLV übernommen' : ''}
                    />
                </div>
            </div>
            <div className='row'>
                <div className={`col mb-3${solvDisabledClass}`}>
                    <OlzTextField
                        title='Ende Datum'
                        name='endDate'
                        errors={errors}
                        register={register}
                        disabled={solvId !== null}
                        placeholder={solvId ? 'Wird von SOLV übernommen' : ''}
                    />
                    {endDateInfo}
                </div>
                <div className={`col mb-3${solvDisabledClass}`}>
                    <OlzTextField
                        title='Ende Zeit'
                        name='endTime'
                        errors={errors}
                        register={register}
                        disabled={solvId !== null}
                        placeholder={solvId ? 'Wird von SOLV übernommen' : ''}
                    />
                </div>
            </div>
            <div className={`mb-3${solvDisabledClass}`}>
                <OlzTextField
                    title='Titel'
                    name='title'
                    errors={errors}
                    register={register}
                    disabled={solvId !== null}
                    placeholder={solvId ? 'Wird von SOLV übernommen' : ''}
                />
            </div>
            <div className={`mb-3${solvDisabledClass}`}>
                <OlzTextField
                    mode='textarea'
                    title={<>Text {MARKDOWN_NOTICE}</>}
                    name='text'
                    errors={errors}
                    register={register}
                    disabled={solvId !== null}
                    placeholder={solvId ? 'Wird von SOLV übernommen' : ''}
                />
            </div>
            <div className='row'>
                <div className={`col mb-3${solvDisabledClass}`}>
                    <OlzTextField
                        title='Meldeschluss'
                        name='deadline'
                        errors={errors}
                        register={register}
                        disabled={solvId !== null}
                        placeholder={solvId ? 'Wird von SOLV übernommen' : ''}
                    />
                    {deadlineInfo}
                </div>
                <div className='col mb-3 shouldPromote-container'>
                    <input
                        type='checkbox'
                        value='yes'
                        {...register('shouldPromote')}
                        disabled={!isShouldPromoteEnabled}
                        id='shouldPromote-input'
                    />
                    <label htmlFor='shouldPromote-input'>
                        Meldeschluss sofort auf der Startseite anzeigen
                        {isShouldPromoteEnabled ? '' : ' (zuerst Bilder hinzufügen!)'}
                    </label>
                </div>
            </div>
            <div className='mb-3'>
                <label htmlFor='types-container'>Typ</label>
                <div id='types-container'>
                    {props.labels?.map((label, index) => (
                        <span className='types-option' key={`${index}-${label.data.ident}`}>
                            <input
                                type='checkbox'
                                value='yes'
                                {...register(`types.${index}`)}
                                id={`types-${label.data.ident}-input`}
                                key={label.id}
                            />
                            <label htmlFor={`types-${label.data.ident}-input`}>
                                {label.data.name}
                            </label>
                        </span>
                    ))}
                </div>
            </div>
            <div className='row'>
                <div className={`col mb-3${solvDisabledClass}`}>
                    <OlzEntityField
                        title={<>Ort {TERMIN_LOCATION_NOTICE}</>}
                        entityType='TerminLocation'
                        name='locationId'
                        errors={errors}
                        control={control}
                        setIsLoading={setIsLocationLoading}
                        nullLabel={solvId ? 'Wird von SOLV übernommen' : 'Kein Termin-Ort ausgewählt'}
                        disabled={solvId !== null}
                    />
                </div>
                <div className='col mb-3'>
                </div>
            </div>
            {locationId === null ? (
                <div className='row'>
                    <div className={`col mb-3${solvDisabledClass}`}>
                        <OlzTextField
                            title='X-Koordinate (LV1903)'
                            name='coordinateX'
                            errors={errors}
                            register={register}
                            disabled={solvId !== null}
                            placeholder={solvId ? 'Wird von SOLV übernommen' : ''}

                        />
                    </div>
                    <div className={`col mb-3${solvDisabledClass}`}>
                        <OlzTextField
                            title='Y-Koordinate (LV1903)'
                            name='coordinateY'
                            errors={errors}
                            register={register}
                            disabled={solvId !== null}
                            placeholder={solvId ? 'Wird von SOLV übernommen' : ''}
                        />
                    </div>
                </div>
            ) : null}
            <div className='mb-3' id='images-upload'>
                <OlzMultiImageField
                    title='Bilder'
                    name='imageIds'
                    errors={errors}
                    control={control}
                    setIsLoading={setIsImagesLoading}
                />
            </div>
            <div className={`mb-3${solvDisabledClass}`} id='files-upload'>
                <OlzMultiFileField
                    title='Dateien'
                    name='fileIds'
                    errors={errors}
                    control={control}
                    setIsLoading={setIsFilesLoading}
                />
            </div>
            <div className='hasNewsletter-container'>
                <input
                    type='checkbox'
                    value='yes'
                    {...register('hasNewsletter')}
                    id='hasNewsletter-input'
                />
                <label htmlFor='hasNewsletter-input'>
                    Newsletter für Änderung
                </label>
            </div>
        </OlzEditModal>
    );
};

export function initOlzEditTerminModal(
    id?: number,
    templateId?: number,
    meta?: OlzMetaData,
    data?: OlzTerminData,
): boolean {

    olzApi.call('listTerminLabels', {}).then((response) => {
        initOlzEditModal('edit-termin-modal', () => (
            <OlzEditTerminModal
                id={id}
                labels={response.items}
                templateId={templateId}
                meta={meta}
                data={data}
            />
        ));
    });

    return false;
}
