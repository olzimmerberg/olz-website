import {olzApi} from '../../../Api/client';
import {initOlzEditRoleModal} from '../OlzEditRoleModal/OlzEditRoleModal';
import {olzConfirm} from '../../../Components/Common/OlzConfirmationDialog/OlzConfirmationDialog';

import './OlzRolePage.scss';

export function editRole(
    newsId: number,
): boolean {
    olzApi.call('editRole', {id: newsId})
        .then((response) => {
            initOlzEditRoleModal(response.id, response.meta, response.data);
        });
    return false;
}

export function deleteRole(newsId: number): boolean {
    olzConfirm('Wirklich löschen?').then(() => {
        olzApi.call('deleteRole', {id: newsId}).then(() => {
            window.setTimeout(() => {
                // TODO: This could probably be done more smoothly!
                window.location.reload();
            }, 1000);
        });
    });
    return false;
}
