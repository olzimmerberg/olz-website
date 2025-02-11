import {olzApi} from '../../../Api/client';
import {initOlzEditQuestionModal} from '../OlzEditQuestionModal/OlzEditQuestionModal';
import {initOlzEditQuestionCategoryModal} from '../OlzEditQuestionCategoryModal/OlzEditQuestionCategoryModal';

import './OlzFaqList.scss';

export function faqListEditQuestion(id: number): boolean {
    olzApi.call('editQuestion', {id})
        .then((response) => {
            initOlzEditQuestionModal(response.id, response.meta, response.data);
        });
    return false;
}

export function faqListEditQuestionCategory(id: number): boolean {
    olzApi.call('editQuestionCategory', {id})
        .then((response) => {
            initOlzEditQuestionCategoryModal(response.id, response.meta, response.data);
        });
    return false;
}
