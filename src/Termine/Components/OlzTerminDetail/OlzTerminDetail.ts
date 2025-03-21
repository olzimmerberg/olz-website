import {olzApi} from '../../../Api/client';
import {initOlzEditTerminModal} from '../OlzEditTerminModal/OlzEditTerminModal';

import './OlzTerminDetail.scss';

export function editTermin(
    terminId: number,
): boolean {
    olzApi.call('editTermin', {id: terminId})
        .then((response) => {
            initOlzEditTerminModal(
                response.id,
                response.data.fromTemplateId ?? undefined,
                response.meta,
                response.data,
            );
        });
    return false;
}
