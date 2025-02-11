import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzQuestionCategoryData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal, OlzEditModalStatus} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {getApiNumber, getApiString, getFormNumber, getFormString, getResolverResult, validateInteger, validateNotEmpty} from '../../../Utils/formUtils';
import {assert} from '../../../Utils/generalUtils';

interface OlzEditQuestionCategoryForm {
    position: string,
    name: string,
}

const resolver: Resolver<OlzEditQuestionCategoryForm> = async (values) => {
    const errors: FieldErrors<OlzEditQuestionCategoryForm> = {};
    errors.position = validateInteger(values.position);
    errors.name = validateNotEmpty(values.name);
    return getResolverResult(errors, values);
};

function getFormFromApi(apiData?: Partial<OlzQuestionCategoryData>): OlzEditQuestionCategoryForm {
    return {
        position: getFormNumber(apiData?.position),
        name: getFormString(apiData?.name),
    };
}

function getApiFromForm(formData: OlzEditQuestionCategoryForm): OlzQuestionCategoryData {
    return {
        position: getApiNumber(formData.position) ?? 0,
        name: getApiString(formData.name) ?? '-',
    };
}

// ---

interface OlzEditQuestionCategoryModalProps {
    id?: number;
    meta?: OlzMetaData;
    data?: Partial<OlzQuestionCategoryData>;
}

export const OlzEditQuestionCategoryModal = (props: OlzEditQuestionCategoryModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}} = useForm<OlzEditQuestionCategoryForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});

    const onSubmit: SubmitHandler<OlzEditQuestionCategoryForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);

        const [err, response] = await (props.id
            ? olzApi.getResult('updateQuestionCategory', {id: props.id, meta, data})
            : olzApi.getResult('createQuestionCategory', {meta, data}));
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
        const [err, response] = await olzApi.getResult('deleteQuestionCategory', {id: assert(props.id)});
        if (err) {
            setStatus({id: 'DELETE_FAILED', message: `Löschen fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'DELETED'});
        // This could probably be done more smoothly!
        window.location.reload();
    } : undefined;

    const dialogTitle = props.id === undefined
        ? 'Frage-Kategorie erstellen'
        : 'Frage-Kategorie bearbeiten';

    return (
        <OlzEditModal
            modalId='edit-question-category-modal'
            dialogTitle={dialogTitle}
            status={status}
            onSubmit={handleSubmit(onSubmit)}
            onDelete={onDelete}
        >
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
                    title='Name'
                    name='name'
                    errors={errors}
                    register={register}
                />
            </div>
        </OlzEditModal>
    );
};

export function initOlzEditQuestionCategoryModal(
    id?: number,
    meta?: OlzMetaData,
    data?: Partial<OlzQuestionCategoryData>,
): boolean {
    return initOlzEditModal('edit-question-category-modal', () => (
        <OlzEditQuestionCategoryModal
            id={id}
            meta={meta}
            data={data}
        />
    ));
}
