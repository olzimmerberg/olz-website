import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzTerminLabelData, OlzTerminTemplateData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzEntityField} from '../../../Components/Common/OlzEntityField/OlzEntityField';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzMultiFileField} from '../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {OlzMultiImageField} from '../../../Components/Upload/OlzMultiImageField/OlzMultiImageField';
import {getApiBoolean, getApiNumber, getApiString, getFormBoolean, getFormNumber, getFormString, getResolverResult, validateIntegerOrNull, validateTimeOrNull} from '../../../Utils/formUtils';
import {Entity, isDefined} from '../../../Utils/generalUtils';

import './OlzEditTerminTemplateModal.scss';

interface OlzEditTerminTemplateForm {
    startTime: string;
    durationSeconds: string;
    title: string;
    text: string;
    deadlineEarlierSeconds: string;
    deadlineTime: string;
    shouldPromote: string;
    types: (string|boolean)[];
    locationId: number|null;
    imageIds: string[];
    fileIds: string[];
    hasNewsletter: string|boolean;
}

const resolver: Resolver<OlzEditTerminTemplateForm> = async (values) => {
    const errors: FieldErrors<OlzEditTerminTemplateForm> = {};
    [errors.startTime, values.startTime] = validateTimeOrNull(values.startTime);
    errors.durationSeconds = validateIntegerOrNull(values.durationSeconds);
    errors.deadlineEarlierSeconds = validateIntegerOrNull(values.deadlineEarlierSeconds);
    [errors.deadlineTime, values.deadlineTime] = validateTimeOrNull(values.deadlineTime);
    return getResolverResult(errors, values);
};

function getFormFromApi(labels: Entity<OlzTerminLabelData>[], apiData?: OlzTerminTemplateData): OlzEditTerminTemplateForm {
    const typesSet = new Set(apiData?.types ?? []);
    return {
        startTime: getFormString(apiData?.startTime),
        durationSeconds: getFormNumber(apiData?.durationSeconds),
        title: getFormString(apiData?.title),
        text: getFormString(apiData?.text),
        deadlineEarlierSeconds: getFormNumber(apiData?.deadlineEarlierSeconds),
        deadlineTime: getFormString(apiData?.deadlineTime),
        shouldPromote: getFormBoolean(apiData?.shouldPromote),
        types: labels.map((label) => getFormBoolean(typesSet.has(label.data.ident))),
        locationId: apiData?.locationId ?? null,
        fileIds: apiData?.fileIds ?? [],
        imageIds: apiData?.imageIds ?? [],
        hasNewsletter: getFormBoolean(apiData?.newsletter),
    };
}

function getApiFromForm(labels: Entity<OlzTerminLabelData>[], formData: OlzEditTerminTemplateForm): OlzTerminTemplateData {
    const typesSet = new Set(labels
        .map((label, index) => (
            getApiBoolean(formData.types[index]) ? label.data.ident : undefined
        ))
        .filter(isDefined));
    return {
        startTime: getApiString(formData.startTime),
        durationSeconds: getApiNumber(formData.durationSeconds),
        title: getApiString(formData.title) ?? '',
        text: getApiString(formData.text) ?? '',
        deadlineEarlierSeconds: getApiNumber(formData.deadlineEarlierSeconds),
        deadlineTime: getApiString(formData.deadlineTime),
        shouldPromote: getApiBoolean(formData.shouldPromote),
        types: Array.from(typesSet),
        locationId: formData.locationId,
        fileIds: formData.fileIds,
        imageIds: formData.imageIds,
        newsletter: getApiBoolean(formData.hasNewsletter),
    };
}

// ---

interface OlzEditTerminTemplateModalProps {
    id?: number;
    labels: Entity<OlzTerminLabelData>[];
    meta?: OlzMetaData;
    data?: OlzTerminTemplateData;
}

export const OlzEditTerminTemplateModal = (props: OlzEditTerminTemplateModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, control, watch} = useForm<OlzEditTerminTemplateForm>({
        resolver,
        defaultValues: getFormFromApi(props.labels, props.data),
    });

    const [isSubmitting, setIsSubmitting] = React.useState<boolean>(false);
    const [isLocationLoading, setIsLocationLoading] = React.useState<boolean>(false);
    const [isImagesLoading, setIsImagesLoading] = React.useState<boolean>(false);
    const [isFilesLoading, setIsFilesLoading] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const imageIds = watch('imageIds');

    const onSubmit: SubmitHandler<OlzEditTerminTemplateForm> = async (values) => {
        setIsSubmitting(true);
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(props.labels, values);

        const [err, response] = await (props.id
            ? olzApi.getResult('updateTerminTemplate', {id: props.id, meta, data})
            : olzApi.getResult('createTerminTemplate', {meta, data}));
        if (err || response.status !== 'OK') {
            setSuccessMessage('');
            setErrorMessage(`Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`);
            setIsSubmitting(false);
            return;
        }

        setSuccessMessage('Änderung erfolgreich. Bitte warten...');
        setErrorMessage('');
        // This could probably be done more smoothly!
        window.location.reload();
    };

    const dialogTitle = (props.id === undefined
        ? 'Termin-Vorlage erstellen'
        : 'Termin-Vorlage bearbeiten'
    );
    const isShouldPromoteEnabled = imageIds.length > 0;
    const isLoading = isLocationLoading || isImagesLoading || isFilesLoading;

    return (
        <OlzEditModal
            modalId='edit-termin-template-modal'
            dialogTitle={dialogTitle}
            successMessage={successMessage}
            errorMessage={errorMessage}
            isLoading={isLoading}
            isSubmitting={isSubmitting}
            onSubmit={handleSubmit(onSubmit)}
        >
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Beginn Zeit'
                        name='startTime'
                        errors={errors}
                        register={register}
                    />
                </div>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Dauer (in Sekunden)'
                        name='durationSeconds'
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
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Meldeschluss vorher (in Sekunden)'
                        name='deadlineEarlierSeconds'
                        errors={errors}
                        register={register}
                    />
                </div>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Meldeschluss Zeit'
                        name='deadlineTime'
                        errors={errors}
                        register={register}
                    />
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
                        Termin-Meldeschluss sofort auf der Startseite anzeigen
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
                <div className='col mb-3'>
                    <OlzEntityField
                        title='Ort'
                        entityType='TerminLocation'
                        name='locationId'
                        errors={errors}
                        control={control}
                        setIsLoading={setIsLocationLoading}
                        nullLabel={'Kein Termin-Ort ausgewählt'}
                    />
                </div>
                <div className='col mb-3'>
                </div>
            </div>
            <div className='mb-3' id='images-upload'>
                <OlzMultiImageField
                    title='Bilder'
                    name='imageIds'
                    errors={errors}
                    control={control}
                    setIsLoading={setIsImagesLoading}
                />
            </div>
            <div className='mb-3' id='files-upload'>
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

export function initOlzEditTerminTemplateModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzTerminTemplateData,
): boolean {
    olzApi.call('listTerminLabels', {}).then((response) => {
        initOlzEditModal('edit-termin-template-modal', () => (
            <OlzEditTerminTemplateModal
                id={id}
                labels={response.items}
                meta={meta}
                data={data}
            />
        ));
    });
    return false;
}
