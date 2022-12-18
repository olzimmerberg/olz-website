import {callOlzApi} from '../../../../src/Api/client';
import {initOlzEditNewsModal} from '../OlzEditNewsModal/OlzEditNewsModal';
import {olzConfirm} from '../../../Components/Common/OlzConfirmationDialog/OlzConfirmationDialog';

export function editNewsArticle(newsId: number): boolean {
    callOlzApi('editNews', {id: newsId})
        .then((response) => {
            console.log(response);
            initOlzEditNewsModal(response.id, response.meta, response.data);
        });
    return false;
}

export function deleteNewsArticle(newsId: number): boolean {
    olzConfirm('Wirklich löschen?').then(() => {
        callOlzApi('deleteNews', {id: newsId});
    });
    return false;
}
