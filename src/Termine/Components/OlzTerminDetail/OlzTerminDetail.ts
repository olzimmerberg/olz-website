import {olzApi} from '../../../../src/Api/client';
import {initOlzEditTerminModal} from '../OlzEditTerminModal/OlzEditTerminModal';
import {olzConfirm} from '../../../Components/Common/OlzConfirmationDialog/OlzConfirmationDialog';

import './OlzTerminDetail.scss';

export function editTermin(
    terminId: number,
): boolean {
    olzApi.call('editTermin', {id: terminId})
        .then((response) => {
            console.log(response);
            initOlzEditTerminModal(response.id, response.meta, response.data);
        });
    return false;
}

export function deleteTermin(terminId: number): boolean {
    olzConfirm('Wirklich löschen?').then(() => {
        olzApi.call('deleteTermin', {id: terminId});
    });
    return false;
}
