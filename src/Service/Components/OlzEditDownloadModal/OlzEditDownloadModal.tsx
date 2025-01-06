import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzDownloadData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzMultiFileField} from '../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {getApiNumber, getApiString, getFormNumber, getFormString, getResolverResult, validateInteger, validateNotEmpty} from '../../../Utils/formUtils';

import './OlzEditDownloadModal.scss';

interface OlzEditDownloadForm {
    name: string;
    position: string;
    fileIds: string[];
}

const resolver: Resolver<OlzEditDownloadForm> = async (values) => {
    const errors: FieldErrors<OlzEditDownloadForm> = {};
    errors.name = validateNotEmpty(values.name);
    errors.position = validateInteger(values.position);
    const requiredNumFileIds = values.name === '---' ? 0 : 1;
    if (values.fileIds?.length !== requiredNumFileIds) {
        errors.fileIds = {type: 'validate', message: `Genau ${requiredNumFileIds} Datei(en) erforderlich.`};
    }
    return getResolverResult(errors, values);
};

function getFormFromApi(apiData?: OlzDownloadData): OlzEditDownloadForm {
    return {
        name: getFormString(apiData?.name),
        position: getFormNumber(apiData?.position),
        fileIds: apiData?.fileId ? [apiData.fileId] : [],
    };
}

function getApiFromForm(formData: OlzEditDownloadForm): OlzDownloadData {
    return {
        name: getApiString(formData.name) ?? '',
        position: getApiNumber(formData.position),
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

    const [isSubmitting, setIsSubmitting] = React.useState<boolean>(false);
    const [isFilesLoading, setIsFilesLoading] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const onSubmit: SubmitHandler<OlzEditDownloadForm> = async (values) => {
        setIsSubmitting(true);
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);
        const [err, response] = await (props.id
            ? olzApi.getResult('updateDownload', {id: props.id, meta, data})
            : olzApi.getResult('createDownload', {meta, data}));
        if (err) {
            setSuccessMessage('');
            setErrorMessage(`Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`);
            setIsSubmitting(false);
            return;
        }

        setSuccessMessage('Änderung erfolgreich. Bitte warten...');
        setErrorMessage('');
        // This could probably be done more smoothly!
        window.location.reload();
    };

    const dialogTitle = props.id === undefined ? 'Download erstellen' : 'Download bearbeiten';
    const isLoading = isFilesLoading;

    return (
        <OlzEditModal
            modalId='edit-download-modal'
            dialogTitle={dialogTitle}
            successMessage={successMessage}
            errorMessage={errorMessage}
            isLoading={isLoading}
            isSubmitting={isSubmitting}
            onSubmit={handleSubmit(onSubmit)}
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
            <div id='file-upload'>
                <OlzMultiFileField
                    title='Dateien'
                    name='fileIds'
                    errors={errors}
                    control={control}
                    setIsLoading={setIsFilesLoading}
                />
            </div>
        </OlzEditModal>
    );
};

export function initOlzEditDownloadModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzDownloadData,
): boolean {
    return initOlzEditModal('edit-download-modal', () => (
        <OlzEditDownloadModal
            id={id}
            meta={meta}
            data={data}
        />
    ));
}
