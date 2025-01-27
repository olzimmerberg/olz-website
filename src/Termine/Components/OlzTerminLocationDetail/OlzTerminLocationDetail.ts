import {olzApi} from '../../../Api/client';
import {initOlzEditTerminLocationModal} from '../OlzEditTerminLocationModal/OlzEditTerminLocationModal';

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
