import $ from 'jquery';

import {initOlzUpdateUserAvatarModal, OlzUpdateUserAvatarModalChangeEvent} from '../OlzUpdateUserAvatarModal/OlzUpdateUserAvatarModal';

export function olzProfileFormUpdateAvatar(form: HTMLFormElement): boolean {
    const onChange = (e: OlzUpdateUserAvatarModalChangeEvent) => {
        form['avatar-id'].value = e.detail.uploadId;
        $('#avatar-img').attr('src', e.detail.dataUrl);
    };
    initOlzUpdateUserAvatarModal(onChange);
    return false;
}

export function olzProfileFormRemoveAvatar(form: HTMLFormElement): void {
    form['avatar-id'].value = '-';
}

export function olzProfileFormOnUsernameFocus(form: HTMLFormElement): void {
    const firstName = form['first-name'].value;
    const lastName = form['last-name'].value;
    if (form.username.value !== '' || !firstName || !lastName) {
        return;
    }
    const usernameSuggestion = getUsernameSuggestion(firstName, lastName);
    form.username.value = usernameSuggestion;
}

export function getUsernameSuggestion(firstName: string, lastName: string): string {
    return `${firstName} ${lastName}`.toLowerCase()
        .replace(/ä/g, 'ae').replace(/ö/g, 'oe').replace(/ü/g, 'ue')
        .replace(/ /g, '.').replace(/[^a-z0-9.-]/g, '');
}
