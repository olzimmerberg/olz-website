import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzMetaData, OlzRunData} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal, OlzEditModalStatus} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {getApiNumber, getApiString, getFormNumber, getFormString, getResolverResult, validateDateTimeOrNull, validateInteger, validateNumber} from '../../../Utils/formUtils';
import {assert} from '../../../Utils/generalUtils';

import './OlzEditRunModal.scss';

interface OlzEditRunForm {
    runAt: string;
    distanceKm: string;
    elevationMeters: string;
}

const resolver: Resolver<OlzEditRunForm> = async (values) => {
    const errors: FieldErrors<OlzEditRunForm> = {};
    [errors.runAt, values.runAt] = validateDateTimeOrNull(values.runAt);
    errors.distanceKm = validateNumber(values.distanceKm);
    errors.elevationMeters = validateInteger(values.elevationMeters);
    return getResolverResult(errors, values);
};

function getFormFromApi(apiData?: OlzRunData): OlzEditRunForm {
    return {
        runAt: getFormString(apiData?.runAt),
        distanceKm: getFormNumber(apiData?.distanceMeters && apiData.distanceMeters / 1000),
        elevationMeters: getFormNumber(apiData?.elevationMeters),
    };
}

function getApiFromForm(formData: OlzEditRunForm): OlzRunData {
    return {
        runAt: getApiString(formData.runAt) ?? null,
        distanceMeters: (getApiNumber(formData.distanceKm) ?? 0) * 1000,
        elevationMeters: getApiNumber(formData.elevationMeters) ?? 0,
        source: 'manuell',
    };
}

// ---

interface OlzEditRunModalProps {
    id?: number;
    meta?: OlzMetaData;
    data?: OlzRunData;
}

export const OlzEditRunModal = (props: OlzEditRunModalProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}} = useForm<OlzEditRunForm>({
        resolver,
        defaultValues: getFormFromApi(props.data),
    });

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});

    const onSubmit: SubmitHandler<OlzEditRunForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const meta: OlzMetaData = props?.meta ?? {
            ownerUserId: null,
            ownerRoleId: null,
            onOff: true,
        };
        const data = getApiFromForm(values);
        const [err, response] = await (props.id
            ? olzApi.getResult('updateRun', {id: props.id, meta, data})
            : olzApi.getResult('createRun', {meta, data}));
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
        const [err, response] = await olzApi.getResult('deleteRun', {id: assert(props.id)});
        if (err) {
            setStatus({id: 'DELETE_FAILED', message: `Löschen fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'DELETED'});
        // This could probably be done more smoothly!
        window.location.reload();
    } : undefined;

    const dialogTitle = props.id === undefined ? 'Aktivität erstellen' : 'Aktivität bearbeiten';
    const editModalStatus: OlzEditModalStatus = status;

    return (
        <OlzEditModal
            modalId='edit-run-modal'
            dialogTitle={dialogTitle}
            status={editModalStatus}
            onSubmit={handleSubmit(onSubmit)}
            onDelete={onDelete}
        >
            <div className='mb-3'>
                <OlzTextField
                    title='Datum & Zeit (leer lassen für "jetzt")'
                    name='runAt'
                    errors={errors}
                    register={register}
                />
            </div>
            <div className='mb-3'>
                <OlzTextField
                    title='Distanz (in Kilometern)'
                    name='distanceKm'
                    options={{required: 'Distanz darf nicht leer sein!'}}
                    errors={errors}
                    register={register}
                />
            </div>
            <div className='mb-3'>
                <OlzTextField
                    title='Höhenmeter (in Metern)'
                    name='elevationMeters'
                    options={{required: 'Höhenmeter dürfen nicht leer sein!'}}
                    errors={errors}
                    register={register}
                />
            </div>
        </OlzEditModal>
    );
};

export function initOlzEditRunModal(
    id?: number,
    meta?: OlzMetaData,
    data?: OlzRunData,
): boolean {
    return initOlzEditModal('edit-run-modal', () => (
        <OlzEditRunModal
            id={id}
            meta={meta}
            data={data}
        />
    ));
}
