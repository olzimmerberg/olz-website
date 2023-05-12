import * as bootstrap from 'bootstrap';
import React from 'react';
import ReactDOM from 'react-dom';
import {OlzApiResponses} from '../../../../src/Api/client';
import {OlzMetaData, OlzTerminData} from '../../../../src/Api/client/generated_olz_api_types';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, getInteger, getIsoDateTime, getIsoDate, getIsoTime, getRequired, getStringOrEmpty, getStringOrNull, getFormField, validFieldResult, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData} from '../../../Components/Common/OlzDefaultForm/OlzDefaultForm';
import {OlzMultiFileUploader} from '../../../Components/Upload/OlzMultiFileUploader/OlzMultiFileUploader';
// import {OlzMultiImageUploader} from '../../../Components/Upload/OlzMultiImageUploader/OlzMultiImageUploader';
import {isoNow} from '../../../Utils/constants';

import './OlzEditTerminModal.scss';

interface OlzEditTerminModalProps {
    id?: number;
    meta?: OlzMetaData;
    data?: OlzTerminData;
}

export const OlzEditTerminModal = (props: OlzEditTerminModalProps): React.ReactElement => {
    const [startDate, setStartDate] = React.useState<string>(props.data?.startDate ?? isoNow.substring(0, 10));
    const [startTime, setStartTime] = React.useState<string|null>(props.data?.startTime ?? null);
    const [endDate, setEndDate] = React.useState<string|null>(props.data?.endDate ?? null);
    const [endTime, setEndTime] = React.useState<string|null>(props.data?.endTime ?? null);
    const [title, setTitle] = React.useState<string>(props.data?.title ?? '');
    const [text, setText] = React.useState<string>(props.data?.text ?? '');
    const [link, setLink] = React.useState<string>(props.data?.link ?? '');
    const [deadline, setDeadline] = React.useState<string>(props.data?.deadline ?? '');
    const [hasNewsletter, setHasNewsletter] = React.useState<boolean>(props.data?.newsletter ?? false);
    const [solvId, setSolvId] = React.useState<string>(props.data?.solvId ?? '');
    const [go2olId, setGo2olId] = React.useState<string>(props.data?.go2olId ?? '');
    const [types, setTypes] = React.useState<Set<string>>(new Set(props.data?.types));
    const [coordinateX, setCoordinateX] = React.useState<string>(props.data?.coordinateX ? String(props.data?.coordinateX) : '');
    const [coordinateY, setCoordinateY] = React.useState<string>(props.data?.coordinateY ? String(props.data?.coordinateY) : '');
    const [fileIds, setFileIds] = React.useState<string[]>(props.data?.fileIds ?? []);
    // const [imageIds, setImageIds] = React.useState<string[]>(props.data?.imageIds ?? []);

    const onSubmit = React.useCallback(async (event: React.FormEvent<HTMLFormElement>): Promise<boolean> => {
        event.preventDefault();
        const form = event.currentTarget;

        if (props.id) {
            const getDataForRequestFn: GetDataForRequestFunction<'updateTermin'> = (f) => {
                const fieldResults: OlzRequestFieldResult<'updateTermin'> = {
                    id: getRequired(validFieldResult('', props.id)),
                    meta: {
                        ownerUserId: validFieldResult('', null),
                        ownerRoleId: validFieldResult('', null),
                        onOff: validFieldResult('', true),
                    },
                    data: {
                        startDate: getRequired(getIsoDate(validFieldResult('', startDate))),
                        startTime: getIsoTime(validFieldResult('', startTime)),
                        endDate: getIsoDate(validFieldResult('', endDate)),
                        endTime: getIsoTime(validFieldResult('', endTime)),
                        title: getStringOrEmpty(getFormField(f, 'title')),
                        text: getStringOrEmpty(getFormField(f, 'text')),
                        link: getStringOrEmpty(getFormField(f, 'link')),
                        deadline: getIsoDateTime(getFormField(f, 'deadline')),
                        newsletter: validFieldResult('', hasNewsletter),
                        solvId: getStringOrNull(getFormField(f, 'solv-id')),
                        go2olId: getStringOrNull(getFormField(f, 'go2ol-id')),
                        types: validFieldResult('', Array.from(types)),
                        coordinateX: getInteger(getFormField(f, 'coordinate-x')),
                        coordinateY: getInteger(getFormField(f, 'coordinate-y')),
                        fileIds: validFieldResult('', fileIds),
                    },
                };
                if (!isFieldResultOrDictThereofValid(fieldResults)) {
                    return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
                }
                return validFormData(getFieldResultOrDictThereofValue(fieldResults));
            };

            const handleUpdateResponse = (_response: OlzApiResponses['updateTermin']): string|void => {
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.reload();
                }, 3000);
                return 'Termin-Eintrag erfolgreich geändert. Bitte warten...';
            };

            olzDefaultFormSubmit(
                'updateTermin',
                getDataForRequestFn,
                form,
                handleUpdateResponse,
            );
        } else {
            const getDataForRequestFn: GetDataForRequestFunction<'createTermin'> = (f) => {
                const fieldResults: OlzRequestFieldResult<'createTermin'> = {
                    meta: {
                        ownerUserId: validFieldResult('', null),
                        ownerRoleId: validFieldResult('', null),
                        onOff: validFieldResult('', true),
                    },
                    data: {
                        startDate: getRequired(getIsoDate(validFieldResult('', startDate))),
                        startTime: getIsoTime(validFieldResult('', startTime)),
                        endDate: getIsoDate(validFieldResult('', endDate)),
                        endTime: getIsoTime(validFieldResult('', endTime)),
                        title: getStringOrEmpty(getFormField(f, 'title')),
                        text: getStringOrEmpty(getFormField(f, 'text')),
                        link: getStringOrEmpty(getFormField(f, 'link')),
                        deadline: getIsoDateTime(getFormField(f, 'deadline')),
                        newsletter: validFieldResult('', hasNewsletter),
                        solvId: getStringOrNull(getFormField(f, 'solv-id')),
                        go2olId: getStringOrNull(getFormField(f, 'go2ol-id')),
                        types: validFieldResult('', Array.from(types)),
                        coordinateX: getInteger(getFormField(f, 'coordinate-x')),
                        coordinateY: getInteger(getFormField(f, 'coordinate-y')),
                        fileIds: validFieldResult('', fileIds),
                    },
                };
                if (!isFieldResultOrDictThereofValid(fieldResults)) {
                    return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
                }
                return validFormData(getFieldResultOrDictThereofValue(fieldResults));
            };

            const handleCreateResponse = (response: OlzApiResponses['createTermin']): string|void => {
                if (response.status === 'ERROR') {
                    throw new Error(`Fehler beim Erstellen des Termin-Eintrags: ${response.status}`);
                } else if (response.status !== 'OK') {
                    throw new Error(`Antwort: ${response.status}`);
                }
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.reload();
                }, 3000);
                return 'Termin-Eintrag erfolgreich erstellt. Bitte warten...';
            };

            olzDefaultFormSubmit(
                'createTermin',
                getDataForRequestFn,
                form,
                handleCreateResponse,
            );
        }

        return false;
    }, [startDate, startTime, fileIds]);

    const dialogTitle = (props.id === undefined
        ? 'Termin-Eintrag erstellen'
        : 'Termin-Eintrag bearbeiten'
    );

    return (
        <div className='modal fade' id='edit-termin-modal' tabIndex={-1} aria-labelledby='edit-termin-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={onSubmit}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-termin-modal-label'>
                                {dialogTitle}
                            </h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
                            <div className='row'>
                                <div className='col mb-3'>
                                    <label htmlFor='termin-start-date-input'>Beginn Datum</label>
                                    <input
                                        type='text'
                                        name='start-date'
                                        value={startDate || ''}
                                        onChange={(e) => setStartDate(e.target.value)}
                                        className='form-control'
                                        id='termin-start-date-input'
                                    />
                                </div>
                                <div className='col mb-3'>
                                    <label htmlFor='termin-start-time-input'>Beginn Zeit</label>
                                    <input
                                        type='text'
                                        name='start-time'
                                        value={startTime || ''}
                                        onChange={(e) => setStartTime(e.target.value)}
                                        className='form-control'
                                        id='termin-start-time-input'
                                    />
                                </div>
                            </div>
                            <div className='row'>
                                <div className='col mb-3'>
                                    <label htmlFor='termin-end-date-input'>Ende Datum</label>
                                    <input
                                        type='text'
                                        name='end-date'
                                        value={endDate || ''}
                                        onChange={(e) => setEndDate(e.target.value)}
                                        className='form-control'
                                        id='termin-end-date-input'
                                    />
                                </div>
                                <div className='col mb-3'>
                                    <label htmlFor='termin-end-time-input'>Ende Zeit</label>
                                    <input
                                        type='text'
                                        name='end-time'
                                        value={endTime || ''}
                                        onChange={(e) => setEndTime(e.target.value)}
                                        className='form-control'
                                        id='termin-end-time-input'
                                    />
                                </div>
                            </div>
                            <div className='mb-3'>
                                <label htmlFor='termin-title-input'>Titel</label>
                                <input
                                    type='text'
                                    name='title'
                                    value={title}
                                    onChange={(e) => setTitle(e.target.value)}
                                    className='form-control'
                                    id='termin-title-input'
                                />
                            </div>
                            <div className='mb-3'>
                                <label htmlFor='termin-text-input'>Text</label>
                                <textarea
                                    name='text'
                                    value={text}
                                    onChange={(e) => setText(e.target.value)}
                                    className='form-control'
                                    id='termin-text-input'
                                />
                            </div>
                            <div className='mb-3'>
                                <label htmlFor='termin-link-input'>Link</label>
                                <textarea
                                    name='link'
                                    value={link}
                                    onChange={(e) => setLink(e.target.value)}
                                    className='form-control'
                                    id='termin-link-input'
                                />
                            </div>
                            <div className='mb-3'>
                                <label htmlFor='termin-deadline-input'>Meldeschluss</label>
                                <textarea
                                    name='deadline'
                                    value={deadline}
                                    onChange={(e) => setDeadline(e.target.value)}
                                    className='form-control'
                                    id='termin-deadline-input'
                                />
                            </div>
                            <div className='mb-3'>
                                <input
                                    type='checkbox'
                                    name='has-newsletter'
                                    value='yes'
                                    checked={hasNewsletter}
                                    onChange={(e) => setHasNewsletter(e.target.checked)}
                                    id='termin-has-newsletter-input'
                                />
                                <label htmlFor='termin-has-newsletter-input'>Newsletter</label>
                            </div>
                            <div className='row'>
                                <div className='col mb-3'>
                                    <label htmlFor='termin-solv-id-input'>SOLV-ID</label>
                                    <input
                                        type='text'
                                        name='solv-id'
                                        value={solvId}
                                        onChange={(e) => setSolvId(e.target.value)}
                                        className='form-control'
                                        id='termin-solv-id-input'
                                    />
                                </div>
                                <div className='col mb-3'>
                                    <label htmlFor='termin-go2ol-id-input'>GO2OL-ID</label>
                                    <input
                                        type='text'
                                        name='go2ol-id'
                                        value={go2olId}
                                        onChange={(e) => setGo2olId(e.target.value)}
                                        className='form-control'
                                        id='termin-go2ol-id-input'
                                    />
                                </div>
                            </div>
                            <div className='mb-3'>
                                <label htmlFor='termin-types-container'>Typen</label>
                                <div id='termin-types-container'>
                                    <span className='types-option'>
                                        <input
                                            type='checkbox'
                                            name='types-programm'
                                            value='yes'
                                            checked={types.has('programm')}
                                            onChange={(e) => {
                                                const newTypes = new Set(types);
                                                if (e.target.checked) {
                                                    newTypes.add('programm');
                                                } else {
                                                    newTypes.delete('programm');
                                                }
                                                setTypes(newTypes);
                                            }}
                                            id='termin-types-programm-input'
                                        />
                                        <label htmlFor='termin-types-programm-input'>
                                            Jahresprogramm
                                        </label>
                                    </span>
                                    <span className='types-option'>
                                        <input
                                            type='checkbox'
                                            name='types-weekend'
                                            value='yes'
                                            checked={types.has('weekend')}
                                            onChange={(e) => {
                                                const newTypes = new Set(types);
                                                if (e.target.checked) {
                                                    newTypes.add('weekend');
                                                } else {
                                                    newTypes.delete('weekend');
                                                }
                                                setTypes(newTypes);
                                            }}
                                            id='termin-types-weekend-input'
                                        />
                                        <label htmlFor='termin-types-weekend-input'>
                                            Weekends
                                        </label>
                                    </span>
                                    <span className='types-option'>
                                        <input
                                            type='checkbox'
                                            name='types-training'
                                            value='yes'
                                            checked={types.has('training')}
                                            onChange={(e) => {
                                                const newTypes = new Set(types);
                                                if (e.target.checked) {
                                                    newTypes.add('training');
                                                } else {
                                                    newTypes.delete('training');
                                                }
                                                setTypes(newTypes);
                                            }}
                                            id='termin-types-training-input'
                                        />
                                        <label htmlFor='termin-types-training-input'>
                                            Trainings
                                        </label>
                                    </span>
                                    <span className='types-option'>
                                        <input
                                            type='checkbox'
                                            name='types-ol'
                                            value='yes'
                                            checked={types.has('ol')}
                                            onChange={(e) => {
                                                const newTypes = new Set(types);
                                                if (e.target.checked) {
                                                    newTypes.add('ol');
                                                } else {
                                                    newTypes.delete('ol');
                                                }
                                                setTypes(newTypes);
                                            }}
                                            id='termin-types-ol-input'
                                        />
                                        <label htmlFor='termin-types-ol-input'>
                                            Wettkämpfe
                                        </label>
                                    </span>
                                    <span className='types-option'>
                                        <input
                                            type='checkbox'
                                            name='types-club'
                                            value='yes'
                                            checked={types.has('club')}
                                            onChange={(e) => {
                                                const newTypes = new Set(types);
                                                if (e.target.checked) {
                                                    newTypes.add('club');
                                                } else {
                                                    newTypes.delete('club');
                                                }
                                                setTypes(newTypes);
                                            }}
                                            id='termin-types-club-input'
                                        />
                                        <label htmlFor='termin-types-club-input'>
                                            Vereinsanlässe
                                        </label>
                                    </span>
                                </div>
                            </div>
                            <div className='row'>
                                <div className='col mb-3'>
                                    <label htmlFor='termin-coordinate-x-input'>X-Koordinate</label>
                                    <input
                                        type='text'
                                        name='coordinate-x'
                                        value={coordinateX || ''}
                                        onChange={(e) => setCoordinateX(e.target.value)}
                                        className='form-control'
                                        id='termin-coordinate-x-input'
                                    />
                                </div>
                                <div className='col mb-3'>
                                    <label htmlFor='termin-coordinate-y-input'>Y-Koordinate</label>
                                    <input
                                        type='text'
                                        name='coordinate-y'
                                        value={coordinateY || ''}
                                        onChange={(e) => setCoordinateY(e.target.value)}
                                        className='form-control'
                                        id='termin-coordinate-y-input'
                                    />
                                </div>
                            </div>
                            {/* <div id='termin-images-upload'>
                                <b>Bilder</b>
                                <OlzMultiImageUploader
                                    initialUploadIds={imageIds}
                                    onUploadIdsChange={setImageIds}
                                />
                            </div> */}
                            <div id='termin-files-upload'>
                                <b>Dateien</b>
                                <OlzMultiFileUploader
                                    initialUploadIds={fileIds}
                                    onUploadIdsChange={setFileIds}
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

export function initOlzEditTerminModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzTerminData,
): boolean {
    ReactDOM.render(
        <OlzEditTerminModal
            id={id}
            meta={meta}
            data={data}
        />,
        document.getElementById('edit-termin-react-root'),
    );
    const modal = document.getElementById('edit-termin-modal');
    if (modal) {
        new bootstrap.Modal(modal, {backdrop: 'static'}).show();
    }
    return false;
}
