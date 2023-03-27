import React from 'react';
import {OlzApiResponses} from '../../../../Api/client';
import {olzDefaultFormSubmit, OlzRequestFieldResult, GetDataForRequestFunction, getRequired, validFieldResult, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData} from '../../../../Components/Common/OlzDefaultForm/OlzDefaultForm';
import {OlzImageUploader} from '../../../../Components/Upload/OlzImageUploader/OlzImageUploader';

import './OlzPanini2024.scss';

const RESIDENCES = [
    'Adliswil',
    'Einsiedeln',
    'Hirzel',
    'Horgen',
    'Hütten',
    'Kilchberg',
    'Langnau am Albis',
    'Oberrieden',
    'Richterswil',
    'Rüschlikon',
    'Schönenberg',
    'Thalwil',
    'Wädenswil',
    'Zürich',
];

export const OlzPanini2024 = (): React.ReactElement => {
    const existingFirstName = (window as unknown as {olzPanini2024FirstName: string}).olzPanini2024FirstName;
    const existingLastName = (window as unknown as {olzPanini2024LastName: string}).olzPanini2024LastName;
    const existingPicture = (window as unknown as {olzPanini2024Picture?: {
        id: number,
        line1: string,
        line2: string,
        association: string,
        imgSrc: string,
        infos: string[],
    }}).olzPanini2024Picture;

    let existingResidenceOption = undefined;
    let existingResidence = undefined;
    if (existingPicture) {
        if (RESIDENCES.includes(existingPicture.association)) {
            existingResidenceOption = existingPicture.association;
        } else {
            existingResidenceOption = 'OTHER';
            existingResidence = existingPicture.association;
        }
    }

    const [onOff, setOnOff] = React.useState<boolean>(true);
    const [uploadId, setUploadId] = React.useState<string|undefined>(existingPicture?.imgSrc ?? undefined);
    const [line1, setLine1] = React.useState<string>(existingPicture?.line1 ?? existingFirstName ?? '');
    const [line2, setLine2] = React.useState<string>(existingPicture?.line2 ?? existingLastName ?? '');
    const [residenceOption, setResidenceOption] = React.useState<string|undefined>(existingResidenceOption);
    const [residence, setResidence] = React.useState<string|undefined>(existingResidence);
    const [info1, setInfo1] = React.useState<string>(existingPicture?.infos[0] ?? '');
    const [info2, setInfo2] = React.useState<string>(existingPicture?.infos[1] ?? '');
    const [info3, setInfo3] = React.useState<string>(existingPicture?.infos[2] ?? '');
    const [info4, setInfo4] = React.useState<string>(existingPicture?.infos[3] ?? '');
    const [info5, setInfo5] = React.useState<string>(existingPicture?.infos[4] ?? '');

    const handleSubmit = React.useCallback((event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        const calculatedResidence = residenceOption === 'OTHER' ? residence : residenceOption;
        const getDataForRequestFn: GetDataForRequestFunction<'updateMyPanini2024'> = () => {
            const fieldResults: OlzRequestFieldResult<'updateMyPanini2024'> = {
                data: {
                    id: validFieldResult('id', existingPicture?.id ?? null),
                    onOff: validFieldResult('on-off', onOff),
                    uploadId: getRequired(validFieldResult('upload-id', uploadId)),
                    line1: validFieldResult('line1', line1),
                    line2: validFieldResult('line2', line2),
                    residence: getRequired(validFieldResult('residence', calculatedResidence)),
                    info1: validFieldResult('info1', info1),
                    info2: validFieldResult('info2', info2),
                    info3: validFieldResult('info3', info3),
                    info4: validFieldResult('info4', info4),
                    info5: validFieldResult('info5', info5),
                },
            };
            if (!isFieldResultOrDictThereofValid(fieldResults)) {
                return invalidFormData(getFieldResultOrDictThereofErrors(fieldResults));
            }
            return validFormData(getFieldResultOrDictThereofValue(fieldResults));
        };

        const handleUpdateResponse = (_response: OlzApiResponses['updateMyPanini2024']): string|void => {
            window.setTimeout(() => {
                // TODO: This could probably be done more smoothly!
                window.location.reload();
            }, 3000);
            return 'Panini-Bildli erfolgreich geändert. Bitte warten...';
        };

        olzDefaultFormSubmit(
            'updateMyPanini2024',
            getDataForRequestFn,
            event.currentTarget,
            handleUpdateResponse,
        );
    }, [onOff, uploadId, line1, line2, residenceOption, residence, info1, info2, info3, info4, info5]);

    return (
        <form className='default-form' onSubmit={handleSubmit}>
            <div className='row'>
                <div className='col mb-3'>
                    <input
                        type='checkbox'
                        name='on-off'
                        value='yes'
                        checked={onOff}
                        onChange={(e) => setOnOff(e.target.checked)}
                        id='panini-on-off-input'
                    />
                &nbsp;
                    <b>Ich bin Mitglied der OL Zimmerberg und nehme beim OLZ Panini-Album 2024 teil!</b>
                </div>
            </div>
            <div className='row'>
                <div id='panini-picture-upload'>
                    <b>Action-Bildli</b>
                    <OlzImageUploader
                        maxImageSize={4000}
                        initialUploadId={uploadId}
                        onUploadIdChange={(newUploadId) => {
                            setUploadId(newUploadId ?? undefined);
                        }}
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <label htmlFor='panini-line1-input'>Vorname</label>
                    <input
                        type='text'
                        name='line1'
                        value={line1}
                        onChange={(e) => setLine1(e.target.value)}
                        className='form-control'
                        id='panini-line1-input'
                    />
                </div>
                <div className='col mb-3'>
                    <label htmlFor='panini-line2-input'>Name</label>
                    <input
                        type='text'
                        name='line2'
                        value={line2}
                        onChange={(e) => setLine2(e.target.value)}
                        className='form-control'
                        id='panini-line2-input'
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <label htmlFor='panini-residence-select'>Wohnort</label>
                    <select
                        name='residence'
                        className='form-control form-select'
                        id='panini-residence-select'
                        defaultValue={residenceOption ?? 'UNDEFINED'}
                        onChange={(e) => {
                            const select = e.target;
                            const newResidenceOption = select.options[select.selectedIndex].value;
                            setResidenceOption(newResidenceOption);
                        }}
                    >
                        <option disabled value='UNDEFINED'>
                        Bitte wählen...
                        </option>
                        {RESIDENCES.map((formatOption) => (
                            <option value={formatOption}>
                                {formatOption}
                            </option>
                        ))}
                        <option value='OTHER'>
                        Andere...
                        </option>
                    </select>
                </div>
                <div className='col mb-3'>
                    {residenceOption === 'OTHER' ? (<>
                        <label htmlFor='panini-residence-input'>Wohnort</label>
                        <input
                            type='text'
                            name='residence'
                            value={residence}
                            onChange={(e) => setResidence(e.target.value)}
                            className='form-control'
                            id='panini-residence-input'
                        />
                    </>) : null}
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <label htmlFor='panini-info1-input'>Lieblings OL Karte</label>
                    <input
                        type='text'
                        name='info1'
                        value={info1}
                        onChange={(e) => setInfo1(e.target.value)}
                        className='form-control'
                        id='panini-info1-input'
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <label htmlFor='panini-info2-input'>
                    Mein Highlight aus 18 Jahren OL Zimmerberg
                    </label>
                    <input
                        type='text'
                        name='info2'
                        value={info2}
                        onChange={(e) => setInfo2(e.target.value)}
                        className='form-control'
                        id='panini-info2-input'
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <label htmlFor='panini-info3-input'>
                    Wie bist du zum OL gekommen?
                    </label>
                    <input
                        type='text'
                        name='info3'
                        value={info3}
                        onChange={(e) => setInfo3(e.target.value)}
                        className='form-control'
                        id='panini-info3-input'
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <label htmlFor='panini-info4-input'>
                    Seit wann machst du OL?
                    </label>
                    <input
                        type='text'
                        name='info4'
                        value={info4}
                        onChange={(e) => setInfo4(e.target.value)}
                        className='form-control'
                        id='panini-info4-input'
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <label htmlFor='panini-info5-input'>
                    Mein Motto / Was ich schon immer sagen wollte
                    </label>
                    <input
                        type='text'
                        name='info5'
                        value={info5}
                        onChange={(e) => setInfo5(e.target.value)}
                        className='form-control'
                        id='panini-info5-input'
                    />
                </div>
            </div>
            <div className='success-message alert alert-success' role='alert'></div>
            <div className='error-message alert alert-danger' role='alert'></div>
            <button
                type='submit'
                className='btn btn-primary'
                id='submit-button'
            >
            Speichern
            </button>
        </form>
    );
};