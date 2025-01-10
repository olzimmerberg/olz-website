import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzSnippetData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzMultiFileField} from '../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {OlzMultiImageField} from '../../../Components/Upload/OlzMultiImageField/OlzMultiImageField';
import {getApiString, getFormString, getResolverResult} from '../../../Utils/formUtils';

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

    const [isSubmitting, setIsSubmitting] = React.useState<boolean>(false);
    const [isImagesLoading, setIsImagesLoading] = React.useState<boolean>(false);
    const [isFilesLoading, setIsFilesLoading] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const onSubmit: SubmitHandler<OlzEditSnippetForm> = async (values) => {
        setIsSubmitting(true);
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);

        const [err, response] = await olzApi.getResult('updateSnippet', {id: props.id, meta, data});
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

    const dialogTitle = (props.id === undefined
        ? 'Textausschnitt erstellen'
        : 'Textausschnitt bearbeiten'
    );
    const isLoading = isImagesLoading || isFilesLoading;

    return (
        <OlzEditModal
            modalId='edit-snippet-modal'
            dialogTitle={dialogTitle}
            successMessage={successMessage}
            errorMessage={errorMessage}
            isLoading={isLoading}
            isSubmitting={isSubmitting}
            onSubmit={handleSubmit(onSubmit)}
        >
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
                    setIsLoading={setIsImagesLoading}
                />
            </div>
            <div id='files-upload'>
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

export function initOlzEditSnippetModal(
    id: number,
    meta?: OlzMetaData,
    data?: OlzSnippetData,
): boolean {
    return initOlzEditModal('edit-snippet-modal', () => (
        <OlzEditSnippetModal
            id={id}
            meta={meta}
            data={data}
        />
    ));
}
