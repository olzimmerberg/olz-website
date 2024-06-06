import * as bootstrap from 'bootstrap';
import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzWeeklyPictureData} from '../../../Api/client/generated_olz_api_types';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzImageField} from '../../../Components/Upload/OlzImageField/OlzImageField';
import {initReact} from '../../../Utils/reactUtils';

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
    data?: OlzWeeklyPictureData;
}

export const OlzEditWeeklyPictureModal = (props: OlzEditWeeklyPictureModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, control} = useForm<OlzEditWeeklyPictureForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [isImageLoading, setIsImageLoading] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const onSubmit: SubmitHandler<OlzEditWeeklyPictureForm> = async (values) => {
        const meta: OlzMetaData = {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);
        const [err, response] = await olzApi.getResult('createWeeklyPicture', {meta, data});
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

    const isLoading = isImageLoading;

    return (
        <div className='modal fade' id='edit-weekly-picture-modal' tabIndex={-1} aria-labelledby='edit-weekly-picture-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={handleSubmit(onSubmit)}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-weekly-picture-modal-label'>Bild der Woche bearbeiten</h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
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
                                id='cancel-button'
                            >
                                Abbrechen
                            </button>
                            <button
                                type='submit'
                                className='btn btn-primary'
                                id='submit-button'
                                disabled={isLoading}
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

export function initOlzEditWeeklyPictureModal(id?: number, data?: OlzWeeklyPictureData): boolean {
    initReact('edit-entity-react-root', (
        <OlzEditWeeklyPictureModal id={id} data={data} />
    ));
    window.setTimeout(() => {
        const modal = document.getElementById('edit-weekly-picture-modal');
        if (modal) {
            new bootstrap.Modal(modal, {backdrop: 'static'}).show();
        }
    }, 1);
    return false;
}
