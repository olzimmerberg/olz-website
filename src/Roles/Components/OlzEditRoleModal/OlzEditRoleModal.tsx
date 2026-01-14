import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzRoleData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, MARKDOWN_NOTICE, OlzEditModal, OlzEditModalStatus} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzEntityField} from '../../../Components/Common/OlzEntityField/OlzEntityField';
import {OlzPositionField} from '../../../Components/Common/OlzPositionField/OlzPositionField';
import {OlzMultiFileField} from '../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {OlzMultiImageField} from '../../../Components/Upload/OlzMultiImageField/OlzMultiImageField';
import {getApiBoolean, getApiNumber, getApiString, getFormBoolean, getFormNumber, getFormString, getResolverResult, validateNumberOrNull, validateNotEmpty, validateStringRegex} from '../../../Utils/formUtils';
import {assert} from '../../../Utils/generalUtils';

import './OlzEditRoleModal.scss';

interface OlzEditRoleForm {
    username: string,
    name: string,
    description: string,
    guide: string,
    imageIds: string[],
    fileIds: string[],
    parentRole: number | null,
    positionWithinParent: string,
    featuredPosition: string,
    canHaveChildRoles: string | boolean,
}

const resolver: Resolver<OlzEditRoleForm> = async (values) => {
    const errors: FieldErrors<OlzEditRoleForm> = {};
    errors.username = validateStringRegex(values.username, /^[a-z0-9\-.]+$/, 'Benutzername darf nur Kleinbuchstaben, Zahlen, "-" und "." enthalten.');
    errors.name = validateNotEmpty(values.name);
    errors.positionWithinParent = validateNumberOrNull(values.positionWithinParent);
    errors.featuredPosition = validateNumberOrNull(values.featuredPosition);
    return getResolverResult(errors, values);
};

function getFormFromApi(apiData?: Partial<OlzRoleData>): OlzEditRoleForm {
    return {
        username: getFormString(apiData?.username),
        name: getFormString(apiData?.name),
        description: getFormString(apiData?.description),
        guide: getFormString(apiData?.guide),
        imageIds: apiData?.imageIds ?? [],
        fileIds: apiData?.fileIds ?? [],
        parentRole: apiData?.parentRole ?? null,
        positionWithinParent: getFormNumber(apiData?.positionWithinParent),
        featuredPosition: getFormNumber(apiData?.featuredPosition),
        canHaveChildRoles: getFormBoolean(apiData?.canHaveChildRoles),
    };
}

function getApiFromForm(formData: OlzEditRoleForm): OlzRoleData {
    return {
        username: getApiString(formData.username) ?? '',
        name: getApiString(formData.name) ?? '',
        description: getApiString(formData.description) ?? '',
        guide: getApiString(formData.guide) ?? '',
        imageIds: formData.imageIds,
        fileIds: formData.fileIds,
        parentRole: formData.parentRole,
        positionWithinParent: getApiNumber(formData.positionWithinParent),
        featuredPosition: getApiNumber(formData.featuredPosition),
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
    const {register, handleSubmit, formState: {errors}, control, watch} = useForm<OlzEditRoleForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});
    const [isPositionWithinParentLoading, setIsPositionWithinParentLoading] = React.useState<boolean>(false);
    const [isFeaturedPositionLoading, setIsFeaturedPositionLoading] = React.useState<boolean>(false);
    const [isImagesLoading, setIsImagesLoading] = React.useState<boolean>(false);
    const [isFilesLoading, setIsFilesLoading] = React.useState<boolean>(false);
    const [isParentRolesLoading, setIsParentRolesLoading] = React.useState<boolean>(false);

    const onSubmit: SubmitHandler<OlzEditRoleForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);

        const [err, response] = await (props.id
            ? olzApi.getResult('updateRole', {id: props.id, meta, data})
            : olzApi.getResult('createRole', {meta, data}));
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
        const [err, response] = await olzApi.getResult('deleteRole', {id: assert(props.id)});
        if (err) {
            setStatus({id: 'DELETE_FAILED', message: `Löschen fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'DELETED'});
        // This could probably be done more smoothly!
        window.location.reload();
    } : undefined;

    const dialogTitle = props.id === undefined
        ? 'Ressort erstellen'
        : 'Ressort bearbeiten';
    const parentRole = watch('parentRole');
    const isLoading = isImagesLoading || isFilesLoading || isParentRolesLoading || isPositionWithinParentLoading || isFeaturedPositionLoading;
    const editModalStatus: OlzEditModalStatus = isLoading ? {id: 'LOADING'} : status;

    return (
        <OlzEditModal
            modalId='edit-role-modal'
            dialogTitle={dialogTitle}
            status={editModalStatus}
            onSubmit={handleSubmit(onSubmit)}
            onDelete={props.canParentRoleEdit ? onDelete : undefined}
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
            <div className='mb-3 test-flaky'>
                <OlzTextField
                    mode='textarea'
                    title={<>Beschreibung {MARKDOWN_NOTICE}</>}
                    name='description'
                    errors={errors}
                    register={register}
                />
            </div>
            <div className='mb-3 test-flaky'>
                <OlzTextField
                    mode='textarea'
                    title={<>Aufgaben (nur für OLZ-Mitglieder sichtbar) {MARKDOWN_NOTICE}</>}
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
                    <OlzPositionField
                        title='Position im Eltern-Ressort'
                        entityType='Role'
                        name='positionWithinParent'
                        filter={{parentRoleId: `${parentRole}`}}
                        errors={errors}
                        control={control}
                        setIsLoading={setIsPositionWithinParentLoading}
                        disabled={!props.canParentRoleEdit}
                    />
                </div>
                <div className='col mb-3'>
                    <OlzPositionField
                        title='Position in der "Häufig gesucht"-Liste'
                        entityType='Role'
                        filter={{featuredPositionNotNull: 'true'}}
                        name='featuredPosition'
                        errors={errors}
                        control={control}
                        setIsLoading={setIsFeaturedPositionLoading}
                        disabled={!props.canParentRoleEdit}
                        nullLabel='(wird nicht angezeigt)'
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
