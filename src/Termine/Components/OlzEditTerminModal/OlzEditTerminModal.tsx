import * as bootstrap from 'bootstrap';
import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../../src/Api/client';
import {OlzMetaData, OlzTerminData, OlzTerminTemplateData} from '../../../../src/Api/client/generated_olz_api_types';
import {OlzEntityChooser} from '../../../Components/Common/OlzEntityChooser/OlzEntityChooser';
import {OlzEntityField} from '../../../Components/Common/OlzEntityField/OlzEntityField';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzMultiFileField} from '../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {OlzMultiImageField} from '../../../Components/Upload/OlzMultiImageField/OlzMultiImageField';
import {isoNow} from '../../../Utils/constants';
import {getApiBoolean, getApiNumber, getApiString, getFormBoolean, getFormNumber, getFormString, getResolverResult, validateDate, validateDateOrNull, validateDateTimeOrNull, validateIntegerOrNull, validateNotEmpty, validateTimeOrNull} from '../../../Utils/formUtils';
import {isDefined, timeout} from '../../../Utils/generalUtils';
import {initReact} from '../../../Utils/reactUtils';

import './OlzEditTerminModal.scss';

interface OlzEditTerminForm {
    startDate: string;
    startTime: string;
    endDate: string;
    endTime: string;
    title: string;
    text: string;
    link: string;
    deadline: string;
    hasNewsletter: string|boolean;
    solvId: string;
    go2olId: string;
    hasTypeProgramm: string|boolean;
    hasTypeWeekend: string|boolean;
    hasTypeTraining: string|boolean;
    hasTypeOl: string|boolean;
    hasTypeClub: string|boolean;
    locationId: number|null;
    coordinateX: string;
    coordinateY: string;
    fileIds: string[];
    imageIds: string[];
}

const resolver: Resolver<OlzEditTerminForm> = async (values) => {
    const errors: FieldErrors<OlzEditTerminForm> = {};
    [errors.startDate, values.startDate] = validateDate(values.startDate);
    [errors.startTime, values.startTime] = validateTimeOrNull(values.startTime);
    [errors.endDate, values.endDate] = validateDateOrNull(values.endDate);
    [errors.endTime, values.endTime] = validateTimeOrNull(values.endTime);
    errors.title = validateNotEmpty(values.title);
    [errors.deadline, values.deadline] = validateDateTimeOrNull(
        values.deadline.length === 10 ? `${values.deadline} 23:59:59` : values.deadline,
    );
    errors.solvId = validateIntegerOrNull(values.solvId);
    errors.coordinateX = validateIntegerOrNull(values.coordinateX);
    errors.coordinateY = validateIntegerOrNull(values.coordinateY);
    return getResolverResult(errors, values);
};

function getFormFromApi(apiData?: OlzTerminData): OlzEditTerminForm {
    const typesSet = new Set(apiData?.types ?? []);
    return {
        startDate: getFormString(apiData?.startDate ?? isoNow.substring(0, 10)),
        startTime: getFormString(apiData?.startTime),
        endDate: getFormString(apiData?.endDate),
        endTime: getFormString(apiData?.endTime),
        title: getFormString(apiData?.title),
        text: getFormString(apiData?.text),
        link: getFormString(apiData?.link),
        deadline: getFormString(apiData?.deadline),
        hasNewsletter: getFormBoolean(apiData?.newsletter),
        solvId: getFormNumber(apiData?.solvId),
        go2olId: getFormString(apiData?.go2olId),
        hasTypeProgramm: getFormBoolean(typesSet.has('programm')),
        hasTypeWeekend: getFormBoolean(typesSet.has('weekend')),
        hasTypeTraining: getFormBoolean(typesSet.has('training')),
        hasTypeOl: getFormBoolean(typesSet.has('ol')),
        hasTypeClub: getFormBoolean(typesSet.has('club')),
        locationId: apiData?.locationId ?? null,
        coordinateX: getFormNumber(apiData?.coordinateX),
        coordinateY: getFormNumber(apiData?.coordinateY),
        fileIds: apiData?.fileIds ?? [],
        imageIds: apiData?.imageIds ?? [],
    };
}

