import * as bootstrap from 'bootstrap';
import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../../Api/client';
import {OlzApiRequests} from '../../../../Api/client/generated_olz_api_types';
import {OlzMultiFileField} from '../../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {OlzTextField} from '../../../../Components/Common/OlzTextField/OlzTextField';
import {getApiString, getResolverResult, validateNotEmpty} from '../../../../Utils/formUtils';
import {initReact} from '../../../../Utils/reactUtils';

import './OlzEditResultModal.scss';

interface OlzEditResultForm {
    name: string;
    iofXmlFileIds: string[];
}

const resolver: Resolver<OlzEditResultForm> = async (values) => {
    const errors: FieldErrors<OlzEditResultForm> = {};
    errors.name = validateNotEmpty(values.name);
    return getResolverResult(errors, values);
};

function getApiFromForm(formData: OlzEditResultForm): OlzApiRequests['updateResults'] {
    return {
        file: getApiString(formData.name) ?? '',
        content: null,
        iofXmlFileId: formData.iofXmlFileIds?.[0] ?? null,
    };
}

// ---

interface OlzEditResultModalProps {
    id?: number;
    data?: OlzApiRequests['updateResults'];
}

export const OlzEditResultModal = (props: OlzEditResultModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, control} = useForm<OlzEditResultForm>({
        resolver,
        defaultValues: {
            name: props?.data?.file ?? '',
            iofXmlFileIds: [],
        },
    });

    const [isFilesLoading, setIsFilesLoading] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const onSubmit: SubmitHandler<OlzEditResultForm> = async (values) => {
        const data = getApiFromForm(values);
        const [err, response] = await olzApi.getResult('updateResults', data)
           ;
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

    const dialogTitle = props.id === undefined ? 'Resultat erstellen' : 'Resultat bearbeiten';
    const isLoading = isFilesLoading;

    return (
        <div className='modal fade' id='edit-result-modal' tabIndex={-1} aria-labelledby='edit-result-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={handleSubmit(onSubmit)}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-result-modal-label'>
                                {dialogTitle}
                            </h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
                            <div className='mb-3'>
                                <OlzTextField
                                    title='Dateiname (muss auf .xml enden)'
                                    name='name'
                                    options={{required: 'Name darf nicht leer sein!'}}
                                    errors={errors}
                                    register={register}
                                />
                            </div>
                            <div id='file-upload'>
                                <OlzMultiFileField
                                    title='IOF-XML Resultate-Datei'
                                    name='iofXmlFileIds'
                                    errors={errors}
                                    control={control}
                                    setIsLoading={setIsFilesLoading}
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

export function initOlzEditResultModal(
    id?: number,
    data?: OlzApiRequests['updateResults'],
): boolean {
    initReact('edit-entity-react-root', (
        <OlzEditResultModal
            id={id}
            data={data}
        />
    ));
    window.setTimeout(() => {
        const modal = document.getElementById('edit-result-modal');
        if (modal) {
            new bootstrap.Modal(modal, {backdrop: 'static'}).show();
        }
    }, 1);
    return false;
}
