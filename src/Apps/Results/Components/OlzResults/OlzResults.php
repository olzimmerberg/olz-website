<?php

namespace Olz\Apps\Results\Components\OlzResults;

use Olz\Apps\Results\Metadata;
use Olz\Components\Apps\OlzNoAppAccess\OlzNoAppAccess;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzResults extends OlzComponent {
    public function getHtml($args = []): string {
        $this->httpUtils()->validateGetParams([
            'file' => new FieldTypes\StringField(['allow_null' => true]),
        ]);

        $code_href = $this->envUtils()->getCodeHref();
        $data_path = $this->envUtils()->getDataPath();
        $filename = $this->getParams()['file'] ?? null;
        $metadata = new Metadata();

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
            'title' => "Resultate",
            'norobots' => true,
        ]);

        $out .= "<div class='content-full'>";
        if ($filename !== null) {
            if (is_file("{$data_path}results/{$filename}")) {
                $out .= <<<'ZZZZZZZZZZ'
                    <div id='title-box'><div id='backbutton' onclick='olzResults.popHash()'>&lt;</div><h1 id='title'></h1></div>
                    <div id='results-content'>
                        <div id='classes-box'></div>
                        <div id='content-box'></div>
                        <div class='inactive' id='grafik-box'><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' style='width:100%; height:100%;' id='grafik-svg'></svg></div>
                    </div>
                    ZZZZZZZZZZ;
            } elseif ($this->authUtils()->hasPermission('any')) {
                $enc_filename = json_encode(['file' => $filename]);
                $out .= <<<ZZZZZZZZZZ
                    <div>
                        <button
                            id='create-result-button'
                            class='btn btn-secondary'
                            onclick='return olzResults.initOlzEditResultModal(null, {$enc_filename})'
                        >
                            <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                            Resultate hochladen
                        </button>
                    </div>
                    ZZZZZZZZZZ;
            } else {
                $out .= OlzNoAppAccess::render([
                    'app' => $metadata,
                ]);
            }
        } else {
            $out .= "<ul>";
            $contents = scandir("{$data_path}results");
            foreach ($contents as $entry) {
                if (preg_match('/\.xml$/', $entry) && !preg_match('/\.bak\./', $entry)) {
                    $out .= "<li><a href='?file={$entry}'>{$entry}</a></li>\n";
                }
            }
            $out .= "</ul>";
            if ($this->authUtils()->hasPermission('any')) {
                $out .= <<<ZZZZZZZZZZ
                    <div>
                        <button
                            id='create-result-button'
                            class='btn btn-secondary'
                            onclick='return olzResults.initOlzEditResultModal()'
                        >
                            <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                            Resultate hochladen
                        </button>
                    </div>
                    ZZZZZZZZZZ;
            }
        }
        $out .= "</div>";

        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
