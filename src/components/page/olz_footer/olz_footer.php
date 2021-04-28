<?php

function olz_footer($args = []): string {
    $out = '';

    $out .= "<div style='clear:both;'>&nbsp;</div>";
    $out .= "</div>"; // site-background

    $out .= "<div class='footer'>";
    $out .= "<a href='fuer_einsteiger.php?von=footer'>Für Einsteiger</a>";
    $out .= "<a href='fragen_und_antworten.php'>Fragen &amp; Antworten (FAQ)</a>";
    $out .= "<a href='datenschutz.php'>Datenschutz</a>";
    $out .= "</div>"; // footer

    $out .= "</div>"; // site-container

    require_once __DIR__.'/../../auth/olz_login_modal/olz_login_modal.php';
    $out .= olz_login_modal();

    require_once __DIR__.'/../../auth/olz_sign_up_modal/olz_sign_up_modal.php';
    $out .= olz_sign_up_modal();

    require_once __DIR__."/../../auth/olz_change_password_modal/olz_change_password_modal.php";
    $out .= olz_change_password_modal();

    require_once __DIR__."/../../notify/olz_link_telegram_modal/olz_link_telegram_modal.php";
    $out .= olz_link_telegram_modal();

    $out .= "</body>
    </html>";

    return $out;
}
