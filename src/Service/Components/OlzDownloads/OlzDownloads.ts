import {olzApi} from '../../../Api/client';
import {initOlzEditDownloadModal} from '../OlzEditDownloadModal/OlzEditDownloadModal';

import './OlzDownloads.scss';

export function olzDownloadsEditDownload(
    downloadId: number,
): boolean {
    olzApi.call('editDownload', {id: downloadId})
        .then((response) => {
            initOlzEditDownloadModal(response.id, response.meta, response.data);
        });
    return false;
}
