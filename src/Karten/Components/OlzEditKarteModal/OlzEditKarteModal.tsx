import * as bootstrap from 'bootstrap';
import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzKarteData, OlzKarteKind} from '../../../Api/client/generated_olz_api_types';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzImageField} from '../../../Components/Upload/OlzImageField/OlzImageField';
import {getApiNumber, getApiString, getFormNumber, getFormString, getResolverResult, validateIntegerOrNull, validateNotEmpty, validateNumber} from '../../../Utils/formUtils';
import {initReact} from '../../../Utils/reactUtils';

import './OlzEditKarteModal.scss';

interface OlzEditKarteForm {
    kartenNr: string,
    name: string,
    latitude: string,
    longitude: string,
    year: string,
    scale: string,
    place: string,
    zoom: string,
    kind: string,
    previewImageId: string,
}

const resolver: Resolver<OlzEditKarteForm> = async (values) => {
    const errors: FieldErrors<OlzEditKarteForm> = {};
    errors.kartenNr = validateIntegerOrNull(values.kartenNr);
    errors.name = validateNotEmpty(values.name);
    errors.latitude = validateNumber(values.latitude);
    errors.longitude = validateNumber(values.longitude);
    errors.year = validateIntegerOrNull(values.year);
    errors.zoom = validateIntegerOrNull(values.zoom);
    return getResolverResult(errors, values);
};

function getFormFromApi(apiData?: OlzKarteData): OlzEditKarteForm {
    return {
        kartenNr: getFormNumber(apiData?.kartennr),
        name: getFormString(apiData?.name),
        latitude: getFormNumber(apiData?.latitude),
        longitude: getFormNumber(apiData?.longitude),
        year: getFormNumber(apiData?.year),
        scale: getFormString(apiData?.scale),
        place: getFormString(apiData?.place),
        zoom: getFormNumber(apiData?.zoom),
        kind: getFormString(apiData?.kind),
        previewImageId: getFormString(apiData?.previewImageId),
    };
}

function getApiFromForm(formData: OlzEditKarteForm): OlzKarteData {
    const kindByString: {[value: string]: OlzKarteKind} = {
        ol: 'ol',
        stadt: 'stadt',
        scool: 'scool',
    };
    return {
        kartennr: getApiNumber(formData?.kartenNr),
        name: getApiString(formData?.name) ?? '',
        latitude: getApiNumber(formData?.latitude),
        longitude: getApiNumber(formData?.longitude),
        year: getApiNumber(formData?.year),
        scale: getApiString(formData?.scale),
        place: getApiString(formData?.place),
        zoom: getApiNumber(formData?.zoom),
        kind: kindByString?.[formData?.kind ?? 'ol'] ?? 'ol',
        previewImageId: getApiString(formData?.previewImageId),
    };
}

// ---

interface OlzEditKarteModalProps {
    id?: number;
    meta?: OlzMetaData;
    data?: OlzKarteData;
}

export const OlzEditKarteModal = (props: OlzEditKarteModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, control} = useForm<OlzEditKarteForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [isImageLoading, setIsImageLoading] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const onSubmit: SubmitHandler<OlzEditKarteForm> = async (values) => {
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);

        const [err, response] = await (props.id
            ? olzApi.getResult('updateKarte', {id: props.id, meta, data})
            : olzApi.getResult('createKarte', {meta, data}));
        if (err || response.status !== 'OK') {
            setSuccessMessage('');
            setErrorMessage(`Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`);
            return;
        }

        setSuccessMessage('Änderung erfolgreich. Bitte warten...');
        setErrorMessage('');
        // This could probably be done more smoothly!
        window.location.reload();
    };

    const dialogTitle = props.id === undefined
        ? 'Karte erstellen'
        : 'Karte bearbeiten';
    const isLoading = isImageLoading;

    return (
        <div className='modal fade' id='edit-karte-modal' tabIndex={-1} aria-labelledby='edit-karte-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={handleSubmit(onSubmit)}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-karte-modal-label'>
                                {dialogTitle}
                            </h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
                            <div className='mb-3'>
                                <OlzTextField
                                    title='Karten-Nummer'
                                    name='kartenNr'
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
                            <div className='row'>
                                <div className='col mb-3'>
                                    <OlzTextField
                                        title='Stand'
                                        name='year'
                                        errors={errors}
                                        register={register}
                                    />
                                </div>
                                <div className='col mb-3'>
                                    <OlzTextField
                                        title='Massstab'
                                        name='scale'
                                        errors={errors}
                                        register={register}
                                    />
                                </div>
                            </div>
                            <div className='mb-3'>
                                <label htmlFor='kind-container'>Typ</label>
                                <div id='kind-container'>
                                    <span className='kind-option'>
                                        <input
                                            type='radio'
                                            {...register('kind')}
                                            value='ol'
                                            id='isKindOl-input'
                                        />
                                        <label htmlFor='isKindOl-input'>OL</label>
                                    </span>
                                    <span className='kind-option'>
                                        <input
                                            type='radio'
                                            {...register('kind')}
                                            value='stadt'
                                            id='isKindStadt-input'
                                        />
                                        <label htmlFor='isKindStadt-input'>Stadt</label>
                                    </span>
                                    <span className='kind-option'>
                                        <input
                                            type='radio'
                                            {...register('kind')}
                                            value='scool'
                                            id='isKindScool-input'
                                        />
                                        <label htmlFor='isKindScool-input'>sCOOL</label>
                                    </span>
                                </div>
                            </div>
                            <div className='row'>
                                <div className='col mb-3'>
                                    <OlzTextField
                                        title='Ort'
                                        name='place'
                                        errors={errors}
                                        register={register}
                                    />
                                </div>
                                <div className='col mb-3'>
                                    <OlzTextField
                                        title='Zoom (z.B. 8, für sCOOL 2)'
                                        name='zoom'
                                        errors={errors}
                                        register={register}
                                    />
                                </div>
                            </div>
                            <div id='images-upload'>
                                <OlzImageField
                                    title='Vorschau-Bild'
                                    name='previewImageId'
                                    errors={errors}
                                    control={control}
                                    setIsLoading={setIsImageLoading}
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
                                className={'btn btn-primary'}
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

export function initOlzEditKarteModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzKarteData,
): boolean {
    initReact('edit-entity-react-root', (
        <OlzEditKarteModal
            id={id}
            meta={meta}
            data={data}
        />
    ));
    window.setTimeout(() => {
        const modal = document.getElementById('edit-karte-modal');
        if (modal) {
            new bootstrap.Modal(modal, {backdrop: 'static'}).show();
        }
    }, 1);
    return false;
}
