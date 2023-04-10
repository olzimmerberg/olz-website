<?php

namespace Olz\Apps\Logs\Components\OlzLogs;

use Olz\Apps\Logs\Metadata;
use Olz\Apps\Logs\Utils\LogsDefinitions;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\AbstractDateUtils;
use Olz\Utils\AuthUtils;

class OlzLogs extends OlzComponent {
    public function getHtml($args = []): string {
        require_once __DIR__.'/../../../../../_/config/init.php';
        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $out = '';

        $out .= OlzHeader::render([
            'back_link' => "{$code_href}apps/",
            'title' => "Logs",
            'norobots' => true,
        ]);

        $date_utils = AbstractDateUtils::fromEnv();
        $auth_utils = AuthUtils::fromEnv();
        $user = $auth_utils->getCurrentUser();

        $out .= <<<'ZZZZZZZZZZ'
        <style>
        .menu-container {
            max-width: none;
        } 
        .site-container {
            max-width: none;
        }
        </style>
        ZZZZZZZZZZ;

        $out .= "<div class='content-full olz-logs'>";
        if ($user && $user->getPermissions() == 'all') {
            $iso_now = $date_utils->getIsoNow();
            $esc_now = json_encode($iso_now);
            $channels_data = [];
            foreach (LogsDefinitions::getLogsChannels() as $channel) {
                $channels_data[$channel::getId()] = $channel::getName();
            }
            $esc_channels = json_encode($channels_data);
            $out .= <<<ZZZZZZZZZZ
                <script>
                    window.olzLogsNow = {$esc_now};
                    window.olzLogsChannels = {$esc_channels};
                </script>
                <div id='react-root'></div>
            ZZZZZZZZZZ;
        } else {
            $out .= "<div class='alert alert-danger' role='alert'>Kein Zugriff!</div>";
        }
        $out .= "</div>";

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
