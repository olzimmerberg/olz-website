<?php

namespace Olz\Apps\Results\Components\OlzResults;

use Olz\Apps\Results\Metadata;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;

class OlzResults {
    public static function render() {
        $out = '';

        $out .= OlzHeader::render([
            'title' => "Resultate",
            'norobots' => true,
        ]);

        $out .= "<div id='content_double' style='position:relative'>";
        $out .= <<<'ZZZZZZZZZZ'
        <div id='title-box'><div id='backbutton' onclick='olz.popHash()'>&lt;</div><h1 id='title'></h1></div>
        <div id='classes-box'></div>
        <div id='content-box'></div>
        <div class='inactive' id='grafik-box'><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' style='width:100%; height:100%;' id='grafik-svg'></svg></div>
        ZZZZZZZZZZ;
        $out .= "</div>";

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
