import {olzApi} from '../../../Api/client';
import {initOlzEditRoleModal} from '../OlzEditRoleModal/OlzEditRoleModal';
import {initOlzAddRoleUserModal} from '../OlzAddRoleUserModal/OlzAddRoleUserModal';
import {olzConfirm} from '../../../Components/Common/OlzConfirmationDialog/OlzConfirmationDialog';

import './OlzRolePage.scss';

export function editRole(
    roleId: number,
): boolean {
    olzApi.call('editRole', {id: roleId})
        .then((response) => {
            initOlzEditRoleModal(response.id, response.meta, response.data);
        });
    return false;
}

export function deleteRole(roleId: number): boolean {
    olzConfirm('Wirklich löschen?').then(() => {
        olzApi.call('deleteRole', {id: roleId}).then(() => {
            window.setTimeout(() => {
                // TODO: This could probably be done more smoothly!
                window.location.reload();
            }, 1000);
        });
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
                // TODO: This could probably be done more smoothly!
                window.location.reload();
            }, 1000);
        });
    });
    return false;
}
