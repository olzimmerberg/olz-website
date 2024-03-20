import {olzApi} from '../../../Api/client';
import {initOlzEditTerminLocationModal} from '../OlzEditTerminLocationModal/OlzEditTerminLocationModal';
import {olzConfirm} from '../../../Components/Common/OlzConfirmationDialog/OlzConfirmationDialog';

import './OlzTerminLocationDetail.scss';

export function editTerminLocation(
    terminLocationId: number,
): boolean {
    olzApi.call('editTerminLocation', {id: terminLocationId})
        .then((response) => {
            initOlzEditTerminLocationModal(response.id, response.meta, response.data);
        });
    return false;
}

export function deleteTerminLocation(terminLocationId: number): boolean {
    olzConfirm('Wirklich löschen?').then(() => {
        olzApi.call('deleteTerminLocation', {id: terminLocationId}).then(() => {
            window.setTimeout(() => {
                // TODO: This could probably be done more smoothly!
                window.location.reload();
            }, 1000);
        });
    });
    return false;
}
