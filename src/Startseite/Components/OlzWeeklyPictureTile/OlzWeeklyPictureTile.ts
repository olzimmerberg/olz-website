import {olzApi} from '../../../Api/client';
import {initOlzEditWeeklyPictureModal} from '../OlzEditWeeklyPictureModal/OlzEditWeeklyPictureModal';

import './OlzWeeklyPictureTile.scss';

export function editWeeklyPicture(id: number): boolean {
    olzApi.call('editWeeklyPicture', {id: id})
        .then((response) => {
            initOlzEditWeeklyPictureModal(response.id, response.meta, response.data);
        });
    return false;
}
