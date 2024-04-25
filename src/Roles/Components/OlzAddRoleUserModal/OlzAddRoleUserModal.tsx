import * as bootstrap from 'bootstrap';
import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzEntityField} from '../../../Components/Common/OlzEntityField/OlzEntityField';
import {getResolverResult} from '../../../Utils/formUtils';
import {assert} from '../../../Utils/generalUtils';
import {initReact} from '../../../Utils/reactUtils';

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

    const [isUsersLoading, setIsUsersLoading] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    const onSubmit: SubmitHandler<OlzAddRoleUserForm> = async (values) => {
        const data = getApiFromForm(values);

        const [err, response] = await olzApi.getResult('addUserRoleMembership', {ids: {
            roleId: props.roleId,
            userId: assert(data.newUser),
        }});
        if (err || response.status !== 'OK') {
            setSuccessMessage('');
            setErrorMessage(`Anfrage fehlgeschlagen: ${JSON.stringify(err || response)}`);
            return;
        }

        setSuccessMessage('Änderung erfolgreich. Bitte warten...');
        setErrorMessage('');
        // TODO: This could probably be done more smoothly!
        window.location.reload();
    };

    const dialogTitle = 'Verantwortliche hinzufügen';
    const isLoading = isUsersLoading;

    return (
        <div className='modal fade' id='add-role-user-modal' tabIndex={-1} aria-labelledby='add-role-user-modal-label' aria-hidden='true'>
            <div className='modal-dialog'>
                <div className='modal-content'>
                    <form className='default-form' onSubmit={handleSubmit(onSubmit)}>
                        <div className='modal-header'>
                            <h5 className='modal-title' id='add-role-user-modal-label'>
                                {dialogTitle}
                            </h5>
                            <button type='button' className='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div className='modal-body'>
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
                            <div className='success-message alert alert-success' role='alert'>
                                {successMessage}
                            </div>
                            <div className='error-message alert alert-danger' role='alert'>
                                {errorMessage}
                            </div>
                        </div>
                        <div className='modal-footer'>
                            <button
                                type='button'
                                className='btn btn-secondary'
                                data-bs-dismiss='modal'
                            >
                                Abbrechen
                            </button>
                            <button
                                type='submit'
                                disabled={isLoading}
                                className={'btn btn-primary'}
                                id='submit-button'
                            >
                                Speichern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
};

export function initOlzAddRoleUserModal(
    roleId: number,
): boolean {
    initReact('edit-entity-react-root', (
        <OlzAddRoleUserModal roleId={roleId} />
    ));
    window.setTimeout(() => {
        const modal = document.getElementById('add-role-user-modal');
        if (modal) {
            new bootstrap.Modal(modal, {backdrop: 'static'}).show();
        }
    }, 1);
    return false;
}
