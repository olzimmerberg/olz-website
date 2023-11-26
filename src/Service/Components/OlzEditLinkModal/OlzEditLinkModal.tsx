import * as bootstrap from 'bootstrap';
import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzLinkData} from '../../../../src/Api/client/generated_olz_api_types';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {timeout} from '../../../Utils/generalUtils';
import {initReact} from '../../../Utils/reactUtils';

import './OlzEditLinkModal.scss';

interface OlzEditLinkForm {
    name: string;
    position: string;
    url: string;
}

const resolver: Resolver<OlzEditLinkForm> = async (values) => {
    const errors: FieldErrors<OlzEditLinkForm> = {};
    if (!values.name) {
        errors.name = {type: 'required', message: 'Darf nicht leer sein.'};
    }
    if (isNaN(Number(values.position))) {
        errors.position = {type: 'validate', message: 'Muss eine Ganzzahl sein.'};
    }
    if (!values.url) {
        errors.url = {type: 'required', message: 'Darf nicht leer sein.'};
    }
    return {
        values: Object.keys(errors).length > 0 ? {} : values,
        errors,
    };
};

function getFormFromApi(apiData?: OlzLinkData): OlzEditLinkForm {
    return {
        name: apiData?.name ?? '',
        position: apiData?.position !== undefined ? String(apiData.position) : '',
        url: apiData?.url ?? '',
    };
}

function getApiFromForm(formData: OlzEditLinkForm): OlzLinkData {
    return {
        name: formData.name,
        position: Number(formData.position),
        url: formData.url,
    };
}

// ---

interface OlzEditLinkModalProps {
    id?: number;
    meta?: OlzMetaData;
    data?: OlzLinkData;
}

export const OlzEditLinkModal = (props: OlzEditLinkModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}} = useForm<OlzEditLinkForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const onSubmit: SubmitHandler<OlzEditLinkForm> = async (values) => {
        const meta: OlzMetaData = {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);
        const [err, response] = await (props.id
            ? olzApi.getResult('updateLink', {id: props.id, meta, data})
            : olzApi.getResult('createLink', {meta, data}));
        if (err || response.status !== 'OK') {
            setErrorMessage(`Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`);
            return;
        }

        // TODO: This could probably be done more smoothly!
        setSuccessMessage('Änderung erfolgreich. Bitte warten...');
        await timeout(1000);
        window.location.reload();
    };

    const dialogTitle = props.id === undefined ? 'Link erstellen' : 'Link bearbeiten';

    return (
        <div className='modal fade' id='edit-link-modal' tabIndex={-1} aria-labelledby='edit-link-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={handleSubmit(onSubmit)}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='edit-link-modal-label'>
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
                            <div className='mb-3'>
                                <OlzTextField
                                    title='URL (--- für Trennlinie)'
                                    name='url'
                                    errors={errors}
                                    register={register}
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

export function initOlzEditLinkModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzLinkData,
): boolean {
    initReact('edit-link-react-root', (
        <OlzEditLinkModal
            id={id}
            meta={meta}
            data={data}
        />
    ));
    window.setTimeout(() => {
        const modal = document.getElementById('edit-link-modal');
        if (modal) {
            new bootstrap.Modal(modal, {backdrop: 'static'}).show();
        }
    }, 1);
    return false;
}
