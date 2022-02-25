import {callOlzApi} from '../../../api/client';
import {initOlzEditNewsModal} from '../OlzEditNewsModal/OlzEditNewsModal';

export function editNewsArticle(newsId: number): boolean {
    callOlzApi('editNews', {id: newsId})
        .then((response) => {
            console.log(response);
            initOlzEditNewsModal(response.id, response.data);
        });
    return false;
}
