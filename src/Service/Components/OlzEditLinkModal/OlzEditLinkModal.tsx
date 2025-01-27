import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzLinkData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal, OlzEditModalStatus} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {getApiNumber, getApiString, getFormNumber, getFormString, getResolverResult, validateInteger, validateNotEmpty} from '../../../Utils/formUtils';
import {assert} from '../../../Utils/generalUtils';

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

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});

    const onSubmit: SubmitHandler<OlzEditLinkForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);
        const [err, response] = await (props.id
            ? olzApi.getResult('updateLink', {id: props.id, meta, data})
            : olzApi.getResult('createLink', {meta, data}));
        if (err) {
            setStatus({id: 'SUBMIT_FAILED', message: `Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'SUBMITTED'});
        // This could probably be done more smoothly!
        window.location.reload();
    };

    const onDelete = props.id ? async () => {
        setStatus({id: 'DELETING'});
        const [err, response] = await olzApi.getResult('deleteLink', {id: assert(props.id)});
        if (err) {
            setStatus({id: 'DELETE_FAILED', message: `Löschen fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'DELETED'});
        // This could probably be done more smoothly!
        window.location.reload();
    } : undefined;

    const dialogTitle = props.id === undefined ? 'Link erstellen' : 'Link bearbeiten';

    return (
        <OlzEditModal
            modalId='edit-link-modal'
            dialogTitle={dialogTitle}
            status={status}
            onSubmit={handleSubmit(onSubmit)}
            onDelete={onDelete}
        >
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
        </OlzEditModal>
    );
};

export function initOlzEditLinkModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzLinkData,
): boolean {
    return initOlzEditModal('edit-link-modal', () => (
        <OlzEditLinkModal
            id={id}
            meta={meta}
            data={data}
        />
    ));
}
