import {olzApi} from '../../../Api/client';
import {olzCustomReaction} from '../../../Components/Common/OlzCustomReactionDialog/OlzCustomReactionDialog';
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

export function toggleTerminReaction(
    terminId: number,
    emoji: string,
): boolean {
    olzApi.call('toggleTerminReaction', {terminId, emoji, action: 'toggle'})
        .then(() => {
            location.reload();
        });
    return false;
}

export function addCustomTerminReaction(
    terminId: number,
): boolean {
    olzCustomReaction((emoji) => olzApi.call('toggleTerminReaction', {terminId, emoji, action: 'on'}));
    return false;
}

export function activateTerminReactionLinks(
    activeByEmoji: {[emoji: string]: boolean},
): void {
    for (const emoji in activeByEmoji) {
        if (activeByEmoji[emoji]) {
            const selector = `.rendered-markdown a[href^='#react-${encodeURIComponent(emoji)}']`;
            document.querySelectorAll(selector).forEach((elem) => {
                elem.classList.add('active');
            });
        }
    }
}

window.addEventListener('hashchange', () => {
    const pathMatch = /^\/termine\/([0-9]+)$/.exec(location.pathname);
    if (!pathMatch) {
        return;
    }
    const match = /^#react-(.+)$/.exec(location.hash);
    if (!match) {
        return;
    }
    const terminId = Number(pathMatch[1]);
    const emoji = decodeURIComponent(match[1]);
    toggleTerminReaction(terminId, emoji);
    location.hash = '';
});
