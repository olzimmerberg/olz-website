import {olzApi} from '../../../Api/client';
import {initOlzEditNewsModal, OlzEditNewsModalMode} from '../OlzEditNewsModal/OlzEditNewsModal';
import {olzConfirm} from '../../../Components/Common/OlzConfirmationDialog/OlzConfirmationDialog';

import './OlzNewsDetail.scss';

export function editNews(
    newsId: number,
    mode: OlzEditNewsModalMode,
): boolean {
    olzApi.call('editNews', {id: newsId})
        .then((response) => {
            initOlzEditNewsModal(mode, response.id, response.meta, response.data);
        });
    return false;
}

export function deleteNewsArticle(newsId: number): boolean {
    olzConfirm('Wirklich löschen?').then(() => {
        olzApi.call('deleteNews', {id: newsId}).then(() => {
            window.setTimeout(() => {
                // This could probably be done more smoothly!
                window.location.reload();
            }, 1000);
        });
    });
    return false;
}
