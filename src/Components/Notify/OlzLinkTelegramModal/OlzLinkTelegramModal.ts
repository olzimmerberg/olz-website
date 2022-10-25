import $ from 'jquery';

import {callOlzApi} from '../../../../src/Api/client';

$(() => {
    $('#link-telegram-modal').on('shown.bs.modal', () => {
        olzLinkTelegramModalGetChatLink();
    });
});

export function olzLinkTelegramModalGetChatLink(): void {
    $('.chat-link-wait').show();
    $('.chat-link-ready').hide();

    callOlzApi(
        'linkTelegram',
        {},
    )
        .then((response) => {
            const chatLink = `https://t.me/${response.botName}`;
            const chatLinkPin = `${chatLink}?start=${response.pin}`;
            const chatMessage = `/start ${response.pin}`;
            $('#telegram-chat-link').attr('href', chatLink);
            $('#telegram-chat-link-pin').attr('href', chatLinkPin);
            $('#telegram-chat-message').val(chatMessage);
            $('.chat-link-wait').hide();
            $('.chat-link-ready').show();
        })
        .catch((err) => {
            $('.chat-link-wait').text(err.message);
        });
}
