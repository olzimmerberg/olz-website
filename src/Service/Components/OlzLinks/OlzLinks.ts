import {olzApi} from '../../../../src/Api/client';
import {initOlzEditLinkModal} from '../OlzEditLinkModal/OlzEditLinkModal';

import './OlzLinks.scss';

export function olzLinksEditLink(
    linkId: number,
): boolean {
    olzApi.call('editLink', {id: linkId})
        .then((response) => {
            initOlzEditLinkModal(response.id, response.meta, response.data);
        });
    return false;
}
