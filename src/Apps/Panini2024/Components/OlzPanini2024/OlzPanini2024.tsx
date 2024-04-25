import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../../Api/client';
import {OlzTextField} from '../../../../Components/Common/OlzTextField/OlzTextField';
import {OlzImageField} from '../../../../Components/Upload/OlzImageField/OlzImageField';
import {OlzPanini2024PictureData} from '../../../../Api/client/generated_olz_api_types';
import {getApiBoolean, getApiString} from '../../../../Utils/formUtils';
import {timeout} from '../../../../Utils/generalUtils';
import {initReact} from '../../../../Utils/reactUtils';

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

interface OlzEditPaniniForm {
    id: number|null;
    onOff: string|boolean;
    uploadId: string|undefined;
    line1: string
    line2: string;
    residenceOption: string|undefined;
    residence: string|undefined;
    info1: string;
    info2: string;
    info3: string;
    info4: string;
    info5: string;
}

const resolver: Resolver<OlzEditPaniniForm> = async (values) => {
    const errors: FieldErrors<OlzEditPaniniForm> = {};
    if (!values.uploadId) {
        errors.uploadId = {type: 'required', message: 'Darf nicht leer sein.'};
    }
    if (values.residenceOption === 'OTHER' && !values.residence) {
        errors.residence = {type: 'required', message: 'Darf nicht leer sein.'};
    }
    return {
        values: Object.keys(errors).length > 0 ? {} : values,
        errors,
    };
};

function getApiFromForm(formData: OlzEditPaniniForm): OlzPanini2024PictureData {
    const calculatedResidence = formData.residenceOption === 'OTHER' ? formData.residence : formData.residenceOption;
    return {
        id: formData.id,
        onOff: getApiBoolean(formData.onOff),
        uploadId: getApiString(formData.uploadId ?? '') ?? '',
        line1: getApiString(formData.line1) ?? '',
        line2: getApiString(formData.line2) ?? '',
        residence: calculatedResidence ?? '',
        info1: getApiString(formData.info1) ?? '',
        info2: getApiString(formData.info2) ?? '',
        info3: getApiString(formData.info3) ?? '',
        info4: getApiString(formData.info4) ?? '',
        info5: getApiString(formData.info5) ?? '',
    };
}

