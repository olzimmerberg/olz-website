import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzWeeklyPictureData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal, OlzEditModalStatus} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzImageField} from '../../../Components/Upload/OlzImageField/OlzImageField';
import {assert} from '../../../Utils/generalUtils';

import './OlzEditWeeklyPictureModal.scss';

interface OlzEditWeeklyPictureForm {
    text: string;
    imageId: string | undefined;
}

const resolver: Resolver<OlzEditWeeklyPictureForm> = async (values) => {
    const errors: FieldErrors<OlzEditWeeklyPictureForm> = {};
    if (!values.imageId) {
        errors.imageId = {type: 'required', message: 'Darf nicht leer sein.'};
    }
    return {
        values: Object.keys(errors).length > 0 ? {} : values,
        errors,
    };
};

function getFormFromApi(apiData?: OlzWeeklyPictureData): OlzEditWeeklyPictureForm {
    return {
        text: apiData?.text ?? '',
        imageId: apiData?.imageId,
    };
}

function getApiFromForm(formData: OlzEditWeeklyPictureForm): OlzWeeklyPictureData {
    return {
        text: formData.text,
        imageId: formData.imageId ?? '',
        publishedDate: null,
    };
}

// ---

interface OlzEditWeeklyPictureModalProps {
    id?: number;
    meta?: OlzMetaData;
    data?: OlzWeeklyPictureData;
}

export const OlzEditWeeklyPictureModal = (props: OlzEditWeeklyPictureModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, control} = useForm<OlzEditWeeklyPictureForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});
    const [isImageLoading, setIsImageLoading] = React.useState<boolean>(false);

    const onSubmit: SubmitHandler<OlzEditWeeklyPictureForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);
        const [err, response] = await (props.id
            ? olzApi.getResult('updateWeeklyPicture', {id: props.id, meta, data})
            : olzApi.getResult('createWeeklyPicture', {meta, data}));
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
        const [err, response] = await olzApi.getResult('deleteWeeklyPicture', {id: assert(props.id)});
        if (err) {
            setStatus({id: 'DELETE_FAILED', message: `Löschen fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'DELETED'});
        // This could probably be done more smoothly!
        window.location.reload();
    } : undefined;

    const dialogTitle = 'Bild der Woche bearbeiten';
    const editModalStatus: OlzEditModalStatus = isImageLoading ? {id: 'LOADING'} : status;

    return (
        <OlzEditModal
            modalId='edit-weekly-picture-modal'
            dialogTitle={dialogTitle}
            status={editModalStatus}
            onSubmit={handleSubmit(onSubmit)}
            onDelete={onDelete}
        >
            <div className='mb-3'>
                <OlzTextField
                    title='Text'
                    name='text'
                    errors={errors}
                    register={register}
                />
            </div>
            <div id='image-upload'>
                <OlzImageField
                    title='Bild'
                    name='imageId'
                    errors={errors}
                    control={control}
                    setIsLoading={setIsImageLoading}
                />
            </div>
        </OlzEditModal>
    );
};

export function initOlzEditWeeklyPictureModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzWeeklyPictureData,
): boolean {
    return initOlzEditModal('edit-weekly-picture-modal', () => (
        <OlzEditWeeklyPictureModal id={id} meta={meta} data={data} />
    ));
}
