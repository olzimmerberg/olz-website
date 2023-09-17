import * as bootstrap from 'bootstrap';
import React from 'react';
import {createRoot} from 'react-dom/client';
import {OlzApiResponses} from '../../../../src/Api/client';
import {OlzMetaData, OlzTerminLocationData} from '../../../../src/Api/client/generated_olz_api_types';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, getRequired, getStringOrEmpty, getFormField, validFieldResult, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData, getNumber} from '../../../Components/Common/OlzDefaultForm/OlzDefaultForm';
import {OlzMultiImageUploader} from '../../../Components/Upload/OlzMultiImageUploader/OlzMultiImageUploader';

import './OlzEditTerminLocationModal.scss';
import { codeHref } from '../../../Utils/constants';

interface OlzEditTerminLocationModalProps {
    id?: number;
    meta?: OlzMetaData;
    data?: OlzTerminLocationData;
}

export const OlzEditTerminLocationModal = (props: OlzEditTerminLocationModalProps): React.ReactElement => {
    const [name, setName] = React.useState<string>(props.data?.name ?? '');
    const [details, setDetails] = React.useState<string>(props.data?.details ?? '');
    const [latitude, setLatitude] = React.useState<string>(props.data?.latitude ? String(props.data?.latitude) : '');
    const [longitude, setLongitude] = React.useState<string>(props.data?.longitude ? String(props.data?.longitude) : '');
    const [imageIds, setImageIds] = React.useState<string[]>(props.data?.imageIds ?? []);

    const onSubmit = React.useCallback((event: React.FormEvent<HTMLFormElement>): boolean => {
        event.preventDefault();
        const form = event.currentTarget;

        if (props.id) {
            const getDataForRequestFn: GetDataForRequestFunction<'updateTerminLocation'> = (f) => {
                const fieldResults: OlzRequestFieldResult<'updateTerminLocation'> = {
                    id: getRequired(validFieldResult('', props.id)),
                    meta: {
                        ownerUserId: validFieldResult('', null),
                        ownerRoleId: validFieldResult('', null),
                        onOff: validFieldResult('', true),
                    },
                    data: {
                        name: getRequired(getFormField(f, 'name')),
                        details: getStringOrEmpty(getFormField(f, 'details')),
                        latitude: getRequired(getNumber(getFormField(f, 'latitude'))),
                        longitude: getRequired(getNumber(getFormField(f, 'longitude'))),
                        imageIds: validFieldResult('', imageIds),
                    },
                };
                if (!isFieldResultOrDictThereofValid(fieldResults)) {
                    return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
                }
                return validFormData(getFieldResultOrDictThereofValue(fieldResults));
            };

            const handleUpdateResponse = (_response: OlzApiResponses['updateTerminLocation']): string|void => {
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.reload();
                }, 1000);
                return 'Ort-Eintrag erfolgreich geändert. Bitte warten...';
            };

            olzDefaultFormSubmit(
                'updateTerminLocation',
                getDataForRequestFn,
                form,
                handleUpdateResponse,
            );
        } else {
            const getDataForRequestFn: GetDataForRequestFunction<'createTerminLocation'> = (f) => {
                const fieldResults: OlzRequestFieldResult<'createTerminLocation'> = {
                    meta: {
                        ownerUserId: validFieldResult('', null),
                        ownerRoleId: validFieldResult('', null),
                        onOff: validFieldResult('', true),
                    },
                    data: {
                        name: getRequired(getFormField(f, 'name')),
                        details: getStringOrEmpty(getFormField(f, 'details')),
                        latitude: getRequired(getNumber(getFormField(f, 'latitude'))),
                        longitude: getRequired(getNumber(getFormField(f, 'longitude'))),
                        imageIds: validFieldResult('', imageIds),
                    },
                };
                if (!isFieldResultOrDictThereofValid(fieldResults)) {
                    return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
                }
                return validFormData(getFieldResultOrDictThereofValue(fieldResults));
            };

            const handleCreateResponse = (response: OlzApiResponses['createTerminLocation']): string|void => {
                if (response.status === 'ERROR') {
                    throw new Error(`Fehler beim Erstellen des Ort-Eintrags: ${response.status}`);
                } else if (response.status !== 'OK') {
                    throw new Error(`Antwort: ${response.status}`);
                }
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.href = `${codeHref}termine/orte/${response.id}`;
                }, 1000);
                return 'Ort-Eintrag erfolgreich erstellt. Bitte warten...';
            };

            olzDefaultFormSubmit(
                'createTerminLocation',
                getDataForRequestFn,
                form,
                handleCreateResponse,
            );
        }

        return false;
    }, [name, details, latitude, longitude, imageIds]);

    const dialogTitle = (props.id === undefined
        ? 'Ort-Eintrag erstellen'
        : 'Ort-Eintrag bearbeiten'
    );

    return (
        <div className='modal fade' id='edit-termin-location-modal' tabIndex={-1} aria-labelledby='edit-termin-location-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={onSubmit}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-termin-location-modal-label'>
                                {dialogTitle}
                            </h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
                            <div className='mb-3'>
                                <label htmlFor='termin-location-name-input'>Name</label>
                                <input
                                    type='text'
                                    name='name'
                                    value={name}
                                    onChange={(e) => setName(e.target.value)}
                                    className='form-control'
                                    id='termin-location-name-input'
                                />
                            </div>
                            <div className='mb-3'>
                                <label htmlFor='termin-location-details-input'>Details</label>
                                <textarea
                                    name='details'
                                    value={details}
                                    onChange={(e) => setDetails(e.target.value)}
                                    className='form-control'
                                    id='termin-location-details-input'
                                />
                            </div>
                            <div className='row'>
                                <div className='col mb-3'>
                                    <label htmlFor='termin-location-latitude-input'>Breite (Latitude)</label>
                                    <input
                                        type='text'
                                        name='latitude'
                                        value={latitude || ''}
                                        onChange={(e) => setLatitude(e.target.value)}
                                        className='form-control'
                                        id='termin-location-latitude-input'
                                    />
                                </div>
                                <div className='col mb-3'>
                                    <label htmlFor='termin-location-longitude-input'>Länge (Longitude)</label>
                                    <input
                                        type='text'
                                        name='longitude'
                                        value={longitude || ''}
                                        onChange={(e) => setLongitude(e.target.value)}
                                        className='form-control'
                                        id='termin-location-longitude-input'
                                    />
                                </div>
                            </div>
                            <div id='termin-location-images-upload'>
                                <b>Bilder</b>
                                <OlzMultiImageUploader
                                    initialUploadIds={imageIds}
                                    onUploadIdsChange={setImageIds}
                                />
                            </div>
                            <div className='success-message alert alert-success' role='alert'></div>
                            <div className='error-message alert alert-danger' role='alert'></div>
                        </div>
                        <div className='modal-footer'>
                            <button type='button' className='btn btn-secondary' data-bs-dismiss='modal'>Abbrechen</button>
                            <button
                                type='submit'
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

let editTerminLocationModalRoot: ReturnType<typeof createRoot>|null = null;

export function initOlzEditTerminLocationModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzTerminLocationData,
): boolean {
    const rootElem = document.getElementById('edit-termin-location-react-root');
    if (!rootElem) {
        return false;
    }
    if (editTerminLocationModalRoot) {
        editTerminLocationModalRoot.unmount();
    }
    editTerminLocationModalRoot = createRoot(rootElem);
    editTerminLocationModalRoot.render(
        <OlzEditTerminLocationModal
            id={id}
            meta={meta}
            data={data}
        />,
    );
    window.setTimeout(() => {
        const modal = document.getElementById('edit-termin-location-modal');
        if (modal) {
            new bootstrap.Modal(modal, {backdrop: 'static'}).show();
        }
    }, 1);
    return false;
}
