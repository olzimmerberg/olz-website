<?php

function olz_link_telegram_modal($args = []): string {
    global $_CONFIG;

    require_once __DIR__.'/../../../config/server.php';
    require_once __DIR__.'/../../../utils/client/UserAgentUtils.php';

    $user_agent_utils = getUserAgentUtilsFromEnv();

    $install_instructions = [
        "<li>Installiere die Telegram-App auf deinem Smartphone</li>",
        "<li>Logge dich in <a href='https://web.telegram.org' target='_blank'>Telegram Web</a> ein</li>",
        "<li><span id='chat-link-wait'>Bitte warten...</span><span id='chat-link-ready'>Öffne <a href='' target='_blank' id='telegram-chat-link'>deinen persönlichen <b>OLZ-Info-Chat</b></a> und <b>klicke auf &quot;OPEN IN WEB&quot;</b></span></li>",
    ];
    if ($user_agent_utils->isAndroidDevice() || $user_agent_utils->isIOsDevice()) {
        $install_instructions = [
            "<li><a href='https://telegram.org/dl/' target='_blank'>Installiere die Telegram-App</a></li>",
            "<li><span id='chat-link-wait'>Bitte warten...</span><span id='chat-link-ready'>Öffne <a href='' target='_blank' id='telegram-chat-link'>deinen persönlichen <b>OLZ-Info-Chat</b></a> und <b>klicke auf &quot;SEND MESSAGE&quot;</b></span></li>",
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
