<?php

namespace Olz\Apps\Logs\Components\OlzLogs;

use Olz\Apps\Logs\Metadata;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\User;

class OlzLogs {
    public static function render() {
        global $entityManager;
        require_once __DIR__.'/../../../../../_/config/init.php';

        session_start();

        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $out = '';

        $out .= OlzHeader::render([
            'title' => "Logs",
            'norobots' => true,
        ]);

        require_once __DIR__.'/../../../../../_/config/doctrine_db.php';

        $user_repo = $entityManager->getRepository(User::class);
        $username = ($_SESSION['user'] ?? null);
        $user = $user_repo->findOneBy(['username' => $username]);

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

        $out .= "<div id='content_double'>";
        if ($user && $user->getPermissions() == 'all') {
            $out .= <<<'ZZZZZZZZZZ'
            <div class='logs-header'>
                <button type='button' class='form-control btn btn-outline-primary' onclick='olz.olzLogsGetNextLog()'>
                    Ältere laden
                </button>
                <select id='log-level-filter-select' class='form-control form-select' onchange='olz.olzLogsLevelFilterChange()'>
                    <option value='levels-all' selected>Alle Log-Levels</option>
                    <option value='levels-info-higher'>"Info" & höher</option>
                    <option value='levels-notice-higher'>"Notice" & höher</option>
                    <option value='levels-warning-higher'>"Warning" & höher</option>
                    <option value='levels-error-higher'>"Error" & höher</option>
                </select>
            </div>
            <div id='logs'></div>
            ZZZZZZZZZZ;
        } else {
            $out .= "<div id='profile-message' class='alert alert-danger' role='alert'>Kein Zugriff!</div>";
        }
        $out .= "</div>";

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
