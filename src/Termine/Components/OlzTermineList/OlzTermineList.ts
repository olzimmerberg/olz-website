import {olzApi} from '../../../Api/client';
import {initOlzEditTerminLabelModal} from '../OlzEditTerminLabelModal/OlzEditTerminLabelModal';

import './OlzTermineList.scss';

export function termineListEditTerminLabel(id: number): boolean {
    olzApi.call('editTerminLabel', {id})
        .then((response) => {
            initOlzEditTerminLabelModal(
                response.id,
                response.meta,
                response.data,
            );
        });
    return false;
}