function getApiFromForm(formData: OlzEditTerminForm): OlzTerminData {
    const typesSet = new Set([
        getApiBoolean(formData.hasTypeProgramm) ? 'programm' : undefined,
        getApiBoolean(formData.hasTypeWeekend) ? 'weekend' : undefined,
        getApiBoolean(formData.hasTypeTraining) ? 'training' : undefined,
        getApiBoolean(formData.hasTypeOl) ? 'ol' : undefined,
        getApiBoolean(formData.hasTypeClub) ? 'club' : undefined,
    ].filter(isDefined));
    return {
        startDate: getApiString(formData.startDate) ?? '',
        startTime: getApiString(formData.startTime),
        endDate: getApiString(formData.endDate),
        endTime: getApiString(formData.endTime),
        title: getApiString(formData.title) ?? '',
        text: getApiString(formData.text) ?? '',
        link: getApiString(formData.link) ?? '',
        deadline: getApiString(formData.deadline) || null,
        newsletter: getApiBoolean(formData.hasNewsletter),
        solvId: getApiNumber(formData.solvId),
        go2olId: getApiString(formData.go2olId) || null,
        types: Array.from(typesSet),
        locationId: formData.locationId,
        coordinateX: getApiNumber(formData.coordinateX),
        coordinateY: getApiNumber(formData.coordinateY),
        fileIds: formData.fileIds,
        imageIds: formData.imageIds,
    };
}

// ---

interface OlzEditTerminModalProps {
    id?: number;
    templateId?: number;
    meta?: OlzMetaData;
    data?: OlzTerminData;
}

