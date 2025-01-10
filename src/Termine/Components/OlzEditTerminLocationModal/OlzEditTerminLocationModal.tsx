import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzTerminLocationData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzMultiImageField} from '../../../Components/Upload/OlzMultiImageField/OlzMultiImageField';
import {getApiNumber, getApiString, getFormNumber, getFormString, getResolverResult, validateNotEmpty, validateNumber} from '../../../Utils/formUtils';

import './OlzEditTerminLocationModal.scss';

const COMMA_LATLONG_REGEX = /^\s*([0-9.]+)\s*,\s*([0-9.]+)\s*$/;
const EMPTY_REGEX = /^\s*$/;

interface OlzEditTerminLocationForm {
    name: string;
    details: string;
    latitude: string;
    longitude: string;
    imageIds: string[];
}

const resolver: Resolver<OlzEditTerminLocationForm> = async (values) => {
    const errors: FieldErrors<OlzEditTerminLocationForm> = {};
    errors.name = validateNotEmpty(values.name);
    errors.latitude = validateNumber(values.latitude);
    errors.longitude = validateNumber(values.longitude);
    return getResolverResult(errors, values);
};

function getFormFromApi(apiData?: OlzTerminLocationData): OlzEditTerminLocationForm {
    return {
        name: getFormString(apiData?.name),
        details: getFormString(apiData?.details),
        latitude: getFormNumber(apiData?.latitude),
        longitude: getFormNumber(apiData?.longitude),
        imageIds: apiData?.imageIds ?? [],
    };
}

function getApiFromForm(formData: OlzEditTerminLocationForm): OlzTerminLocationData {
    return {
        name: getApiString(formData.name) ?? '',
        details: getApiString(formData.details) ?? '',
        latitude: getApiNumber(formData.latitude) ?? 0,
        longitude: getApiNumber(formData.longitude) ?? 0,
        imageIds: formData.imageIds,
    };
}

// ---

interface OlzEditTerminLocationModalProps {
    id?: number;
    meta?: OlzMetaData;
    data?: OlzTerminLocationData;
}

export const OlzEditTerminLocationModal = (props: OlzEditTerminLocationModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, control, setValue, watch} = useForm<OlzEditTerminLocationForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [isSubmitting, setIsSubmitting] = React.useState<boolean>(false);
    const [isImagesLoading, setIsImagesLoading] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const latitude = watch('latitude');
    const longitude = watch('longitude');

    React.useEffect(() => {
        const latMatch = COMMA_LATLONG_REGEX.exec(latitude);
        if (latMatch && EMPTY_REGEX.exec(longitude)) {
            setValue('latitude', getFormString(latMatch[1]));
            setValue('longitude', getFormString(latMatch[2]));
        }
    }, [latitude]);

    React.useEffect(() => {
        const lngMatch = COMMA_LATLONG_REGEX.exec(longitude);
        if (lngMatch && EMPTY_REGEX.exec(latitude)) {
            setValue('latitude', getFormString(lngMatch[1]));
            setValue('longitude', getFormString(lngMatch[2]));
        }
    }, [longitude]);

    const onSubmit: SubmitHandler<OlzEditTerminLocationForm> = async (values) => {
        setIsSubmitting(true);
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);

        const [err, response] = await (props.id
            ? olzApi.getResult('updateTerminLocation', {id: props.id, meta, data})
            : olzApi.getResult('createTerminLocation', {meta, data}));
        if (err) {
            setIsSubmitting(false);
            setSuccessMessage('');
            setErrorMessage(`Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`);
            return;
        }

        setSuccessMessage('Änderung erfolgreich. Bitte warten...');
        setErrorMessage('');
        // This could probably be done more smoothly!
        window.location.reload();
    };

    const dialogTitle = (props.id === undefined
        ? 'Ort-Eintrag erstellen'
        : 'Ort-Eintrag bearbeiten'
    );
    const isLoading = isImagesLoading;

    return (
        <OlzEditModal
            modalId='edit-termin-location-modal'
            dialogTitle={dialogTitle}
            successMessage={successMessage}
            errorMessage={errorMessage}
            isLoading={isLoading}
            isSubmitting={isSubmitting}
            onSubmit={handleSubmit(onSubmit)}
        >
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
                    title='Details'
                    name='details'
                    errors={errors}
                    register={register}
                />
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Breite (Latitude)'
                        name='latitude'
                        errors={errors}
                        register={register}
                    />
                </div>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Länge (Longitude)'
                        name='longitude'
                        errors={errors}
                        register={register}
                    />
                </div>
            </div>
            <div id='images-upload'>
                <OlzMultiImageField
                    title='Bilder'
                    name='imageIds'
                    errors={errors}
                    control={control}
                    setIsLoading={setIsImagesLoading}
                />
            </div>
        </OlzEditModal>
    );
};

export function initOlzEditTerminLocationModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzTerminLocationData,
): boolean {
    return initOlzEditModal('edit-termin-location-modal', () => (
        <OlzEditTerminLocationModal
            id={id}
            meta={meta}
            data={data}
        />
    ));
}
