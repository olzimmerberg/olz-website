import * as bootstrap from 'bootstrap';
import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {createRoot} from 'react-dom/client';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzDownloadData} from '../../../Api/client/generated_olz_api_types';
import {OlzMultiFileField} from '../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {timeout} from '../../../Utils/generalUtils';

import './OlzEditDownloadModal.scss';

interface OlzEditDownloadForm {
    name: string;
    position: string;
    fileIds: string[];
}

const resolver: Resolver<OlzEditDownloadForm> = async (values) => {
    const errors: FieldErrors<OlzEditDownloadForm> = {};
    if (!values.name) {
        errors.name = {type: 'required', message: 'Darf nicht leer sein.'};
    }
    if (isNaN(Number(values.position))) {
        errors.position = {type: 'validate', message: 'Muss eine Ganzzahl sein.'};
    }
    const requiredNumFileIds = values.name === '---' ? 0 : 1;
    if (values.fileIds?.length !== requiredNumFileIds) {
        errors.fileIds = {type: 'validate', message: `Genau ${requiredNumFileIds} Datei(en) erforderlich.`};
    }
    return {
        values: Object.keys(errors).length > 0 ? {} : values,
        errors,
    };
};

function getFormFromApi(apiData?: OlzDownloadData): OlzEditDownloadForm {
    return {
        name: apiData?.name ?? '',
        position: apiData?.position !== undefined ? String(apiData.position) : '',
        fileIds: apiData?.fileId ? [apiData.fileId] : [],
    };
}

function getApiFromForm(formData: OlzEditDownloadForm): OlzDownloadData {
    return {
        name: formData.name,
        position: Number(formData.position),
        fileId: formData.fileIds?.[0] ?? null,
    };
}

// ---

interface OlzEditDownloadModalProps {
    id?: number;
    meta?: OlzMetaData;
    data?: OlzDownloadData;
}

export const OlzEditDownloadModal = (props: OlzEditDownloadModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, control} = useForm<OlzEditDownloadForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [isLoading, setIsLoading] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const onSubmit: SubmitHandler<OlzEditDownloadForm> = async (values) => {
        const meta: OlzMetaData = {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);
        const [err, response] = await (props.id
            ? olzApi.getResult('updateDownload', {id: props.id, meta, data})
            : olzApi.getResult('createDownload', {meta, data}));
        if (err || response.status !== 'OK') {
            setErrorMessage(`Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`);
            return;
        }

        // TODO: This could probably be done more smoothly!
        setSuccessMessage('Änderung erfolgreich. Bitte warten...');
        await timeout(1000);
        window.location.reload();
    };

    const dialogTitle = props.id === undefined ? 'Download erstellen' : 'Download bearbeiten';

    return (
        <div className='modal fade' id='edit-download-modal' tabIndex={-1} aria-labelledby='edit-download-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={handleSubmit(onSubmit)}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-download-modal-label'>
                                {dialogTitle}
                            </h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
                            <div className='mb-3'>
                                <OlzTextField
                                    title='Name (--- für Trennlinie)'
                                    name='name'
                                    options={{required: 'Name darf nicht leer sein!'}}
                                    errors={errors}
                                    register={register}
                                />
                            </div>
                            <div className='mb-3'>
                                <OlzTextField
                                    title='Position'
                                    name='position'
                                    errors={errors}
                                    register={register}
                                />
                            </div>
                            <div id='file-upload'>
                                <OlzMultiFileField
                                    title='Dateien'
                                    name='fileIds'
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

let editDownloadModalRoot: ReturnType<typeof createRoot>|null = null;

export function initOlzEditDownloadModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzDownloadData,
): boolean {
    const rootElem = document.getElementById('edit-download-react-root');
    if (!rootElem) {
        return false;
    }
    if (editDownloadModalRoot) {
        editDownloadModalRoot.unmount();
    }
    editDownloadModalRoot = createRoot(rootElem);
    editDownloadModalRoot.render(
        <OlzEditDownloadModal
            id={id}
            meta={meta}
            data={data}
        />,
    );
    window.setTimeout(() => {
        const modal = document.getElementById('edit-download-modal');
        if (modal) {
            new bootstrap.Modal(modal, {backdrop: 'static'}).show();
        }
    }, 1);
    return false;
}
