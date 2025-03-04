<?php

namespace Olz\Karten\Components\OlzKartenList;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\Karten\Karte;
use Olz\Karten\Components\OlzKartenListItem\OlzKartenListItem;

/** @extends OlzComponent<array<string, mixed>> */
class OlzKartenList extends OlzComponent {
    public function getHtml(mixed $args): string {
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

        $out .= "<table class='boxy'>";
        // @phpstan-ignore-next-line
        while ($row = $result->fetch_assoc()) {
            $karte = new Karte();
            $karte->setOwnerUser(null);
            $karte->setOwnerRole(null);
            $karte->setOnOff(1);
            $karte->setId(intval($row['id']));
            $karte->setKartenNr($row['kartennr'] ? intval($row['kartennr']) : null);
            // @phpstan-ignore-next-line
            $karte->setName($row['name']);
            $karte->setLatitude($row['latitude'] ? floatval($row['latitude']) : null);
            $karte->setLongitude($row['longitude'] ? floatval($row['longitude']) : null);
            // @phpstan-ignore-next-line
            $karte->setYear($row['jahr']);
            // @phpstan-ignore-next-line
            $karte->setScale($row['massstab']);
            // @phpstan-ignore-next-line
            $karte->setPlace($row['ort']);
            $karte->setZoom($row['zoom'] ? intval($row['zoom']) : null);
            // @phpstan-ignore-next-line
            $karte->setKind($row['typ']);
            // @phpstan-ignore-next-line
            $karte->setPreviewImageId($row['vorschau']);

            $kind = $row['typ'];
            $icon = null;
            if ($kind == 'ol') {
                $icon = 'orienteering_forest_16.svg';
            } elseif ($kind == 'stadt') {
                $icon = 'orienteering_village_16.svg';
            } elseif ($kind == 'scool') {
                $icon = 'orienteering_scool_16.svg';
            }
            if ($kind != $last_kind) {
                $kind_name = $kind_name_by_ident[$kind];
                $out .= <<<ZZZZZZZZZZ
                    <tr><td colspan='3'>
                        <h2 class='section-title'>
                            <img src='{$code_href}assets/icns/{$icon}' class='noborder' style='margin-right:10px;vertical-align:bottom;'>
                            {$kind_name}
                        </h2>
                    </td></tr>
                    ZZZZZZZZZZ;
                $last_kind = $kind;
            }

            $out .= OlzKartenListItem::render(['karte' => $karte]);
        }
        $out .= '</table>';

        return $out;
    }
}
