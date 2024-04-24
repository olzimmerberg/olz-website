<?php

namespace Olz\Karten\Components\OlzKarteDetail;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Components\Common\OlzLocationMap\OlzLocationMap;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Karten\Karte;

class OlzKarteDetail extends OlzComponent {
    public function getHtml($args = []): string {
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
        }

        $title = $karte->getName();
        $back_link = "{$code_href}karten";
        $out = OlzHeader::render([
            'back_link' => $back_link,
            'title' => "{$title} - Karten",
            'description' => "OL-Karten, die von der OL Zimmerberg unterhalten und angeboten werden.",
        ]);

        $out .= <<<'ZZZZZZZZZZ'
        <div class='content-right'>
        </div>
        <div class='content-middle'>
        ZZZZZZZZZZ;

        $name = $karte->getName();
        $kind = $karte->getKind();
        $scale = $karte->getScale();
        $year = $karte->getYear();
        $place = $karte->getPlace();
        $kartennr = $karte->getKartenNr();
        $center_x = $karte->getCenterX();
        $center_y = $karte->getCenterY();
        // TODO: Migrate to lat/long
        // $latitude = $karte->getLatitude();
        // $longitude = $karte->getLongitude();
        $preview_image_id = $karte->getPreviewImageId();

        $out .= "<div class='olz-karte-detail'>";

        $out .= OlzLocationMap::render([
            'name' => $name,
            'xkoord' => $center_x,
            'ykoord' => $center_y,
            'zoom' => 12,
        ]);

        // Editing Tools
        $is_owner = $user && intval($karte->getOwnerUser()?->getId() ?? 0) === intval($user->getId());
        $has_termine_permissions = $this->authUtils()->hasPermission('termine');
        $can_edit = $is_owner || $has_termine_permissions;
        if ($can_edit) {
            $json_id = json_encode(intval($id));
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
                <button
                    id='delete-karte-button'
                    class='btn btn-danger'
                    onclick='return olz.deleteKarte({$json_id})'
                >
                    <img src='{$code_href}assets/icns/delete_white_16.svg' class='noborder' />
                    Löschen
                </button>
            </div>
            ZZZZZZZZZZ;
        }

        $maybe_solv_link = '';
        if ($kartennr) {
            // SOLV-Kartenverzeichnis-Link zeigen
            $maybe_solv_link .= "<div><a href='https://www.swiss-orienteering.ch/karten/kartedetail.php?kid={$kartennr}' target='_blank' class='linkol'>SOLV Karten-Nr. {$kartennr}</a></div>\n";
        }

        $maybe_place = $place ? "<div>Ort: {$place}</div>" : '';

        $pretty_kind = [
            'ol' => "🌳 Wald-OL-Karte",
            'stadt' => "🏘️ Stadt-OL-Karte",
            'scool' => "🏫 sCOOL-Schulhaus-Karte",
        ][$kind] ?? "Unbekannter Kartentyp";

        $out .= <<<ZZZZZZZZZZ
        <h1>OL-Karte {$name}</h1>
        <div><b>{$pretty_kind}</b></div>
        <div>Masstab: {$scale}</div>
        <div>Stand: {$year}</div>
        {$maybe_place}
        {$maybe_solv_link}
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
        $out .= OlzEditableText::render(['snippet_id' => 12]);

        $out .= "</div>"; // olz-karte-detail
        $out .= "</div>"; // content-middle

        $out .= OlzFooter::render();

        return $out;
    }
}
