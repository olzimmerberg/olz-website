import {olzApi} from '../../../../src/Api/client';
import {initOlzEditNewsModal, OlzEditNewsModalMode} from '../OlzEditNewsModal/OlzEditNewsModal';
import {olzConfirm} from '../../../Components/Common/OlzConfirmationDialog/OlzConfirmationDialog';

import './OlzNewsArticle.scss';

export function editNewsArticle(
    newsId: number,
    mode: OlzEditNewsModalMode,
): boolean {
    olzApi.call('editNews', {id: newsId})
        .then((response) => {
            console.log(response);
            initOlzEditNewsModal(mode, response.id, response.meta, response.data);
        });
    return false;
}

export function deleteNewsArticle(newsId: number): boolean {
    olzConfirm('Wirklich löschen?').then(() => {
        olzApi.call('deleteNews', {id: newsId});
    });
    return false;
}
