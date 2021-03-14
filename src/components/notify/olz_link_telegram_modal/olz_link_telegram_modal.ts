import {OlzApiEndpoint, callOlzApi} from '../../../api/client';

$(() => {
    $('#link-telegram-modal').on('shown.bs.modal', () => {
        olzLinkTelegramModalGetChatLink();
    });
});

export function olzLinkTelegramModalGetChatLink(): void {
    $('#chat-link-wait').show();
    $('#chat-link-ready').hide();

    callOlzApi(
        OlzApiEndpoint.linkTelegram,
        {},
    )
        .then((response) => {
            $('#telegram-chat-link').attr('href', response.chatLink);
            $('#chat-link-wait').hide();
            $('#chat-link-ready').show();
        })
        .catch((err) => {
            $('#chat-link-wait').text(err.message);
        });
}
