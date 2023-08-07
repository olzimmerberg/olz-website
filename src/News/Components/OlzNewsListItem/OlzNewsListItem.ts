import {olzApi} from '../../../../src/Api/client';
import {initOlzEditNewsModal, OlzEditNewsModalMode} from '../OlzEditNewsModal/OlzEditNewsModal';

import './OlzNewsListItem.scss';

export function newsListItemEditNewsArticle(
    newsId: number,
    mode: OlzEditNewsModalMode,
): boolean {
    olzApi.call('editNews', {id: newsId})
        .then((response) => {
            initOlzEditNewsModal(mode, response.id, response.meta, response.data);
        });
    return false;
}
