<?php

namespace Olz\Components\Apps\OlzNoAppAccess;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\Users\User;

class OlzNoAppAccess extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $no_access_out = <<<'ZZZZZZZZZZ'
            <div class='alert alert-danger' role='alert'>
                Kein Zugriff!
            </div>
            ZZZZZZZZZZ;

        $code_href = $this->envUtils()->getCodeHref();
        $app = $args['app'];

        if ($this->authUtils()->hasPermission('any')) {
            // We're already logged in. No advertising an account necessary.
            return $no_access_out;
        }
        $hypothetical_logged_in_user = new User();
        $hypothetical_logged_in_user->setPermissions(' verified_email ');
        if (!$app->isAccessibleToUser($hypothetical_logged_in_user)) {
            // Hypothetical logged-in user wouldn't have access either.
            return $no_access_out;
        }
        $icon = $app->getIcon();
        $display_name = $app->getDisplayName();
        $basename = $app->getBasename();
        return <<<ZZZZZZZZZZ
            <div class='olz-no-app-access'>
                <div class='app-container hypothetical'>
                    <img src='{$icon}' alt='{$basename}-icon' class='app-icon' />
                    <div>{$display_name}</div>
                </div>
                <br />
                <div>Die "{$display_name}"-App ist nur für eingeloggte Benutzer verfügbar.</div>
                <div class='auth-buttons'>
                    <a class='btn btn-primary' href='#login-dialog' role='button'>Login</a>
                </div>
            </div>
            ZZZZZZZZZZ;
    }
}
