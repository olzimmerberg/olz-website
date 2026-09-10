import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi, OlzTerminNotificationData} from '../../../Api/client';
import {initOlzEditModal, MARKDOWN_NOTICE, OlzEditModal, OlzEditModalStatus, OPTIONAL_NOTICE} from '../../../Common/Components/OlzEditModal/OlzEditModal';
import {OlzEntityField} from '../../../Common/Components/OlzEntityField/OlzEntityField';
import {OlzTextField} from '../../../Common/Components/OlzTextField/OlzTextField';
import {OlzTimeIntervalField} from '../../../Common/Components/OlzTimeIntervalField/OlzTimeIntervalField';
import {getApiBoolean, getApiNumber, getApiString, getFormBoolean, getFormNumber, getFormString, getResolverResult, validateInteger, validateNotEmpty} from '../../../Utils/formUtils';
import {assert} from '../../../Utils/generalUtils';

import './OlzEditTerminNotificationModal.scss';

interface OlzEditTerminNotificationForm {
    terminId: number | null;
    firesEarlierSeconds: string;
    title: string;
    content: string;
    recipientUserId: number | null;
    recipientRoleId: number | null;
    recipientTerminOwnerUser: string | boolean;
    recipientTerminOwnerRole: string | boolean;
    recipientTerminOrganizer: string | boolean;
    recipientTerminVolunteers: string | boolean;
    recipientTerminParticipants: string | boolean;
}

const resolver: Resolver<OlzEditTerminNotificationForm> = async (values) => {
    const errors: FieldErrors<OlzEditTerminNotificationForm> = {};
    errors.firesEarlierSeconds = validateInteger(values.firesEarlierSeconds);
    errors.title = validateNotEmpty(values.title);
    const hasAnyRecipient = values.recipientUserId || values.recipientRoleId
        || values.recipientTerminOrganizer;
    errors.recipientTerminOrganizer = hasAnyRecipient ? undefined
        : {type: 'validate', message: 'Ein Empfänger muss ausgewählt sein.'};
    return getResolverResult(errors, values);
};

function getFormFromApi(
    apiData?: Partial<OlzTerminNotificationData>,
): OlzEditTerminNotificationForm {
    return {
        terminId: apiData?.terminId ?? null,
        firesEarlierSeconds: getFormNumber(apiData?.firesEarlierSeconds),
        title: getFormString(apiData?.title),
        content: getFormString(apiData?.content),
        recipientUserId: apiData?.recipientUserId ?? null,
        recipientRoleId: apiData?.recipientRoleId ?? null,
        recipientTerminOwnerUser: getFormBoolean(apiData?.recipientTerminOwnerUser),
        recipientTerminOwnerRole: getFormBoolean(apiData?.recipientTerminOwnerRole),
        recipientTerminOrganizer: getFormBoolean(apiData?.recipientTerminOrganizer),
        recipientTerminVolunteers: getFormBoolean(apiData?.recipientTerminVolunteers),
        recipientTerminParticipants: getFormBoolean(apiData?.recipientTerminParticipants),
    };
}

function getApiFromForm(formData: OlzEditTerminNotificationForm): OlzTerminNotificationData {
    return {
        terminId: formData.terminId ?? 0,
        firesEarlierSeconds: getApiNumber(formData.firesEarlierSeconds) ?? 0,
        title: getApiString(formData.title) ?? '',
        content: getApiString(formData.content) ?? '',
        recipientUserId: formData.recipientUserId,
        recipientRoleId: formData.recipientRoleId,
        recipientTerminOwnerUser: getApiBoolean(formData.recipientTerminOwnerUser),
        recipientTerminOwnerRole: getApiBoolean(formData.recipientTerminOwnerRole),
        recipientTerminOrganizer: getApiBoolean(formData.recipientTerminOrganizer),
        recipientTerminVolunteers: getApiBoolean(formData.recipientTerminVolunteers),
        recipientTerminParticipants: getApiBoolean(formData.recipientTerminParticipants),
    };
}

// ---

