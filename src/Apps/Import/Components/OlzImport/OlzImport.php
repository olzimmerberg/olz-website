<?php

namespace Olz\Apps\Import\Components\OlzImport;

use Olz\Apps\Import\Metadata;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;

class OlzImport extends OlzComponent {
    public function getHtml($args = []): string {
        require_once __DIR__.'/../../../../../_/config/init.php';
        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $out = '';

        $out .= OlzHeader::render([
            'back_link' => "{$code_href}apps/",
            'title' => "Import",
            'norobots' => true,
        ]);

        $user = $this->authUtils()->getCurrentUser();

        $out .= "<div class='content-full'>";
        if ($this->authUtils()->hasPermission('termine')) {
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
