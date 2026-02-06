import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzTerminLabelData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, MARKDOWN_NOTICE, OlzEditModal, OlzEditModalStatus} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzPositionField} from '../../../Components/Common/OlzPositionField/OlzPositionField';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzImageField} from '../../../Components/Upload/OlzImageField/OlzImageField';
import {OlzMultiFileField} from '../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {OlzMultiImageField} from '../../../Components/Upload/OlzMultiImageField/OlzMultiImageField';
import {getApiNumber, getApiString, getFormNumber, getFormString, getResolverResult, validateNotEmpty, validateNumber} from '../../../Utils/formUtils';
import {assert} from '../../../Utils/generalUtils';

import './OlzEditTerminLabelModal.scss';

interface OlzEditTerminLabelForm {
    ident: string;
    name: string;
    details: string;
    icon: string | null;
    position: string;
    imageIds: string[];
    fileIds: string[];
}

const resolver: Resolver<OlzEditTerminLabelForm> = async (values) => {
    const errors: FieldErrors<OlzEditTerminLabelForm> = {};
    errors.ident = validateNotEmpty(values.ident);
    errors.name = validateNotEmpty(values.name);
    errors.position = validateNumber(values.position);
    return getResolverResult(errors, values);
};

function getFormFromApi(apiData?: OlzTerminLabelData): OlzEditTerminLabelForm {
    return {
        ident: getFormString(apiData?.ident),
        name: getFormString(apiData?.name),
        details: getFormString(apiData?.details),
        icon: getFormString(apiData?.icon),
        position: getFormNumber(apiData?.position),
        imageIds: apiData?.imageIds ?? [],
        fileIds: apiData?.fileIds ?? [],
    };
}

function getApiFromForm(formData: OlzEditTerminLabelForm): OlzTerminLabelData {
    return {
        ident: getApiString(formData.ident) ?? '',
        name: getApiString(formData.name) ?? '',
        details: getApiString(formData.details) ?? '',
        icon: formData.icon ? getApiString(formData.icon) : null,
        position: getApiNumber(formData.position),
        imageIds: formData.imageIds,
        fileIds: formData.fileIds,
    };
}

// ---

interface OlzEditTerminLabelModalProps {
    id?: number;
    meta?: OlzMetaData;
    data?: OlzTerminLabelData;
}

export const OlzEditTerminLabelModal = (props: OlzEditTerminLabelModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, control} = useForm<OlzEditTerminLabelForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});
    const [isIconLoading, setIsIconLoading] = React.useState<boolean>(false);
    const [isPositionLoading, setIsPositionLoading] = React.useState<boolean>(false);
    const [isImagesLoading, setIsImagesLoading] = React.useState<boolean>(false);
    const [isFilesLoading, setIsFilesLoading] = React.useState<boolean>(false);

    const onSubmit: SubmitHandler<OlzEditTerminLabelForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);

        const [err, response] = await (props.id
            ? olzApi.getResult('updateTerminLabel', {id: props.id, meta, data})
            : olzApi.getResult('createTerminLabel', {meta, data}));
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
        const [err, response] = await olzApi.getResult('deleteTerminLabel', {id: assert(props.id)});
        if (err) {
            setStatus({id: 'DELETE_FAILED', message: `Löschen fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'DELETED'});
        // This could probably be done more smoothly!
        window.location.reload();
    } : undefined;

    const dialogTitle = (props.id === undefined
        ? 'Termin-Label erstellen'
        : 'Termin-Label bearbeiten'
    );
    const isLoading = isIconLoading || isPositionLoading || isImagesLoading || isFilesLoading;
    const editModalStatus: OlzEditModalStatus = isLoading ? {id: 'LOADING'} : status;

    return (
        <OlzEditModal
            modalId='edit-termin-label-modal'
            dialogTitle={dialogTitle}
            status={editModalStatus}
            onSubmit={handleSubmit(onSubmit)}
            onDelete={onDelete}
        >
            <div className='mb-3'>
                <OlzTextField
                    title='URL-Name'
                    name='ident'
                    errors={errors}
                    register={register}
                />
            </div>
            <div className='mb-3'>
                <OlzTextField
                    title='Name'
                    name='name'
                    errors={errors}
                    register={register}
                />
            </div>
            <div className='mb-3'>
                <OlzTextField
                    mode='textarea'
                    title={<>Details {MARKDOWN_NOTICE}</>}
                    name='details'
                    errors={errors}
                    register={register}
                />
            </div>
            <div id='icon-upload'>
                <OlzImageField
                    title='Icon'
                    name='icon'
                    errors={errors}
                    control={control}
                    setIsLoading={setIsIconLoading}
                />
            </div>
            <div className='mb-3'>
                <OlzPositionField
                    title='Position'
                    entityType='TerminLabel'
                    name='position'
                    errors={errors}
                    control={control}
                    setIsLoading={setIsPositionLoading}
                />
            </div>
            <div id='images-upload' className='mb-3'>
                <OlzMultiImageField
                    title='Bilder'
                    name='imageIds'
                    errors={errors}
                    control={control}
                    setIsLoading={setIsImagesLoading}
                />
            </div>
            <div id='files-upload' className='mb-3'>
                <OlzMultiFileField
                    title='Dateien'
                    name='fileIds'
                    errors={errors}
                    control={control}
                    setIsLoading={setIsFilesLoading}
                />
            </div>
        </OlzEditModal>
    );
};

export function initOlzEditTerminLabelModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzTerminLabelData,
): boolean {
    return initOlzEditModal('edit-termin-label-modal', () => (
        <OlzEditTerminLabelModal
            id={id}
            meta={meta}
            data={data}
        />
    ));
}
