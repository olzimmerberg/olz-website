import {olzApi} from '../../../Api/client';
import {initOlzEditRunModal} from '../OlzEditRunModal/OlzEditRunModal';

import './OlzAnniversary.scss';

export function olzAnniversaryEditRun(
    runId: number,
): boolean {
    olzApi.call('editRun', {id: runId})
        .then((response) => {
            initOlzEditRunModal(response.id, response.meta, response.data);
        });
    return false;
}
