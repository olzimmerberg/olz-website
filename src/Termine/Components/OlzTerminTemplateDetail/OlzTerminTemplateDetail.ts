import {olzApi} from '../../../../src/Api/client';
import {initOlzEditTerminTemplateModal} from '../OlzEditTerminTemplateModal/OlzEditTerminTemplateModal';
import {olzConfirm} from '../../../Components/Common/OlzConfirmationDialog/OlzConfirmationDialog';

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

export function deleteTerminTemplate(terminTemplateId: number): boolean {
    olzConfirm('Wirklich löschen?').then(() => {
        olzApi.call('deleteTerminTemplate', {id: terminTemplateId}).then(() => {
            window.setTimeout(() => {
                // TODO: This could probably be done more smoothly!
                window.location.reload();
            }, 1000);
        });
    });
    return false;
}
