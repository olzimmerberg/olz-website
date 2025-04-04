import {olzApi} from '../../../Api/client';
import {initOlzEditRoleModal} from '../OlzEditRoleModal/OlzEditRoleModal';
import {initOlzAddRoleUserModal} from '../OlzAddRoleUserModal/OlzAddRoleUserModal';
import {olzConfirm} from '../../../Components/Common/OlzConfirmationDialog/OlzConfirmationDialog';

import './OlzRolePage.scss';

export function editRole(
    roleId: number,
    canParentRoleEdit: boolean,
): boolean {
    olzApi.call('editRole', {id: roleId})
        .then((response) => {
            initOlzEditRoleModal(canParentRoleEdit, response.id, response.meta, response.data);
        });
    return false;
}

export function addRoleUser(
    roleId: number,
): boolean {
    initOlzAddRoleUserModal(roleId);
    return false;
}

export function deleteRoleUser(roleId: number, userId: number): boolean {
    olzConfirm('Wirklich entfernen?').then(() => {
        olzApi.call('removeUserRoleMembership', {ids: {roleId, userId}}).then(() => {
            window.setTimeout(() => {
                // This could probably be done more smoothly!
                window.location.reload();
            }, 1000);
        });
    });
    return false;
}

export function addChildRole(roleId: number): boolean {
    initOlzEditRoleModal(true, undefined, undefined, {parentRole: roleId});
    return false;
}
