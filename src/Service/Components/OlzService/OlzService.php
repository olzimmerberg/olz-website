<?php

namespace Olz\Service\Components\OlzService;

use Olz\Components\Apps\OlzAppsList\OlzAppsList;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Service\Components\OlzDownloads\OlzDownloads;
use Olz\Service\Components\OlzLinks\OlzLinks;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzServiceParams extends HttpParams {
}

class OlzService extends OlzComponent {
    public static string $title = "Service";
    public static string $description = "Diverse Online-Tools rund um OL und die OL Zimmerberg.";

    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $this->httpUtils()->validateGetParams(OlzServiceParams::class);

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
