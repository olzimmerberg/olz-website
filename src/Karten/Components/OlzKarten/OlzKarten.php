<?php

namespace Olz\Karten\Components\OlzKarten;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Karten\Karte;
use Olz\Karten\Components\OlzKartenList\OlzKartenList;

class OlzKarten extends OlzComponent {
    public static string $title = "Karten";
    public static string $description = "Die OL-Karten, die die OL Zimmerberg aufnimmt, unterhält und verkauft.";

    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $this->httpUtils()->validateGetParams([]);
        $db = $this->dbUtils()->getDb();
        $code_href = $this->envUtils()->getCodeHref();

        $out = OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
        ]);

        $out .= "<div class='content-right'>";
        $out .= OlzKartenList::render([]);
        $out .= "</div>
        <div class='content-middle'>";

        $karten_repo = $this->entityManager()->getRepository(Karte::class);
        $karten = $karten_repo->findAll();
        $karten_data = array_map(function (Karte $karte) use ($code_href) {
            $icon_by_type = [
                'ol' => 'orienteering_forest_16.svg',
                'stadt' => 'orienteering_village_16.svg',
                'scool' => 'orienteering_scool_16.svg',
            ];
            return [
                'id' => $karte->getId(),
                'url' => "{$code_href}karten/{$karte->getIdent()}",
                'icon' => $icon_by_type[$karte->getKind()] ?? 'orienteering_scool_16.svg',
                'name' => $karte->getName(),
                'lat' => $karte->getLatitude(),
                'lng' => $karte->getLongitude(),
            ];
        }, $karten);
        $karten_json = json_encode($karten_data);

        $out .= <<<ZZZZZZZZZZ
            <div id='olz-karten-map' class='test-flaky'></div>
            <script>olz.olzKartenMapRender({$karten_json});</script>
            <br>
            ZZZZZZZZZZ;

        $out .= "<h2>Kartenverkauf</h2>";
        $out .= OlzEditableText::render(['snippet_id' => 12]);
        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
