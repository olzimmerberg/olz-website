import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {initOlzEditModal, OlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
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

    const [isSubmitting, setIsSubmitting] = React.useState<boolean>(false);
    const [isUsersLoading, setIsUsersLoading] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const onSubmit: SubmitHandler<OlzAddRoleUserForm> = async (values) => {
        setIsSubmitting(true);
        const data = getApiFromForm(values);

        const [err, response] = await olzApi.getResult('addUserRoleMembership', {ids: {
            roleId: props.roleId,
            userId: assert(data.newUser),
        }});
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

    const dialogTitle = 'Verantwortliche hinzufügen';
    const isLoading = isUsersLoading;

    return (
        <OlzEditModal
            modalId='add-role-user-modal'
            dialogTitle={dialogTitle}
            successMessage={successMessage}
            errorMessage={errorMessage}
            isLoading={isLoading}
            isSubmitting={isSubmitting}
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
