import {olzApi} from '../../../Api/client';
import {olzCustomReaction} from '../../../Components/Common/OlzCustomReactionDialog/OlzCustomReactionDialog';
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

export function toggleNewsReaction(
    newsEntryId: number,
    emoji: string,
): boolean {
    olzApi.call('toggleNewsReaction', {newsEntryId, emoji, action: 'toggle'})
        .then(() => {
            location.reload();
        });
    return false;
}

export function addCustomNewsReaction(
    newsEntryId: number,
): boolean {
    olzCustomReaction((emoji) => olzApi.call('toggleNewsReaction', {newsEntryId, emoji, action: 'toggle'}));
    return false;
}

export function activateNewsReactionLinks(
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
    const pathMatch = /^\/news\/([0-9]+)$/.exec(location.pathname);
    if (!pathMatch) {
        return;
    }
    const match = /^#react-(.+)$/.exec(location.hash);
    if (!match) {
        return;
    }
    const newsEntryId = Number(pathMatch[1]);
    const emoji = decodeURIComponent(match[1]);
    toggleNewsReaction(newsEntryId, emoji);
    location.hash = '';
});
