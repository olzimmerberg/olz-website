<?php

namespace Olz\Apps\Results\Components\OlzResults;

use Olz\Apps\Results\Metadata;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzResults extends OlzComponent {
    public function getHtml($args = []): string {
        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $code_href = $this->envUtils()->getCodeHref();
        $data_path = $this->envUtils()->getDataPath();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $http_utils->validateGetParams([
            'file' => new FieldTypes\StringField(['allow_null' => true]),
        ], $_GET);

        $out = '';

        $out .= OlzHeader::render([
            'back_link' => "{$code_href}apps/",
            'title' => "Resultate",
            'norobots' => true,
        ]);

        if (isset($_GET['file'])) {
            $out .= "<div class='content-full'>";
            $out .= <<<'ZZZZZZZZZZ'
            <div id='title-box'><div id='backbutton' onclick='olzResults.popHash()'>&lt;</div><h1 id='title'></h1></div>
            <div id='results-content'>
                <div id='classes-box'></div>
                <div id='content-box'></div>
                <div class='inactive' id='grafik-box'><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' style='width:100%; height:100%;' id='grafik-svg'></svg></div>
            </div>
            ZZZZZZZZZZ;
            $out .= "</div>";
        } else {
            $out .= "<div class='content-full'>";
            $out .= "<ul>";
            $contents = scandir("{$data_path}results");
            foreach ($contents as $entry) {
                if (preg_match('/\.xml$/', $entry) && !preg_match('/\.bak\./', $entry)) {
                    $out .= "<li><a href='?file={$entry}'>{$entry}</a></li>\n";
                }
            }
            $out .= "</ul>";
            $out .= "</div>";
        }

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