interface OlzEditTerminNotificationModalProps {
    id?: number;
    data?: Partial<OlzTerminNotificationData>;
}

export const OlzEditTerminNotificationModal = (props: OlzEditTerminNotificationModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}, control} = useForm<OlzEditTerminNotificationForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});
    const [isUserLoading, setIsUserLoading] = React.useState<boolean>(false);
    const [isRoleLoading, setIsRoleLoading] = React.useState<boolean>(false);

    const onSubmit: SubmitHandler<OlzEditTerminNotificationForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const data = getApiFromForm(values);

        const [err, response] = await (props.id
            ? olzApi.getResult('updateTerminNotification', {id: props.id, data})
            : olzApi.getResult('createTerminNotification', {data}));
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
        const [err, response] = await olzApi.getResult('deleteTerminNotification', {id: assert(props.id)});
        if (err) {
            setStatus({id: 'DELETE_FAILED', message: `Löschen fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'DELETED'});
        // This could probably be done more smoothly!
        window.location.reload();
    } : undefined;

    const dialogTitle = (props.id === undefined
        ? 'Termin-Benachrichtigung erstellen'
        : 'Termin-Benachrichtigung bearbeiten'
    );
    const isLoading = isUserLoading || isRoleLoading;
    const editModalStatus: OlzEditModalStatus = isLoading ? {id: 'LOADING'} : status;
    const recipientError = errors.recipientTerminOrganizer?.message;
    const recipientErrorComponent = recipientError && <p className='error'>{String(recipientError)}</p>;

    return (
        <OlzEditModal
            modalId='edit-termin-notification-modal'
            dialogTitle={dialogTitle}
            status={editModalStatus}
            onSubmit={handleSubmit(onSubmit)}
            onDelete={onDelete}
        >
            <div className='row'>
                <div className='col mb-3'>
                    <OlzTimeIntervalField
                        title='Benachrichtigung ... vor dem Termin'
                        name='firesEarlierSeconds'
                        errors={errors}
                        control={control}
                    />
                </div>
                <div className='col mb-3'>
                </div>
            </div>
            <div className='mb-3'>
                <OlzTextField
                    title='Titel'
                    name='title'
                    errors={errors}
                    register={register}
                />
            </div>
            <div className='mb-3'>
                <OlzTextField
                    mode='textarea'
                    title={<>Inhalt {MARKDOWN_NOTICE}</>}
                    name='content'
                    errors={errors}
                    register={register}
                />
            </div>
            <h5>Wähle einen oder mehrere Empfänger:</h5>
            {recipientErrorComponent}
            <div className='row'>
                <div className='col mb-3'>
                    <OlzEntityField
                        title={<>Für spezifischen Benutzer {OPTIONAL_NOTICE}</>}
                        entityType='User'
                        name='recipientUserId'
                        errors={errors}
                        control={control}
                        setIsLoading={setIsUserLoading}
                        nullLabel={'-'}
                    />
                </div>
                <div className='col mb-3'>
                    <OlzEntityField
                        title={<>Für spezifisches Ressort {OPTIONAL_NOTICE}</>}
                        entityType='Role'
                        name='recipientRoleId'
                        errors={errors}
                        control={control}
                        setIsLoading={setIsRoleLoading}
                        nullLabel={'-'}
                    />
                </div>
            </div>
            <div className='row'>
                <div className='col mb-3 recipientTerminOrganizer-container'>
                    <input
                        type='checkbox'
                        value='yes'
                        {...register('recipientTerminOrganizer')}
                        id='recipientTerminOrganizer-input'
                    />
                    <label htmlFor='recipientTerminOrganizer-input'>
                        Für den Termin-Organisator
                    </label>
                </div>
            </div>
        </OlzEditModal>
    );
};

export function initOlzEditTerminNotificationModal(
    id?: number,
    data?: Partial<OlzTerminNotificationData>,
): boolean {
    return initOlzEditModal('edit-termin-notification-modal', () => (
        <OlzEditTerminNotificationModal
            id={id}
            data={data}
        />
    ));
}
