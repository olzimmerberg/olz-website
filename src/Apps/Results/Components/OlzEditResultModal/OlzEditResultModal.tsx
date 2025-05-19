import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../../Api/client';
import {OlzApiRequests} from '../../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal, OlzEditModalStatus} from '../../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzMultiFileField} from '../../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {OlzTextField} from '../../../../Components/Common/OlzTextField/OlzTextField';
import {getApiString, getResolverResult, validateNotEmpty} from '../../../../Utils/formUtils';

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

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});
    const [isFilesLoading, setIsFilesLoading] = React.useState<boolean>(false);

    const onSubmit: SubmitHandler<OlzEditResultForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const data = getApiFromForm(values);
        const [err, response] = await olzApi.getResult('updateResults', data);
        if (err || response.status !== 'OK') {
            setStatus({id: 'SUBMIT_FAILED', message: `Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'SUBMITTED'});
        // This could probably be done more smoothly!
        window.location.reload();
    };

    const dialogTitle = props.data?.file === undefined ? 'Resultat erstellen' : 'Resultat bearbeiten';
    const editModalStatus: OlzEditModalStatus = isFilesLoading ? {id: 'LOADING'} : status;

    return (
        <OlzEditModal
            modalId='edit-result-modal'
            dialogTitle={dialogTitle}
            status={editModalStatus}
            onSubmit={handleSubmit(onSubmit)}
        >
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
        </OlzEditModal>
    );
};

export function initOlzEditResultModal(
    data?: OlzApiRequests['updateResults'],
): boolean {
    return initOlzEditModal('edit-result-modal', () => (
        <OlzEditResultModal
            data={data}
        />
    ));
}
