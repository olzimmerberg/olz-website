import $ from 'jquery';

import {olzApi} from '../../../../src/Api/client';

$(() => {
    const linkTelegramModal = document.getElementById('link-telegram-modal');
    linkTelegramModal?.addEventListener('shown.bs.modal', () => {
        olzLinkTelegramModalGetChatLink();
    });
});

export function olzLinkTelegramModalGetChatLink(): void {
    $('.chat-link-wait').show();
    $('.chat-link-ready').hide();

    olzApi.call(
        'linkTelegram',
        {},
    )
        .then((response) => {
            const chatLinkDesktop = `https://web.telegram.org/k/#@${response.botName}`;
            const chatLinkMobile = `https://t.me/${response.botName}?start=${response.pin}`;
            const chatMessage = `/start ${response.pin}`;
            $('#telegram-chat-link-desktop').attr('href', chatLinkDesktop);
            $('#telegram-chat-link-mobile').attr('href', chatLinkMobile);
            $('#telegram-chat-message').val(chatMessage);
            $('.chat-link-wait').hide();
            $('.chat-link-ready').show();
        })
        .catch((err) => {
            $('.chat-link-wait').text(err.message);
        });
}
