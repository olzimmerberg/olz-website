<?php

namespace Olz\Karten\Components\OlzKarteDetail;

use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Components\Common\OlzLocationMap\OlzLocationMap;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Karten\Karte;
use Olz\Repository\Snippets\PredefinedSnippet;

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzKarteDetail extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function getSearchTitle(): string {
        return 'Karten';
    }

    public function getSearchResultsWhenHasAccess(array $terms): ?array {
        return null; // TODO: Remove after migration
    }

    public function searchSqlWhenHasAccess(array $terms): ?string {
        $code_href = $this->envUtils()->getCodeHref();
        $where = implode(' AND ', array_map(
            fn ($term) => "(k.name LIKE '%{$term}%' OR k.ort LIKE '%{$term}%')",
            $terms,
        ));
        return <<<ZZZZZZZZZZ
            SELECT
                CONCAT('{$code_href}karten/', k.id) AS link,
                '{$code_href}assets/icns/link_map_16.svg' AS icon,
                NULL AS date,
                k.name AS title,
                k.ort AS text
            FROM karten k
            WHERE
                k.on_off = '1' AND {$where}
            ZZZZZZZZZZ;
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $code_href = $this->envUtils()->getCodeHref();
        $data_href = $this->envUtils()->getDataHref();
        $karten_repo = $this->entityManager()->getRepository(Karte::class);
        $user = $this->authUtils()->getCurrentUser();
        $id = $args['id'] ?? null;

        $karte = $karten_repo->findOneBy([
            'id' => $id,
            'on_off' => 1,
        ]);

        if (!$karte) {
            $this->httpUtils()->dieWithHttpError(404);
            throw new \Exception('should already have failed');
        }

        $name = $karte->getName();
        $kind = $karte->getKind();
        $scale = $karte->getScale();
        $year = $karte->getYear();
        $place = $karte->getPlace();
        $kartennr = $karte->getKartenNr();
        $latitude = $karte->getLatitude();
        $longitude = $karte->getLongitude();
        $preview_image_id = $karte->getPreviewImageId();

        $pretty_kind = [
            'ol' => "🌳 Wald-OL-Karte",
            'stadt' => "🏘️ Stadt-OL-Karte",
            'scool' => "🏫 sCOOL-Schulhaus-Karte",
        ][$kind] ?? "Unbekannter Kartentyp";

        $title = $karte->getName();
        $back_link = "{$code_href}karten";
        $maybe_place = $place ? "Ort: {$place}, " : '';
        $out = OlzHeader::render([
            'back_link' => $back_link,
            'title' => "{$title} - Karten",
            'description' => "OL-Karte {$name} ({$pretty_kind}), Masstab: {$scale}, Stand {$year}, {$maybe_place}Herausgeber: OL Zimmerberg.",
        ]);

        $out .= <<<'ZZZZZZZZZZ'
            <div class='content-right'>
            </div>
            <div class='content-middle'>
            ZZZZZZZZZZ;
        $out .= "<div class='olz-karte-detail'>";

        $out .= OlzLocationMap::render([
            'name' => $name,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'zoom' => 12,
        ]);

        // Editing Tools
        $is_owner = $user && intval($karte->getOwnerUser()?->getId() ?? 0) === intval($user->getId());
        $has_karten_permissions = $this->authUtils()->hasPermission('karten');
        $can_edit = $is_owner || $has_karten_permissions;
        if ($can_edit) {
            $json_id = json_encode($id);
            $out .= <<<ZZZZZZZZZZ
                <div>
                    <button
                        id='edit-karte-button'
                        class='btn btn-primary'
                        onclick='return olz.editKarte({$json_id})'
                    >
                        <img src='{$code_href}assets/icns/edit_white_16.svg' class='noborder' />
                        Bearbeiten
                    </button>
                </div>
                ZZZZZZZZZZ;
        }

        $maybe_place = $place ? "<div>Ort: {$place}</div>" : '';

        $maybe_solv_link = '';
        if ($kartennr) {
            // SOLV-Kartenverzeichnis-Link zeigen
            $maybe_solv_link .= "<div><a href='https://www.swiss-orienteering.ch/karten/kartedetail.php?kid={$kartennr}' target='_blank' class='linkol'>SOLV Karten-Nr. {$kartennr}</a></div>\n";
        }

        $maybe_omap_link = '';
        if ($latitude !== null && $longitude !== null) {
            // OMap-Kartenverzeichnis-Link zeigen
            $maybe_omap_link .= "<div><a href='https://omap.ch/map.php#7/{$latitude}/{$longitude}' target='_blank' class='linkol'>Karte auf omap.ch</a></div>\n";
        }

        $out .= <<<ZZZZZZZZZZ
            <h1>OL-Karte {$name}</h1>
            <div><b>{$pretty_kind}</b></div>
            <div>Masstab: {$scale}</div>
            <div>Stand: {$year}</div>
            {$maybe_place}
            {$maybe_solv_link}
            {$maybe_omap_link}
            <div>Herausgeber: OL Zimmerberg</div>
            ZZZZZZZZZZ;

        if ($preview_image_id) {
            $img_href = "{$data_href}img/karten/{$id}/img/{$preview_image_id}";

            $out .= <<<ZZZZZZZZZZ
                <h3>Vorschau</h3>
                <div class='olz-karte-preview'>
                    <img
                        src='{$img_href}'
                        alt='OL-Karte {$name}'
                        class='noborder'
                    />
                </div>
                ZZZZZZZZZZ;
        }

        $out .= "<h2>Kartenverkauf</h2>";
        $out .= OlzEditableText::render(['snippet' => PredefinedSnippet::KartenVerkauf]);

        $out .= "</div>"; // olz-karte-detail
        $out .= "</div>"; // content-middle

        $out .= OlzFooter::render();

        return $out;
    }
}
