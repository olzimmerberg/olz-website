<?php

function olz_link_telegram_modal($args = []): string {
    global $_CONFIG;

    require_once __DIR__.'/../../../config/server.php';
    require_once __DIR__.'/../../../utils/client/UserAgentUtils.php';

    $user_agent_utils = UserAgentUtils::fromEnv();

    $install_instructions = [
        "<li>Installiere die Telegram-App auf deinem Smartphone</li>",
        "<li>Logge dich in <a href='https://web.telegram.org' rel='noopener noreferrer' target='_blank'>Telegram Web</a> ein</li>",
        "<li><span class='chat-link-wait'>Bitte warten...</span><span class='chat-link-ready'>Öffne <a href='' rel='noopener noreferrer' target='_blank' id='telegram-chat-link'>deinen persönlichen OLZ-Info-Chat</a></span></li>",
        "<li>Klicke auf &quot;OPEN IN WEB&quot;</li>",
        "<li><span class='chat-link-wait'>Bitte warten...</span><span class='chat-link-ready'>Sende dem OLZ Bot folgende Nachricht: <input type='text' id='telegram-chat-message' class='form-control' readonly /></span></li>",
    ];
    if ($user_agent_utils->isAndroidDevice() || $user_agent_utils->isIOsDevice()) {
        $install_instructions = [
            "<li><a href='https://telegram.org/dl/' rel='noopener noreferrer' target='_blank'>Installiere die Telegram-App</a></li>",
            "<li><span class='chat-link-wait'>Bitte warten...</span><span class='chat-link-ready'>Öffne <a href='' rel='noopener noreferrer' target='_blank' id='telegram-chat-link-pin'>deinen persönlichen OLZ-Info-Chat</a></span></li>",
            "<li>Klicke auf &quot;SEND MESSAGE&quot;</li>",
            "<li>...und dann auf &quot;START&quot;</li>",
        ];
    }

    $install_instructions_string = implode("\n", $install_instructions);

    return <<<ZZZZZZZZZZ
    <div class='modal fade' id='link-telegram-modal' tabindex='-1' aria-labelledby='link-telegram-modal-label' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='link-telegram-modal-label'>Telegram-Infos aktivieren</h5>
                    <button type='button' class='close' data-dismiss='modal' aria-label='Schliessen'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
                </div>
                <div class='modal-body'>
                    <center>
                        <div class='telegram-circle'>
                            <img src='{$_CONFIG->getCodeHref()}icns/login_telegram.svg' alt=''>
                        </div>
                    </center>
                    <ol class='todo-list'>
                        {$install_instructions_string}
                    </ol>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-secondary' data-dismiss='modal'>Schliessen</button>
                </div>
            </div>
        </div>
    </div>
    ZZZZZZZZZZ;
}
