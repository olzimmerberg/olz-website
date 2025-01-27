import {olzApi} from '../../../Api/client';
import {initOlzEditTerminTemplateModal} from '../OlzEditTerminTemplateModal/OlzEditTerminTemplateModal';

import './OlzTerminTemplateDetail.scss';

export function editTerminTemplate(
    terminTemplateId: number,
): boolean {
    olzApi.call('editTerminTemplate', {id: terminTemplateId})
        .then((response) => {
            initOlzEditTerminTemplateModal(response.id, response.meta, response.data);
        });
    return false;
}
