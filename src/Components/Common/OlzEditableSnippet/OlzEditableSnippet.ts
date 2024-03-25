import {olzApi} from '../../../Api/client';
import {initOlzEditSnippetModal} from '../../../Snippets';

import './OlzEditableSnippet.scss';

export function olzEditableSnippetEditSnippet(
    snippetId: number,
): boolean {
    olzApi.call('editSnippet', {id: snippetId})
        .then((response) => {
            initOlzEditSnippetModal(response.id, response.meta, response.data);
        });
    return false;
}
