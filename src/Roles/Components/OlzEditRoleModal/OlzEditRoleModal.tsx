import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzRoleData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzEntityField} from '../../../Components/Common/OlzEntityField/OlzEntityField';
import {OlzMultiFileField} from '../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {OlzMultiImageField} from '../../../Components/Upload/OlzMultiImageField/OlzMultiImageField';
import {getApiBoolean, getApiNumber, getApiString, getFormBoolean, getFormNumber, getFormString, getResolverResult, validateIntegerOrNull, validateNotEmpty} from '../../../Utils/formUtils';

import './OlzEditRoleModal.scss';

interface OlzEditRoleForm {
    username: string,
    name: string,
    title: string,
    description: string,
    guide: string,
    imageIds: string[],
    fileIds: string[],
    parentRole: number|null,
    indexWithinParent: string,
    featuredIndex: string,
    canHaveChildRoles: string|boolean,
}

const resolver: Resolver<OlzEditRoleForm> = async (values) => {
    const errors: FieldErrors<OlzEditRoleForm> = {};
    errors.username = validateNotEmpty(values.username);
    errors.name = validateNotEmpty(values.name);
    errors.indexWithinParent = validateIntegerOrNull(values.indexWithinParent);
    errors.featuredIndex = validateIntegerOrNull(values.featuredIndex);
    return getResolverResult(errors, values);
};

function getFormFromApi(apiData?: Partial<OlzRoleData>): OlzEditRoleForm {
    return {
        username: getFormString(apiData?.username),
        name: getFormString(apiData?.name),
        title: getFormString(apiData?.title),
        description: getFormString(apiData?.description),
        guide: getFormString(apiData?.guide),
        imageIds: apiData?.imageIds ?? [],
        fileIds: apiData?.fileIds ?? [],
        parentRole: apiData?.parentRole ?? null,
        indexWithinParent: getFormNumber(apiData?.indexWithinParent),
        featuredIndex: getFormNumber(apiData?.featuredIndex),
        canHaveChildRoles: getFormBoolean(apiData?.canHaveChildRoles),
    };
}

function getApiFromForm(formData: OlzEditRoleForm): OlzRoleData {
    return {
        username: getApiString(formData.username) ?? '',
        name: getApiString(formData.name) ?? '',
        title: getApiString(formData.title),
        description: getApiString(formData.description) ?? '',
        guide: getApiString(formData.guide) ?? '',
        imageIds: formData.imageIds,
        fileIds: formData.fileIds,
        parentRole: formData.parentRole,
        indexWithinParent: getApiNumber(formData.indexWithinParent),
        featuredIndex: getApiNumber(formData.featuredIndex),
        canHaveChildRoles: getApiBoolean(formData.canHaveChildRoles ?? ''),
    };
}

// ---

interface OlzEditRoleModalProps {
    canParentRoleEdit: boolean;
    id?: number;
    meta?: OlzMetaData;
    data?: Partial<OlzRoleData>;
}

export const OlzEditRoleModal = (props: OlzEditRoleModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, control} = useForm<OlzEditRoleForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [isSubmitting, setIsSubmitting] = React.useState<boolean>(false);
    const [isImagesLoading, setIsImagesLoading] = React.useState<boolean>(false);
    const [isFilesLoading, setIsFilesLoading] = React.useState<boolean>(false);
    const [isParentRolesLoading, setIsParentRolesLoading] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const onSubmit: SubmitHandler<OlzEditRoleForm> = async (values) => {
        setIsSubmitting(true);
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);

        const [err, response] = await (props.id
            ? olzApi.getResult('updateRole', {id: props.id, meta, data})
            : olzApi.getResult('createRole', {meta, data}));
        if (err || response.status !== 'OK') {
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

    const dialogTitle = props.id === undefined
        ? 'Ressort erstellen'
        : 'Ressort bearbeiten';
    const isLoading = isImagesLoading || isFilesLoading || isParentRolesLoading;

    return (
        <OlzEditModal
            modalId='edit-role-modal'
            dialogTitle={dialogTitle}
            successMessage={successMessage}
            errorMessage={errorMessage}
            isLoading={isLoading}
            isSubmitting={isSubmitting}
            onSubmit={handleSubmit(onSubmit)}
        >
            <div className='mb-3'>
                <OlzTextField
                    title='Benutzername'
                    name='username'
                    errors={errors}
                    register={register}
                />
            </div>
            <div className='mb-3'>
                <OlzTextField
                    title='Name (kurz; fürs Organigramm)'
                    name='name'
                    errors={errors}
                    register={register}
                />
            </div>
            <div className='mb-3'>
                <OlzTextField
                    title='Titel (voller Name)'
                    name='title'
                    errors={errors}
                    register={register}
                />
            </div>
            <div className='mb-3'>
                <OlzTextField
                    mode='textarea'
                    title='Beschreibung'
                    name='description'
                    errors={errors}
                    register={register}
                />
            </div>
            <div className='mb-3'>
                <OlzTextField
                    mode='textarea'
                    title='Aufgaben (nur für OLZ-Mitglieder sichtbar)'
                    name='guide'
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
            {!props.canParentRoleEdit && (<div className='row'>
                <b>Die folgenden Felder können nur von Verantwortlichen für übergeordnete Rollen verändert werden:</b>
            </div>)}
            <div className='row'>
                <div className='col mb-3'>
                    <OlzEntityField
                        title='Eltern-Ressort'
                        entityType='Role'
                        name='parentRole'
                        errors={errors}
                        control={control}
                        disabled={!props.canParentRoleEdit}
                        setIsLoading={setIsParentRolesLoading}
                        nullLabel={'Kein Eltern-Ressort (d.h. Vorstandsamt)'}
                    />
                </div>
                <div className='col mb-3 checkbox-field'>
                    <input
                        type='checkbox'
                        value='yes'
                        {...register('canHaveChildRoles')}
                        disabled={!props.canParentRoleEdit}
                        id='canHaveChildRoles-input'
                    />
                    <label htmlFor='canHaveChildRoles-input'>
                                        Kinder-Rollen erlauben
                    </label>
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Position im Eltern-Ressort'
                        name='indexWithinParent'
                        errors={errors}
                        register={register}
                        disabled={!props.canParentRoleEdit}
                    />
                </div>
                <div className='col mb-3'>
                    <OlzTextField
                        title='Position in der "Häufig gesucht"-Liste'
                        name='featuredIndex'
                        errors={errors}
                        register={register}
                        disabled={!props.canParentRoleEdit}
                    />
                </div>
            </div>
        </OlzEditModal>
    );
};

export function initOlzEditRoleModal(
    canParentRoleEdit: boolean,
    id?: number,
    meta?: OlzMetaData,
    data?: Partial<OlzRoleData>,
): boolean {
    return initOlzEditModal('edit-role-modal', () => (
        <OlzEditRoleModal
            canParentRoleEdit={canParentRoleEdit}
            id={id}
            meta={meta}
            data={data}
        />
    ));
}