// ---

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
    const isReadOnly = (window as unknown as {olzPanini2024IsReadOnly: boolean}).olzPanini2024IsReadOnly;

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

    const {register, handleSubmit, formState: {errors}, watch, control} = useForm<OlzEditPaniniForm>({
        resolver,
        defaultValues: {
            id: existingPicture?.id ?? null,
            onOff: true,
            uploadId: existingPicture?.imgSrc ?? undefined,
            line1: existingPicture?.line1 ?? existingFirstName ?? '',
            line2: existingPicture?.line2 ?? existingLastName ?? '',
            residenceOption: existingResidenceOption,
            residence: existingResidence,
            info1: existingPicture?.infos[0] ?? '',
            info2: existingPicture?.infos[1] ?? '',
            info3: existingPicture?.infos[2] ?? '',
            info4: existingPicture?.infos[3] ?? '',
            info5: existingPicture?.infos[4] ?? '',
        },
    });

    const [isImageLoading, setIsImageLoading] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const onSubmit: SubmitHandler<OlzEditPaniniForm> = async (values) => {
        const data = getApiFromForm(values);
        const [err, response] = await olzApi.getResult('updateMyPanini2024', {data});
        if (err || response.status !== 'OK') {
            setSuccessMessage('');
            setErrorMessage(`Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`);
            return;
        }

        setSuccessMessage('Änderung erfolgreich. Bitte warten...');
        setErrorMessage('');
        // TODO: This could probably be done more smoothly!
        await timeout(1000);
        window.location.reload();
    };

    const residenceOption = watch('residenceOption');
    const isLoading = isImageLoading;

    return (<>
        <form className='default-form' onSubmit={handleSubmit(onSubmit)}>
            <div className='alert alert-warning' role='alert'>
                {isReadOnly
                    ? 'Die Deadline ist abgelaufen. Du kannst die Daten nicht mehr bearbeiten, da die Bildli bereits im Druck sind. 🫤'
                    : 'Ab 30. November 2023 kannst du die Daten nicht mehr ändern!'}
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <input
                        type='checkbox'
                        value='yes'
                        {...register('onOff')}
                        disabled={isReadOnly}
                        id='panini-on-off-input'
                    />
                &nbsp;
                    <b>Ich bin Mitglied der OL Zimmerberg und nehme beim OLZ Panini-Album 2024 teil!</b>
                </div>
            </div>
            <div className='row'>
                <div id='panini-picture-upload'>
                    <OlzImageField
                        title='Action-Bildli'
                        name='uploadId'
                        errors={errors}
                        control={control}
                        setIsLoading={setIsImageLoading}
                        maxImageSize={2500}
                        disabled={isReadOnly}
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Vorname'
                        name='line1'
                        options={{disabled: isReadOnly}}
                        errors={errors}
                        register={register}
                    />
                </div>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Name'
                        name='line2'
                        options={{disabled: isReadOnly}}
                        errors={errors}
                        register={register}
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <label htmlFor='panini-residence-select'>Wohnort</label>
                    <select
                        className='form-control form-select'
                        id='panini-residence-select'
                        {...register('residenceOption')}
                        disabled={isReadOnly}
                    >
                        <option disabled value='UNDEFINED'>
                        Bitte wählen...
                        </option>
                        {RESIDENCES.map((formatOption) => (
                            <option value={formatOption} key={formatOption}>
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
                        <OlzTextField
                            title='Wohnort'
                            name='residence'
                            options={{disabled: isReadOnly}}
                            errors={errors}
                            register={register}
                        />
                    </>) : null}
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Lieblings OL Karte'
                        name='info1'
                        options={{disabled: isReadOnly}}
                        errors={errors}
                        register={register}
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Mein Highlight aus 18 Jahren OL Zimmerberg'
                        name='info2'
                        options={{disabled: isReadOnly}}
                        errors={errors}
                        register={register}
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Wie bist du zum OL gekommen?'
                        name='info3'
                        options={{disabled: isReadOnly}}
                        errors={errors}
                        register={register}
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Seit wann machst du OL?'
                        name='info4'
                        options={{disabled: isReadOnly}}
                        errors={errors}
                        register={register}
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Mein Motto / Was ich schon immer sagen wollte'
                        name='info5'
                        options={{disabled: isReadOnly}}
                        errors={errors}
                        register={register}
                    />
                </div>
            </div>
            <div className='success-message alert alert-success' role='alert'>
                {successMessage}
            </div>
            <div className='error-message alert alert-danger' role='alert'>
                {errorMessage}
            </div>
            <button
                type='submit'
                disabled={isReadOnly || isLoading}
                className='btn btn-primary'
                id='submit-button'
            >
            Speichern
            </button>
        </form>
        <h2>Panini-Bildli für die ganze Familie erstellen</h2>
        <p>Falls die anderen Familienmitglieder ein eigenes OLZ-Konto haben, können diese sich selber einloggen und ihr Panini-Bildli erstellen.</p>
        <p>Für Familienmitglieder, die noch <b>kein</b> OLZ-Konto haben und auch kein eigenes brauchen, kannst du ein Kind-Konto erstellen und dort das Panini-Bildli erstellen:</p>
        <ul className='bullet-list'>
            <li>Gehe auf dein Profil (OLZ-Konto-Menu rechts oben &gt; Profil)</li>
            <li>Wähle "Neues Familienmitglied erstellen"</li>
            <li>Formular ausfüllen und abschicken (Hinweis: Im Gegensatz zum Hauptkonto dürfen E-Mail und Passwort leer bleiben)</li>
            <li>Nun hast du im OLZ-Konto-Menu rechts oben die Möglichkeit, zwischen deinem Hauptkonto und dem Kind-Konto hin- und herzuwechseln</li>
        </ul>
    </>);
};

export function initOlzPanini(): boolean {
    initReact('panini-react-root', (
        <OlzPanini2024 />
    ));
    return false;
}
