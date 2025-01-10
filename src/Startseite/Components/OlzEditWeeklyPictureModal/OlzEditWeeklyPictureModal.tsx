import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzWeeklyPictureData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzImageField} from '../../../Components/Upload/OlzImageField/OlzImageField';

import './OlzEditWeeklyPictureModal.scss';

interface OlzEditWeeklyPictureForm {
    text: string;
    imageId: string|undefined;
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

    const [isSubmitting, setIsSubmitting] = React.useState<boolean>(false);
    const [isImageLoading, setIsImageLoading] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const onSubmit: SubmitHandler<OlzEditWeeklyPictureForm> = async (values) => {
        setIsSubmitting(true);
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

    const dialogTitle = 'Bild der Woche bearbeiten';
    const isLoading = isImageLoading;

    return (
        <OlzEditModal
            modalId='edit-weekly-picture-modal'
            dialogTitle={dialogTitle}
            successMessage={successMessage}
            errorMessage={errorMessage}
            isLoading={isLoading}
            isSubmitting={isSubmitting}
            onSubmit={handleSubmit(onSubmit)}
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
