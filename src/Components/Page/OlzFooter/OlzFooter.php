<?php

namespace Olz\Components\Page\OlzFooter;

use Olz\Components\Auth\OlzChangePasswordModal\OlzChangePasswordModal;
use Olz\Components\Auth\OlzLoginModal\OlzLoginModal;
use Olz\Components\Auth\OlzResetPasswordModal\OlzResetPasswordModal;
use Olz\Components\Auth\OlzSignUpModal\OlzSignUpModal;
use Olz\Components\Auth\OlzVerifyUserEmailModal\OlzVerifyUserEmailModal;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Notify\OlzLinkTelegramModal\OlzLinkTelegramModal;

class OlzFooter extends OlzComponent {
    public function getHtml($args = []): string {
        $code_href = $this->envUtils()->getCodeHref();

        $out = '';

        $out .= "<div style='clear:both;'>&nbsp;</div>";
        $out .= "</div>"; // site-background

        $out .= "<div class='footer'>";
        $out .= "<a href='{$code_href}fuer_einsteiger?von=footer'>Für Einsteiger</a>";
        $out .= "<a href='{$code_href}fragen_und_antworten'>Fragen &amp; Antworten (FAQ)</a>";
        $out .= "<a href='{$code_href}datenschutz'>Datenschutz</a>";
        $out .= "<a href='{$code_href}sitemap'>Sitemap</a>";
        $out .= "</div>"; // footer

        $out .= "</div>"; // site-container

        // "Legacy" component modals
        if (!($args['skip_modals'] ?? false)) {
            $out .= OlzChangePasswordModal::render([], $this);
            $out .= OlzLinkTelegramModal::render([], $this);
            $out .= OlzLoginModal::render([], $this);
            $out .= OlzResetPasswordModal::render([], $this);
            $out .= OlzSignUpModal::render([], $this);
            $out .= OlzVerifyUserEmailModal::render([], $this);
        }

        // React modals

        $out .= "<div id='edit-entity-react-root'></div>";
        $out .= "<div id='dialog-react-root'></div>";
        $out .= "<div id='update-user-avatar-react-root'></div>";

        $out .= "</body>
        </html>";

        return $out;
    }
}
