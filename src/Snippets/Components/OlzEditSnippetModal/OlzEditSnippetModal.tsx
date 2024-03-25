import * as bootstrap from 'bootstrap';
import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzSnippetData} from '../../../Api/client/generated_olz_api_types';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzMultiFileField} from '../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {OlzMultiImageField} from '../../../Components/Upload/OlzMultiImageField/OlzMultiImageField';
import {getApiString, getFormString, getResolverResult} from '../../../Utils/formUtils';
import {initReact} from '../../../Utils/reactUtils';

import './OlzEditSnippetModal.scss';

interface OlzEditSnippetForm {
    text: string;
    imageIds: string[];
    fileIds: string[];
}

const resolver: Resolver<OlzEditSnippetForm> = async (values) => {
    const errors: FieldErrors<OlzEditSnippetForm> = {};
    return getResolverResult(errors, values);
};

function getFormFromApi(apiData?: OlzSnippetData): OlzEditSnippetForm {
    return {
        text: getFormString(apiData?.text),
        imageIds: apiData?.imageIds ?? [],
        fileIds: apiData?.fileIds ?? [],
    };
}

function getApiFromForm(formData: OlzEditSnippetForm): OlzSnippetData {
    return {
        text: getApiString(formData.text) ?? '',
        imageIds: formData.imageIds,
        fileIds: formData.fileIds,
    };
}

// ---

interface OlzEditSnippetModalProps {
    id: number;
    meta?: OlzMetaData;
    data?: OlzSnippetData;
}

export const OlzEditSnippetModal = (props: OlzEditSnippetModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, control} = useForm<OlzEditSnippetForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [isLoading, setIsLoading] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const onSubmit: SubmitHandler<OlzEditSnippetForm> = async (values) => {
        const meta: OlzMetaData = {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);

        const [err, response] = await olzApi.getResult('updateSnippet', {id: props.id, meta, data});
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
        ? 'Textausschnitt erstellen'
        : 'Textausschnitt bearbeiten'
    );

    return (
        <div className='modal fade' id='edit-snippet-modal' tabIndex={-1} aria-labelledby='edit-snippet-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={handleSubmit(onSubmit)}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-snippet-modal-label'>
                                {dialogTitle}
                            </h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
                            <div className='mb-3'>
                                <OlzTextField
                                    mode='textarea'
                                    title='Inhalt'
                                    name='text'
                                    errors={errors}
                                    register={register}
                                />
                            </div>
                            <div id='images-upload'>
                                <OlzMultiImageField
                                    title='Bilder'
                                    name='imageIds'
                                    errors={errors}
                                    control={control}
                                    setIsLoading={setIsLoading}
                                    isMarkdown
                                />
                            </div>
                            <div id='files-upload'>
                                <OlzMultiFileField
                                    title='Dateien'
                                    name='fileIds'
                                    errors={errors}
                                    control={control}
                                    setIsLoading={setIsLoading}
                                    isMarkdown
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

export function initOlzEditSnippetModal(
    id: number,
    meta?: OlzMetaData,
    data?: OlzSnippetData,
): boolean {
    initReact('edit-entity-react-root', (
        <OlzEditSnippetModal
            id={id}
            meta={meta}
            data={data}
        />
    ));
    window.setTimeout(() => {
        const modal = document.getElementById('edit-snippet-modal');
        if (modal) {
            new bootstrap.Modal(modal, {backdrop: 'static'}).show();
        }
    }, 1);
    return false;
}
