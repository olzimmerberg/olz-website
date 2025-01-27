import {olzApi} from '../../../Api/client';
import {initOlzEditKarteModal} from '../OlzEditKarteModal/OlzEditKarteModal';

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
