import {olzApi} from '../../../Api/client';
import {initOlzEditNewsModal, OlzEditNewsModalMode} from '../OlzEditNewsModal/OlzEditNewsModal';

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
