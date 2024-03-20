import * as bootstrap from 'bootstrap';
import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzLinkData} from '../../../Api/client/generated_olz_api_types';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {getApiNumber, getApiString, getFormNumber, getFormString, getResolverResult, validateInteger, validateNotEmpty} from '../../../Utils/formUtils';
import {initReact} from '../../../Utils/reactUtils';

import './OlzEditLinkModal.scss';

interface OlzEditLinkForm {
    name: string;
    position: string;
    url: string;
}

const resolver: Resolver<OlzEditLinkForm> = async (values) => {
    const errors: FieldErrors<OlzEditLinkForm> = {};
    errors.name = validateNotEmpty(values.position);
    errors.position = validateInteger(values.position);
    errors.url = validateNotEmpty(values.url);
    return getResolverResult(errors, values);
};

function getFormFromApi(apiData?: OlzLinkData): OlzEditLinkForm {
    return {
        name: getFormString(apiData?.name),
        position: getFormNumber(apiData?.position),
        url: getFormString(apiData?.url),
    };
}

function getApiFromForm(formData: OlzEditLinkForm): OlzLinkData {
    return {
        name: getApiString(formData.name) ?? '',
        position: getApiNumber(formData.position),
        url: getApiString(formData.url) ?? '',
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
            setSuccessMessage('');
            setErrorMessage(`Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`);
            return;
        }

        setSuccessMessage('Änderung erfolgreich. Bitte warten...');
        setErrorMessage('');
        // TODO: This could probably be done more smoothly!
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
    initReact('edit-entity-react-root', (
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
