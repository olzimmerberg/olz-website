import React from 'react';
import {olzApi} from '../../../Api/client';
import {OlzEditableReactions} from '../../../Components/Common/OlzEditableReactions/OlzEditableReactions';
import {initReact} from '../../../Utils/reactUtils';
import {initOlzEditTerminModal} from '../OlzEditTerminModal/OlzEditTerminModal';

import './OlzTerminDetail.scss';

export function editTermin(
    terminId: number,
): boolean {
    olzApi.call('editTermin', {id: terminId})
        .then((response) => {
            initOlzEditTerminModal(
                response.id,
                response.data.fromTemplateId ?? undefined,
                response.meta,
                response.data,
            );
        });
    return false;
}

export function initTerminReactions(
    terminId: number,
    emojis: Array<string>,
): void {
    initReact(
        'termin-reactions',
        <OlzEditableReactions
            defaultEmojis={['👍', '🤩', '🙏', '😢', ...emojis]}
            listFn={
                () => olzApi.call(
                    'listTerminReactions',
                    {filter: {terminId}},
                ).then((resp) => resp.result)
            }
            toggleFn={
                (userId, emoji) => olzApi.call(
                    'toggleTerminReaction',
                    {userId, terminId, emoji, action: 'toggle'},
                ).then((resp) => resp.result)
            }
        />,
    );
}
