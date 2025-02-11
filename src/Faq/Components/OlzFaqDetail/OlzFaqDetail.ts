import {olzApi} from '../../../Api/client';
import {initOlzEditQuestionModal} from '../OlzEditQuestionModal/OlzEditQuestionModal';

export function editQuestion(id: number): boolean {
    olzApi.call('editQuestion', {id})
        .then((response) => {
            initOlzEditQuestionModal(response.id, response.meta, response.data);
        });
    return false;
}
