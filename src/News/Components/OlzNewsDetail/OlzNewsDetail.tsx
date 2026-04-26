import React from 'react';
import {olzApi} from '../../../Api/client';
import {OlzEditableReactions} from '../../../Components/Common/OlzEditableReactions/OlzEditableReactions';
import {initReact} from '../../../Utils/reactUtils';
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

export function initNewsReactions(
    newsEntryId: number,
): void {
    initReact(
        'news-reactions',
        <OlzEditableReactions
            defaultEmojis={['👍', '🤩', '🙏', '😢']}
            listFn={
                () => olzApi.call(
                    'listNewsReactions',
                    {filter: {newsEntryId}},
                ).then((resp) => resp.result)
            }
            toggleFn={
                (userId, emoji) => olzApi.call(
                    'toggleNewsReaction',
                    {userId, newsEntryId, emoji, action: 'toggle'},
                ).then((resp) => resp.result)
            }
        />,
    );
}
