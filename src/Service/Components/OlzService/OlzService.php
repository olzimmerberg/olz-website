<?php

namespace Olz\Service\Components\OlzService;

use Olz\Components\Apps\OlzAppsList\OlzAppsList;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Service\Components\OlzDownloads\OlzDownloads;
use Olz\Service\Components\OlzLinks\OlzLinks;

class OlzService extends OlzComponent {
    public static $title = "Service";
    public static $description = "Diverse Online-Tools rund um OL und die OL Zimmerberg.";

    public function getHtml($args = []): string {
        $this->httpUtils()->validateGetParams([]);

        $out = OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
        ]);

        $out .= "<div class='content-full'>";

        $out .= "<h1>Service</h1>";
        $out .= "<h2>Apps</h2>";
        $out .= OlzAppsList::render();
        $out .= "<br /><br />";

        $out .= "<div class='responsive-flex'>";
        $out .= "<div class='responsive-flex-2'>";
        $out .= OlzLinks::render();
        $out .= "</div>";
        $out .= "<div class='responsive-flex-2'>";
        $out .= OlzDownloads::render();
        $out .= "</div></div><br><br>";

        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