export const OlzEditTerminModal = (props: OlzEditTerminModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, control, setValue, watch} = useForm<OlzEditTerminForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [isLoading, setIsLoading] = React.useState<boolean>(false);
    const [templateId, setTemplateId] = React.useState<number|null>(props.templateId ?? null);
    const [templateData, setTemplateData] = React.useState<OlzTerminTemplateData|null>(null);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const startDate = watch('startDate');
    const startTime = watch('startTime');
    const locationId = watch('locationId');

    React.useEffect(() => {
        if (!templateId) {
            return;
        }
        olzApi.call('getTerminTemplate', {
            id: templateId,
        })
            .then((response) => {
                setTemplateData(response.data);
            });
    }, [templateId]);

    React.useEffect(() => {
        if (!templateData) {
            return;
        }
        setValue('startTime', getFormString(templateData.startTime));
        if (templateData.startTime && templateData.durationSeconds) {
            const isStartDateValid = true; // TODO: Actually check!
            const startIso = isStartDateValid ? `${startDate} ${templateData.startTime}` : `${templateData.startTime}`;
            const start = new Date(Date.parse(startIso));
            const end = new Date(start.getTime() + templateData.durationSeconds * 1000);
            const utcEnd = new Date(end.getTime() - end.getTimezoneOffset() * 60 * 1000);
            setValue('endDate', getFormString(utcEnd.toISOString().substring(0, 10)));
            setValue('endTime', getFormString(utcEnd.toISOString().substring(11, 19)));
        }
        setValue('title', getFormString(templateData.title));
        setValue('text', getFormString(templateData.text));
        setValue('link', getFormString(templateData.link));
        setValue('deadline', '');
        setValue('hasNewsletter', getFormBoolean(templateData.newsletter));
        const typesSet = new Set(templateData.types ?? []);
        setValue('hasTypeProgramm', getFormBoolean(typesSet.has('programm')));
        setValue('hasTypeWeekend', getFormBoolean(typesSet.has('weekend')));
        setValue('hasTypeTraining', getFormBoolean(typesSet.has('training')));
        setValue('hasTypeOl', getFormBoolean(typesSet.has('ol')));
        setValue('hasTypeClub', getFormBoolean(typesSet.has('club')));
        setValue('locationId', templateData.locationId ?? null);
        // TODO: Images & Files
    }, [templateData]);

    React.useEffect(() => {
        if (!templateData || !startTime) {
            return;
        }
        const isStartDateValid = true; // TODO: Actually check!
        const startIso = isStartDateValid ? `${startDate} ${startTime}` : `${startTime}`;
        const start = new Date(Date.parse(startIso));
        if (!(start instanceof Date) || isNaN(start.valueOf())) {
            return;
        }

        if (templateData?.durationSeconds) {
            // Calculate end date & time
            const endDateObj = new Date(start.getTime() + templateData.durationSeconds * 1000);
            const utcEnd = new Date(endDateObj.getTime() - endDateObj.getTimezoneOffset() * 60 * 1000);
            if (!(utcEnd instanceof Date) || isNaN(utcEnd.valueOf())) {
                return;
            }
            setValue('endDate', utcEnd.toISOString().substring(0, 10));
            setValue('endTime', utcEnd.toISOString().substring(11, 19));
        }

        if (templateData.deadlineEarlierSeconds) {
            // Calculate deadline datetime
            const deadlineDateObj = new Date(start.getTime() - templateData.deadlineEarlierSeconds * 1000);
            const utcDeadline = new Date(deadlineDateObj.getTime() - deadlineDateObj.getTimezoneOffset() * 60 * 1000);
            if (!(utcDeadline instanceof Date) || isNaN(utcDeadline.valueOf())) {
                return;
            }
            const deadlineDateIso = utcDeadline.toISOString().substring(0, 10);
            const deadlineTimeIso = templateData.deadlineTime ?? utcDeadline.toISOString().substring(11, 19);
            setValue('deadline', `${deadlineDateIso} ${deadlineTimeIso}`);
        }
    }, [templateData, startDate, startTime]);

    const onSubmit: SubmitHandler<OlzEditTerminForm> = async (values) => {
        const meta: OlzMetaData = {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);

        const [err, response] = await (props.id
            ? olzApi.getResult('updateTermin', {id: props.id, meta, data})
            : olzApi.getResult('createTermin', {meta, data}));
        if (err || response.status !== 'OK') {
            setErrorMessage(`Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`);
            return;
        }

        // TODO: This could probably be done more smoothly!
        setSuccessMessage('Änderung erfolgreich. Bitte warten...');
        await timeout(1000);
        window.location.reload();
    };

    const dialogTitle = (props.id === undefined
        ? 'Termin-Eintrag erstellen'
        : 'Termin-Eintrag bearbeiten'
    );

    return (
        <div className='modal fade' id='edit-termin-modal' tabIndex={-1} aria-labelledby='edit-termin-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={handleSubmit(onSubmit)}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-termin-modal-label'>
                                {dialogTitle}
                            </h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
                            <div className='row'>
                                <div className='col mb-3'>
                                    <label>Vorlage</label>
                                    <OlzEntityChooser
                                        entityType={'TerminTemplate'}
                                        entityId={templateId}
                                        onEntityIdChange={(e) => setTemplateId(e.detail)}
                                        nullLabel={'Ohne Vorlage'}
                                    />
                                </div>
                                <div className='col mb-3'>
                                </div>
                            </div>
                            <div className='row'>
                                <div className='col mb-3'>
                                    <OlzTextField
                                        title='Beginn Datum'
                                        name='startDate'
                                        errors={errors}
                                        register={register}
                                    />
                                </div>
                                <div className='col mb-3'>
                                    <OlzTextField
                                        title='Beginn Zeit'
                                        name='startTime'
                                        errors={errors}
                                        register={register}
                                    />
                                </div>
                            </div>
                            <div className='row'>
                                <div className='col mb-3'>
                                    <OlzTextField
                                        title='Ende Datum'
                                        name='endDate'
                                        errors={errors}
                                        register={register}
                                    />
                                </div>
                                <div className='col mb-3'>
                                    <OlzTextField
                                        title='Ende Zeit'
                                        name='endTime'
                                        errors={errors}
                                        register={register}
                                    />
                                </div>
                            </div>
                            <div className='mb-3'>
                                <OlzTextField
                                    title='Titel'
                                    name='title'
                                    errors={errors}
                                    register={register}
                                />
                            </div>
                            <div className='mb-3'>
                                <OlzTextField
                                    mode='textarea'
                                    title='Text'
                                    name='text'
                                    errors={errors}
                                    register={register}
                                />
                            </div>
                            <div className='mb-3'>
                                <OlzTextField
                                    mode='textarea'
                                    title='Link'
                                    name='link'
                                    errors={errors}
                                    register={register}
                                />
                            </div>
                            <div className='mb-3'>
                                <OlzTextField
                                    title='Meldeschluss'
                                    name='deadline'
                                    errors={errors}
                                    register={register}
                                />
                            </div>
                            <div className='mb-3'>
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
                            <div className='row'>
                                <div className='col mb-3'>
                                    <OlzTextField
                                        title='SOLV-ID'
                                        name='solvId'
                                        errors={errors}
                                        register={register}
                                    />
                                </div>
                                <div className='col mb-3'>
                                    <OlzTextField
                                        title='GO2OL-ID'
                                        name='go2olId'
                                        errors={errors}
                                        register={register}
                                    />
                                </div>
                            </div>
                            <div className='mb-3'>
                                <label htmlFor='types-container'>Typ</label>
                                <div id='types-container'>
                                    <span className='types-option'>
                                        <input
                                            type='checkbox'
                                            value='yes'
                                            {...register('hasTypeProgramm')}
                                            id='hasTypeProgramm-input'
                                        />
                                        <label htmlFor='hasTypeProgramm-input'>Jahresprogramm</label>
                                    </span>
                                    <span className='types-option'>
                                        <input
                                            type='checkbox'
                                            value='yes'
                                            {...register('hasTypeWeekend')}
                                            id='hasTypeWeekend-input'
                                        />
                                        <label htmlFor='hasTypeWeekend-input'>Weekends</label>
                                    </span>
                                    <span className='types-option'>
                                        <input
                                            type='checkbox'
                                            value='yes'
                                            {...register('hasTypeTraining')}
                                            id='hasTypeTraining-input'
                                        />
                                        <label htmlFor='hasTypeTraining-input'>Trainings</label>
                                    </span>
                                    <span className='types-option'>
                                        <input
                                            type='checkbox'
                                            value='yes'
                                            {...register('hasTypeOl')}
                                            id='hasTypeOl-input'
                                        />
                                        <label htmlFor='hasTypeOl-input'>Wettkämpfe</label>
                                    </span>
                                    <span className='types-option'>
                                        <input
                                            type='checkbox'
                                            value='yes'
                                            {...register('hasTypeClub')}
                                            id='hasTypeClub-input'
                                        />
                                        <label htmlFor='hasTypeClub-input'>Vereinsanlässe</label>
                                    </span>
                                </div>
                            </div>
                            <div className='row'>
                                <div className='col mb-3'>
                                    <OlzEntityField
                                        title='Ort'
                                        entityType='TerminLocation'
                                        name='locationId'
                                        errors={errors}
                                        control={control}
                                        setIsLoading={setIsLoading}
                                        nullLabel={'Kein Termin-Ort ausgewählt'}
                                    />
                                </div>
                                <div className='col mb-3'>
                                </div>
                            </div>
                            {locationId === null ? (
                                <div className='row'>
                                    <div className='col mb-3'>
                                        <OlzTextField
                                            title='X-Koordinate'
                                            name='coordinateX'
                                            errors={errors}
                                            register={register}
                                        />
                                    </div>
                                    <div className='col mb-3'>
                                        <OlzTextField
                                            title='Y-Koordinate'
                                            name='coordinateY'
                                            errors={errors}
                                            register={register}
                                        />
                                    </div>
                                </div>
                            ) : null}
                            <div id='images-upload'>
                                <OlzMultiImageField
                                    title='Bilder'
                                    name='imageIds'
                                    errors={errors}
                                    control={control}
                                    setIsLoading={setIsLoading}
                                />
                            </div>
                            <div id='files-upload'>
                                <OlzMultiFileField
                                    title='Dateien'
                                    name='fileIds'
                                    errors={errors}
                                    control={control}
                                    setIsLoading={setIsLoading}
                                />
                            </div>
                            <div className='success-message alert alert-success' role='alert'>
                                {successMessage}
                            </div>
                            <div className='error-message alert alert-danger' role='alert'>
                                {errorMessage}
                            </div>
                        </div>
                        <div className='modal-footer'>
                            <button type='button' className='btn btn-secondary' data-bs-dismiss='modal'>Abbrechen</button>
                            <button
                                type='submit'
                                disabled={isLoading}
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

export function initOlzEditTerminModal(
    id?: number,
    templateId?: number,
    meta?: OlzMetaData,
    data?: OlzTerminData,
): boolean {
    initReact('edit-entity-react-root', (
        <OlzEditTerminModal
            id={id}
            templateId={templateId}
            meta={meta}
            data={data}
        />
    ));
    window.setTimeout(() => {
        const modal = document.getElementById('edit-termin-modal');
        if (modal) {
            new bootstrap.Modal(modal, {backdrop: 'static'}).show();
        }
    }, 1);
    return false;
}
