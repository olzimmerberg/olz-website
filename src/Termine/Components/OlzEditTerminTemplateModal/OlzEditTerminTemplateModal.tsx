import * as bootstrap from 'bootstrap';
import React from 'react';
import {createRoot} from 'react-dom/client';
import {OlzApiResponses} from '../../../../src/Api/client';
import {OlzMetaData, OlzTerminTemplateData} from '../../../../src/Api/client/generated_olz_api_types';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, getRequired, getStringOrEmpty, getFormField, validFieldResult, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData, getInteger, getIsoTime} from '../../../Components/Common/OlzDefaultForm/OlzDefaultForm';
import {OlzEntityChooser} from '../../../Components/Common/OlzEntityChooser/OlzEntityChooser';
import {OlzMultiFileUploader} from '../../../Components/Upload/OlzMultiFileUploader/OlzMultiFileUploader';
import {OlzMultiImageUploader} from '../../../Components/Upload/OlzMultiImageUploader/OlzMultiImageUploader';
import {codeHref} from '../../../Utils/constants';

import './OlzEditTerminTemplateModal.scss';

interface OlzEditTerminTemplateModalProps {
    id?: number;
    meta?: OlzMetaData;
    data?: OlzTerminTemplateData;
}

export const OlzEditTerminTemplateModal = (props: OlzEditTerminTemplateModalProps): React.ReactElement => {
    const [startTime, setStartTime] = React.useState<string|null>(props.data?.startTime ?? null);
    const [durationSeconds, setDurationSeconds] = React.useState<string>(String(props.data?.durationSeconds ?? ''));
    const [title, setTitle] = React.useState<string>(props.data?.title ?? '');
    const [text, setText] = React.useState<string>(props.data?.text ?? '');
    const [link, setLink] = React.useState<string>(props.data?.link ?? '');
    const [deadlineEarlierSeconds, setDeadlineEarlierSeconds] = React.useState<string>(String(props.data?.deadlineEarlierSeconds ?? ''));
    const [deadlineTime, setDeadlineTime] = React.useState<string|null>(props.data?.deadlineTime ?? null);
    const [hasNewsletter, setHasNewsletter] = React.useState<boolean>(props.data?.newsletter ?? false);
    const [types, setTypes] = React.useState<Set<string>>(new Set(props.data?.types));
    const [locationId, setLocationId] = React.useState<number|null>(props.data?.locationId ?? null);
    const [fileIds, setFileIds] = React.useState<string[]>(props.data?.fileIds ?? []);
    const [imageIds, setImageIds] = React.useState<string[]>(props.data?.imageIds ?? []);

    const onSubmit = React.useCallback((event: React.FormEvent<HTMLFormElement>): boolean => {
        event.preventDefault();
        const form = event.currentTarget;

        if (props.id) {
            const getDataForRequestFn: GetDataForRequestFunction<'updateTerminTemplate'> = (f) => {
                const fieldResults: OlzRequestFieldResult<'updateTerminTemplate'> = {
                    id: getRequired(validFieldResult('', props.id)),
                    meta: {
                        ownerUserId: validFieldResult('', null),
                        ownerRoleId: validFieldResult('', null),
                        onOff: validFieldResult('', true),
                    },
                    data: {
                        startTime: getIsoTime(validFieldResult('', startTime)),
                        durationSeconds: getInteger(validFieldResult('', durationSeconds)),
                        title: getStringOrEmpty(getFormField(f, 'title')),
                        text: getStringOrEmpty(getFormField(f, 'text')),
                        link: getStringOrEmpty(getFormField(f, 'link')),
                        deadlineEarlierSeconds: getInteger(validFieldResult('', deadlineEarlierSeconds)),
                        deadlineTime: getIsoTime(validFieldResult('', deadlineTime)),
                        newsletter: validFieldResult('', hasNewsletter),
                        types: validFieldResult('', Array.from(types)),
                        locationId: validFieldResult('', locationId),
                        imageIds: validFieldResult('', imageIds),
                        fileIds: validFieldResult('', fileIds),
                    },
                };
                if (!isFieldResultOrDictThereofValid(fieldResults)) {
                    return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
                }
                return validFormData(getFieldResultOrDictThereofValue(fieldResults));
            };

            const handleUpdateResponse = (_response: OlzApiResponses['updateTerminTemplate']): string|void => {
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.reload();
                }, 1000);
                return 'Ort-Eintrag erfolgreich geändert. Bitte warten...';
            };

            olzDefaultFormSubmit(
                'updateTerminTemplate',
                getDataForRequestFn,
                form,
                handleUpdateResponse,
            );
        } else {
            const getDataForRequestFn: GetDataForRequestFunction<'createTerminTemplate'> = (f) => {
                const fieldResults: OlzRequestFieldResult<'createTerminTemplate'> = {
                    meta: {
                        ownerUserId: validFieldResult('', null),
                        ownerRoleId: validFieldResult('', null),
                        onOff: validFieldResult('', true),
                    },
                    data: {
                        startTime: getIsoTime(validFieldResult('', startTime)),
                        durationSeconds: getInteger(validFieldResult('', durationSeconds)),
                        title: getStringOrEmpty(getFormField(f, 'title')),
                        text: getStringOrEmpty(getFormField(f, 'text')),
                        link: getStringOrEmpty(getFormField(f, 'link')),
                        deadlineEarlierSeconds: getInteger(validFieldResult('', deadlineEarlierSeconds)),
                        deadlineTime: getIsoTime(validFieldResult('', deadlineTime)),
                        newsletter: validFieldResult('', hasNewsletter),
                        types: validFieldResult('', Array.from(types)),
                        locationId: validFieldResult('', locationId),
                        imageIds: validFieldResult('', imageIds),
                        fileIds: validFieldResult('', fileIds),
                    },
                };
                if (!isFieldResultOrDictThereofValid(fieldResults)) {
                    return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
                }
                return validFormData(getFieldResultOrDictThereofValue(fieldResults));
            };

            const handleCreateResponse = (response: OlzApiResponses['createTerminTemplate']): string|void => {
                if (response.status === 'ERROR') {
                    throw new Error(`Fehler beim Erstellen des Ort-Eintrags: ${response.status}`);
                } else if (response.status !== 'OK') {
                    throw new Error(`Antwort: ${response.status}`);
                }
                window.setTimeout(() => {
                    // TODO: This could probably be done more smoothly!
                    window.location.href = `${codeHref}termine/vorlagen/${response.id}`;
                }, 1000);
                return 'Ort-Eintrag erfolgreich erstellt. Bitte warten...';
            };

            olzDefaultFormSubmit(
                'createTerminTemplate',
                getDataForRequestFn,
                form,
                handleCreateResponse,
            );
        }

        return false;
    }, [startTime, durationSeconds, deadlineEarlierSeconds, deadlineTime, hasNewsletter, types, locationId, imageIds, fileIds]);

    const dialogTitle = (props.id === undefined
        ? 'Termin-Vorlage erstellen'
        : 'Termin-Vorlage bearbeiten'
    );

    return (
        <div className='modal fade' id='edit-termin-template-modal' tabIndex={-1} aria-labelledby='edit-termin-template-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={onSubmit}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-termin-template-modal-label'>
                                {dialogTitle}
                            </h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
                            <div className='row'>
                                <div className='col mb-3'>
                                    <label htmlFor='termin-template-start-time-input'>Beginn Zeit</label>
                                    <input
                                        type='text'
                                        name='start-time'
                                        value={startTime || ''}
                                        onChange={(e) => setStartTime(e.target.value)}
                                        className='form-control'
                                        id='termin-template-start-time-input'
                                    />
                                </div>
                                <div className='col mb-3'>
                                    <label htmlFor='termin-template-duration-seconds-input'>Dauer (in Sekunden)</label>
                                    <input
                                        type='text'
                                        name='duration-seconds'
                                        value={durationSeconds}
                                        onChange={(e) => setDurationSeconds(e.target.value)}
                                        className='form-control'
                                        id='termin-template-duration-seconds-input'
                                    />
                                </div>
                            </div>
                            <div className='mb-3'>
                                <label htmlFor='termin-template-title-input'>Titel</label>
                                <input
                                    type='text'
                                    name='title'
                                    value={title}
                                    onChange={(e) => setTitle(e.target.value)}
                                    className='form-control'
                                    id='termin-template-title-input'
                                />
                            </div>
                            <div className='mb-3'>
                                <label htmlFor='termin-template-text-input'>Text</label>
                                <textarea
                                    name='text'
                                    value={text}
                                    onChange={(e) => setText(e.target.value)}
                                    className='form-control'
                                    id='termin-template-text-input'
                                />
                            </div>
                            <div className='mb-3'>
                                <label htmlFor='termin-template-link-input'>Link</label>
                                <textarea
                                    name='link'
                                    value={link}
                                    onChange={(e) => setLink(e.target.value)}
                                    className='form-control'
                                    id='termin-template-link-input'
                                />
                            </div>
                            <div className='row'>
                                <div className='col mb-3'>
                                    <label htmlFor='termin-template-deadline-earlier-seconds-input'>
                                        Meldeschluss vorher (in Sekunden)
                                    </label>
                                    <input
                                        type='text'
                                        name='deadline-earlier-seconds'
                                        value={deadlineEarlierSeconds || ''}
                                        onChange={(e) => setDeadlineEarlierSeconds(e.target.value)}
                                        className='form-control'
                                        id='termin-template-deadline-earlier-seconds-input'
                                    />
                                </div>
                                <div className='col mb-3'>
                                    <label htmlFor='termin-template-deadline-time-input'>
                                        Meldeschluss Zeit
                                    </label>
                                    <input
                                        type='text'
                                        name='deadline-time'
                                        value={deadlineTime || ''}
                                        onChange={(e) => setDeadlineTime(e.target.value)}
                                        className='form-control'
                                        id='termin-template-deadline-time-input'
                                    />
                                </div>
                            </div>
                            <div className='mb-3'>
                                <input
                                    type='checkbox'
                                    name='has-newsletter'
                                    value='yes'
                                    checked={hasNewsletter}
                                    onChange={(e) => setHasNewsletter(e.target.checked)}
                                    id='termin-template-has-newsletter-input'
                                />
                                <label htmlFor='termin-template-has-newsletter-input'>Newsletter für Änderung</label>
                            </div>
                            <div className='mb-3'>
                                <label htmlFor='termin-template-types-container'>Typ</label>
                                <div id='termin-template-types-container'>
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
                                            id='termin-template-types-programm-input'
                                        />
                                        <label htmlFor='termin-template-types-programm-input'>
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
                                            id='termin-template-types-weekend-input'
                                        />
                                        <label htmlFor='termin-template-types-weekend-input'>
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
                                            id='termin-template-types-training-input'
                                        />
                                        <label htmlFor='termin-template-types-training-input'>
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
                                            id='termin-template-types-ol-input'
                                        />
                                        <label htmlFor='termin-template-types-ol-input'>
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
                                            id='termin-template-types-club-input'
                                        />
                                        <label htmlFor='termin-template-types-club-input'>
                                            Vereinsanlässe
                                        </label>
                                    </span>
                                </div>
                            </div>
                            <div className='row'>
                                <div className='col mb-3'>
                                    <b>Ort</b>
                                    <OlzEntityChooser
                                        entityType={'TerminLocation'}
                                        entityId={locationId}
                                        onEntityIdChange={(e) => setLocationId(e.detail)}
                                        nullLabel={'Kein Termin-Ort ausgewählt'}
                                    />
                                </div>
                                <div className='col mb-3'>
                                </div>
                            </div>
                            <div id='termin-template-images-upload'>
                                <b>Bilder</b>
                                <OlzMultiImageUploader
                                    initialUploadIds={imageIds}
                                    onUploadIdsChange={setImageIds}
                                />
                            </div>
                            <div id='termin-template-files-upload'>
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

let editTerminTemplateModalRoot: ReturnType<typeof createRoot>|null = null;

export function initOlzEditTerminTemplateModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzTerminTemplateData,
): boolean {
    const rootElem = document.getElementById('edit-termin-template-react-root');
    if (!rootElem) {
        return false;
    }
    if (editTerminTemplateModalRoot) {
        editTerminTemplateModalRoot.unmount();
    }
    editTerminTemplateModalRoot = createRoot(rootElem);
    editTerminTemplateModalRoot.render(
        <OlzEditTerminTemplateModal
            id={id}
            meta={meta}
            data={data}
        />,
    );
    window.setTimeout(() => {
        const modal = document.getElementById('edit-termin-template-modal');
        if (modal) {
            new bootstrap.Modal(modal, {backdrop: 'static'}).show();
        }
    }, 1);
    return false;
}
