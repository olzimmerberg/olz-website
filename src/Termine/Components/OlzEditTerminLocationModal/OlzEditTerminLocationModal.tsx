import * as bootstrap from 'bootstrap';
import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzTerminLocationData} from '../../../Api/client/generated_olz_api_types';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzMultiImageField} from '../../../Components/Upload/OlzMultiImageField/OlzMultiImageField';
import {getApiNumber, getApiString, getFormNumber, getFormString, getResolverResult, validateNotEmpty, validateNumber} from '../../../Utils/formUtils';
import {initReact} from '../../../Utils/reactUtils';

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

    const [isLoading, setIsLoading] = React.useState<boolean>(false);
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
        const meta: OlzMetaData = {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);

        const [err, response] = await (props.id
            ? olzApi.getResult('updateTerminLocation', {id: props.id, meta, data})
            : olzApi.getResult('createTerminLocation', {meta, data}));
        if (err || response.status !== 'OK') {
            setSuccessMessage('');
            setErrorMessage(`Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`);
            return;
        }

        setSuccessMessage('Änderung erfolgreich. Bitte warten...');
        setErrorMessage('');
        // TODO: This could probably be done more smoothly!
        window.location.reload();
    };

    const dialogTitle = (props.id === undefined
        ? 'Ort-Eintrag erstellen'
        : 'Ort-Eintrag bearbeiten'
    );

    return (
        <div className='modal fade' id='edit-termin-location-modal' tabIndex={-1} aria-labelledby='edit-termin-location-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={handleSubmit(onSubmit)}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-termin-location-modal-label'>
                                {dialogTitle}
                            </h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
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

export function initOlzEditTerminLocationModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzTerminLocationData,
): boolean {
    initReact('edit-entity-react-root', (
        <OlzEditTerminLocationModal
            id={id}
            meta={meta}
            data={data}
        />
    ));
    window.setTimeout(() => {
        const modal = document.getElementById('edit-termin-location-modal');
        if (modal) {
            new bootstrap.Modal(modal, {backdrop: 'static'}).show();
        }
    }, 1);
    return false;
}
