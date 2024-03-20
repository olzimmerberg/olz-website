import {olzApi} from '../../../Api/client';
import {initOlzEditKarteModal} from '../OlzEditKarteModal/OlzEditKarteModal';

import './OlzKartenListItem.scss';

export function kartenListItemEditKarte(
    karteId: number,
): boolean {
    olzApi.call('editKarte', {id: karteId})
        .then((response) => {
            initOlzEditKarteModal(response.id, response.meta, response.data);
        });
    return false;
}
