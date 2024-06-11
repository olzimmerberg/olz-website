import {olzApi} from '../../../Api/client';
import {initOlzEditKarteModal} from '../OlzEditKarteModal/OlzEditKarteModal';
import {olzConfirm} from '../../../Components/Common/OlzConfirmationDialog/OlzConfirmationDialog';

import './OlzKarteDetail.scss';

export function editKarte(
    karteId: number,
): boolean {
    olzApi.call('editKarte', {id: karteId})
        .then((response) => {
            initOlzEditKarteModal(response.id, response.meta, response.data);
        });
    return false;
}

export function deleteKarte(karteId: number): boolean {
    olzConfirm('Wirklich löschen?').then(() => {
        olzApi.call('deleteKarte', {id: karteId}).then(() => {
            window.setTimeout(() => {
                // This could probably be done more smoothly!
                window.location.reload();
            }, 1000);
        });
    });
    return false;
}
