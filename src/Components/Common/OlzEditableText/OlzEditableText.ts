import {olzApi} from '../../../Api/client';
import {initOlzEditSnippetModal} from '../../../Snippets';

export function olzEditableTextEditSnippet(
    snippetId: number,
): boolean {
    olzApi.call('editSnippet', {id: snippetId})
        .then((response) => {
            initOlzEditSnippetModal(response.id, response.meta, response.data);
        });
    return false;
}
