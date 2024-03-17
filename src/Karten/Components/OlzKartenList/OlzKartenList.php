<?php

namespace Olz\Karten\Components\OlzKartenList;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\Karten\Karte;
use Olz\Karten\Components\OlzKartenListItem\OlzKartenListItem;

class OlzKartenList extends OlzComponent {
    public function getHtml($args = []): string {
        $out = '';

        $db = $this->dbUtils()->getDb();
        $code_href = $this->envUtils()->getCodeHref();

        $kind_name_by_ident = [
            'ol' => 'OL-Karten',
            'stadt' => 'Dorf-Karten',
            'scool' => 'sCOOL-Karten',
        ];

        $has_access = $this->authUtils()->hasPermission('karten');
        if ($has_access) {
            $out .= <<<ZZZZZZZZZZ
            <button
                id='create-karte-button'
                class='btn btn-secondary create-karte-container'
                onclick='return olz.initOlzEditKarteModal()'
            >
                <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                Neue Karte
            </button>
            ZZZZZZZZZZ;
        }

        $sql = "SELECT * FROM karten WHERE on_off = '1' ORDER BY CASE WHEN `typ` = 'ol' THEN 1 WHEN `typ` = 'stadt' THEN 2 WHEN `typ` = 'scool' THEN 3 ELSE 4 END, ort ASC, name ASC";
        $result = $db->query($sql);

        $last_kind = null;

        while ($row = $result->fetch_assoc()) {
            $karte = new Karte();
            $karte->setId($row['id']);
            $karte->setKartenNr($row['kartennr'] ? $row['kartennr'] : null);
            $karte->setName($row['name']);
            $karte->setCenterX($row['center_x']);
            $karte->setCenterY($row['center_y']);
            $karte->setLatitude($row['latitude']);
            $karte->setLongitude($row['longitude']);
            $karte->setYear($row['jahr']);
            $karte->setScale($row['massstab']);
            $karte->setPlace($row['ort']);
            $karte->setZoom($row['zoom']);
            $karte->setKind($row['typ']);
            $karte->setPreviewImageId($row['vorschau']);

            $kind = $row['typ'];
            if ($kind == 'ol') {
                $icon = 'orienteering_forest_16.svg';
            } elseif ($kind == 'stadt') {
                $icon = 'orienteering_village_16.svg';
            } elseif ($kind == 'scool') {
                $icon = 'orienteering_scool_16.svg';
            }
            if ($kind != $last_kind) {
                $kind_name = $kind_name_by_ident[$kind];
                $is_first = $last_kind === null;
                $tag = $is_first ? '' : '</table>';
                $out .= "{$tag}<h2><img src='{$code_href}assets/icns/{$icon}' class='noborder' style='margin-right:10px;vertical-align:bottom;'>{$kind_name}</h2><table class='liste'>";
                $last_kind = $kind;
            }

            $out .= OlzKartenListItem::render(['karte' => $karte]);
        }
        $out .= '</table>';

        return $out;
    }
}
