<?php

namespace Olz\Apps\Results\Components\OlzResults;

use Olz\Apps\Results\Metadata;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\EnvUtils;

class OlzResults {
    public static function render() {
        $out = '';

        $out .= OlzHeader::render([
            'title' => "Resultate",
            'norobots' => true,
        ]);

        if (isset($_GET['file'])) {
            $out .= "<div class='content-full' style='position:relative'>";
            $out .= <<<'ZZZZZZZZZZ'
            <div id='title-box'><div id='backbutton' onclick='olzResults.popHash()'>&lt;</div><h1 id='title'></h1></div>
            <div id='classes-box'></div>
            <div id='content-box'></div>
            <div class='inactive' id='grafik-box'><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' style='width:100%; height:100%;' id='grafik-svg'></svg></div>
            ZZZZZZZZZZ;
            $out .= "</div>";
        } else {
            $out .= "<div class='content-full'>";
            $out .= "<ul>";
            $env_utils = EnvUtils::fromEnv();
            $data_path = $env_utils->getDataPath();
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
