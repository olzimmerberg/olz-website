import {olzApi} from '../../../Api/client';
import {initOlzEditTerminModal} from '../OlzEditTerminModal/OlzEditTerminModal';
import {olzConfirm} from '../../../Components/Common/OlzConfirmationDialog/OlzConfirmationDialog';

import './OlzTerminDetail.scss';

export function editTermin(
    terminId: number,
): boolean {
    olzApi.call('editTermin', {id: terminId})
        .then((response) => {
            initOlzEditTerminModal(response.id, undefined, response.meta, response.data);
        });
    return false;
}

export function deleteTermin(terminId: number): boolean {
    olzConfirm('Wirklich löschen?').then(() => {
        olzApi.call('deleteTermin', {id: terminId}).then(() => {
            window.setTimeout(() => {
                // TODO: This could probably be done more smoothly!
                window.location.reload();
            }, 1000);
        });
    });
    return false;
}
