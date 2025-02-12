import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzQuestionData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, MARKDOWN_NOTICE, OlzEditModal, OlzEditModalStatus} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzEntityField} from '../../../Components/Common/OlzEntityField/OlzEntityField';
import {OlzMultiFileField} from '../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {OlzMultiImageField} from '../../../Components/Upload/OlzMultiImageField/OlzMultiImageField';
import {getApiNumber, getApiString, getFormNumber, getFormString, getResolverResult, validateInteger, validateNotEmpty} from '../../../Utils/formUtils';
import {assert} from '../../../Utils/generalUtils';

import './OlzEditQuestionModal.scss';

interface OlzEditQuestionForm {
    ident: string,
    question: string,
    categoryId: number|null,
    positionWithinCategory: string,
    answer: string,
    imageIds: string[],
    fileIds: string[],
}

const resolver: Resolver<OlzEditQuestionForm> = async (values) => {
    const errors: FieldErrors<OlzEditQuestionForm> = {};
    errors.ident = validateNotEmpty(values.ident);
    errors.question = validateNotEmpty(values.question);
    if (values.categoryId === null) {
        errors.categoryId = {type: 'required', message: 'Darf nicht leer sein.'};
    }
    errors.positionWithinCategory = validateInteger(values.positionWithinCategory);
    errors.answer = validateNotEmpty(values.answer);
    return getResolverResult(errors, values);
};

function getFormFromApi(apiData?: Partial<OlzQuestionData>): OlzEditQuestionForm {
    return {
        ident: getFormString(apiData?.ident),
        question: getFormString(apiData?.question),
        categoryId: apiData?.categoryId ?? null,
        positionWithinCategory: getFormNumber(apiData?.positionWithinCategory),
        answer: getFormString(apiData?.answer),
        imageIds: apiData?.imageIds ?? [],
        fileIds: apiData?.fileIds ?? [],
    };
}

function getApiFromForm(formData: OlzEditQuestionForm): OlzQuestionData {
    return {
        ident: getApiString(formData.ident) ?? '-',
        question: getApiString(formData.question) ?? '-',
        categoryId: formData.categoryId,
        positionWithinCategory: getApiNumber(formData.positionWithinCategory),
        answer: getApiString(formData.answer) ?? '-',
        imageIds: formData.imageIds,
        fileIds: formData.fileIds,
    };
}

// ---

interface OlzEditQuestionModalProps {
    id?: number;
    meta?: OlzMetaData;
    data?: Partial<OlzQuestionData>;
}

export const OlzEditQuestionModal = (props: OlzEditQuestionModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, control} = useForm<OlzEditQuestionForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});
    const [isImagesLoading, setIsImagesLoading] = React.useState<boolean>(false);
    const [isFilesLoading, setIsFilesLoading] = React.useState<boolean>(false);
    const [isCategoriesLoading, setIsCategoriesLoading] = React.useState<boolean>(false);

    const onSubmit: SubmitHandler<OlzEditQuestionForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);

        const [err, response] = await (props.id
            ? olzApi.getResult('updateQuestion', {id: props.id, meta, data})
            : olzApi.getResult('createQuestion', {meta, data}));
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
        const [err, response] = await olzApi.getResult('deleteQuestion', {id: assert(props.id)});
        if (err) {
            setStatus({id: 'DELETE_FAILED', message: `Löschen fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'DELETED'});
        // This could probably be done more smoothly!
        window.location.reload();
    } : undefined;

    const dialogTitle = props.id === undefined
        ? 'Frage erstellen'
        : 'Frage bearbeiten';
    const isLoading = isImagesLoading || isFilesLoading || isCategoriesLoading;
    const editModalStatus: OlzEditModalStatus = isLoading ? {id: 'LOADING'} : status;

    return (
        <OlzEditModal
            modalId='edit-question-modal'
            dialogTitle={dialogTitle}
            status={editModalStatus}
            onSubmit={handleSubmit(onSubmit)}
            onDelete={onDelete}
        >
            <div className='row'>
                <div className='col mb-3'>
                    <OlzEntityField
                        title='Fragen-Kategorie'
                        entityType='QuestionCategory'
                        name='categoryId'
                        errors={errors}
                        control={control}
                        setIsLoading={setIsCategoriesLoading}
                    />
                </div>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Position in der Fragen-Kategorie'
                        name='positionWithinCategory'
                        errors={errors}
                        register={register}
                    />
                </div>
            </div>
            <div className='mb-3'>
                <OlzTextField
                    title='URL-Name'
                    name='ident'
                    errors={errors}
                    register={register}
                />
            </div>
            <div className='mb-3'>
                <OlzTextField
                    title='Frage'
                    name='question'
                    errors={errors}
                    register={register}
                />
            </div>
            <div className='mb-3 test-flaky'>
                <OlzTextField
                    mode='textarea'
                    title={<>Antwort {MARKDOWN_NOTICE}</>}
                    name='answer'
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

export function initOlzEditQuestionModal(
    id?: number,
    meta?: OlzMetaData,
    data?: Partial<OlzQuestionData>,
): boolean {
    return initOlzEditModal('edit-question-modal', () => (
        <OlzEditQuestionModal
            id={id}
            meta={meta}
            data={data}
        />
    ));
}
