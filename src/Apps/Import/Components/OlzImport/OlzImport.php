<?php

namespace Olz\Apps\Import\Components\OlzImport;

use Olz\Apps\Import\Metadata;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\User;
use Olz\Utils\AuthUtils;
use Olz\Utils\DbUtils;

class OlzImport {
    public static function render() {
        require_once __DIR__.'/../../../../../_/config/init.php';

        session_start();

        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $out = '';

        $out .= OlzHeader::render([
            'title' => "Import",
            'norobots' => true,
        ]);

        $auth_utils = AuthUtils::fromEnv();
        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $user_repo = $entityManager->getRepository(User::class);
        $username = ($_SESSION['user'] ?? null);
        $user = $user_repo->findOneBy(['username' => $username]);

        $out .= "<div class='content-full'>";
        if ($auth_utils->hasPermission('termine')) {
            $out .= <<<'ZZZZZZZZZZ'
            <div id='pastebox' class='dropzone' contenteditable='true'>Zellen aus Excel kopieren und hier einfügen.</div>
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
