import React from 'react';
import {olzApi} from '../../../Api/client';
import {OlzEditableReactions} from '../../../Common/Components/OlzEditableReactions/OlzEditableReactions';
import {olzConfirm} from '../../../Common/Components/OlzConfirmationDialog/OlzConfirmationDialog';
import {initReact} from '../../../Utils/reactUtils';
import {initOlzEditTerminTemplateModal} from '../OlzEditTerminTemplateModal/OlzEditTerminTemplateModal';

import './OlzTerminTemplateDetail.scss';

export function editTerminTemplate(
    terminTemplateId: number,
): boolean {
    olzApi.call('editTerminTemplate', {id: terminTemplateId})
        .then((response) => {
            initOlzEditTerminTemplateModal(response.id, response.meta, response.data);
        });
    return false;
}

export function initTerminTemplateReactions(
    emojis: Array<string>,
): void {
    initReact(
        'termin-template-reactions',
        <OlzEditableReactions
            defaultEmojis={['👍', '🤩', '🙏', '😢', ...emojis]}
            listFn={() => Promise.resolve([])}
            toggleFn={() => olzConfirm('Termin-Vorlagen können nicht bewertet werden', {
                description: 'Dies ist nur eine Vorschau für den Termin.',
                confirmLabel: 'OK',
            }).then(() => null)}
        />,
    );
}
