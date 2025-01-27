import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {initOlzEditModal, OlzEditModal, OlzEditModalStatus} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzEntityField} from '../../../Components/Common/OlzEntityField/OlzEntityField';
import {getResolverResult} from '../../../Utils/formUtils';
import {assert} from '../../../Utils/generalUtils';

import './OlzAddRoleUserModal.scss';

interface OlzAddRoleUserForm {
    newUser: number|null,
}

const resolver: Resolver<OlzAddRoleUserForm> = async (values) => {
    const errors: FieldErrors<OlzAddRoleUserForm> = {};
    return getResolverResult(errors, values);
};

function getApiFromForm(formData: OlzAddRoleUserForm) {
    return {
        newUser: formData.newUser,
    };
}

// ---

interface OlzAddRoleUserModalProps {
    roleId: number;
}

export const OlzAddRoleUserModal = (props: OlzAddRoleUserModalProps): React.ReactElement => {
    const {handleSubmit, formState: {errors}, control} = useForm<OlzAddRoleUserForm>({
        resolver,
    });

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});
    const [isUsersLoading, setIsUsersLoading] = React.useState<boolean>(false);

    const onSubmit: SubmitHandler<OlzAddRoleUserForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const data = getApiFromForm(values);

        const [err, response] = await olzApi.getResult('addUserRoleMembership', {ids: {
            roleId: props.roleId,
            userId: assert(data.newUser),
        }});
        if (err) {
            setStatus({id: 'SUBMIT_FAILED', message: `Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`});
            return;
        }
        setStatus({id: 'SUBMITTED'});
        // This could probably be done more smoothly!
        window.location.reload();
    };

    const dialogTitle = 'Verantwortliche hinzufügen';
    const editModalStatus: OlzEditModalStatus = isUsersLoading ? {id: 'LOADING'} : status;

    return (
        <OlzEditModal
            modalId='add-role-user-modal'
            dialogTitle={dialogTitle}
            status={editModalStatus}
            onSubmit={handleSubmit(onSubmit)}
        >
            <div className='row'>
                <div className='col mb-3'>
                    <OlzEntityField
                        title='Neuer Verantwortlicher'
                        entityType='User'
                        name='newUser'
                        errors={errors}
                        control={control}
                        setIsLoading={setIsUsersLoading}
                        nullLabel={'Bitte wählen...'}
                    />
                </div>
            </div>
        </OlzEditModal>
    );
};

export function initOlzAddRoleUserModal(
    roleId: number,
): boolean {
    return initOlzEditModal('add-role-user-modal', () => (
        <OlzAddRoleUserModal roleId={roleId} />
    ));
}
