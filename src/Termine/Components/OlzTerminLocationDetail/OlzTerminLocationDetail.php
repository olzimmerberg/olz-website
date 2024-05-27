<?php

namespace Olz\Termine\Components\OlzTerminLocationDetail;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzLocationMap\OlzLocationMap;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Termine\TerminLocation;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzTerminLocationDetail extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $params = $this->httpUtils()->validateGetParams([
            'filter' => new FieldTypes\StringField(['allow_null' => true]),
            'id' => new FieldTypes\IntegerField(['allow_null' => true]),
        ]);

        $code_href = $this->envUtils()->getCodeHref();
        $user = $this->authUtils()->getCurrentUser();
        $id = $args['id'] ?? null;

        $termin_location = $this->getTerminLocationById($id);

        if (!$termin_location) {
            $this->httpUtils()->dieWithHttpError(404);
        }

        $title = $termin_location->getName() ?? '';
        $back_link = "{$code_href}termine";
        if ($params['filter'] ?? null) {
            $enc_filter = urlencode($params['filter']);
            $back_link = "{$code_href}termine?filter={$enc_filter}";
            if ($params['id'] ?? null) {
                $enc_id = urlencode($params['id']);
                $back_link = "{$code_href}termine/{$enc_id}?filter={$enc_filter}";
            }
        }
        $out = OlzHeader::render([
            'back_link' => $back_link,
            'title' => "{$title} - Orte",
            'description' => "Orte, an denen Anlässe der OL Zimmerberg stattfinden.",
            'norobots' => true,
        ]);

        // Creation Tools
        $has_termine_permissions = $this->authUtils()->hasPermission('termine');
        $creation_tools = '';
        if ($has_termine_permissions) {
            $creation_tools .= <<<ZZZZZZZZZZ
                <div>
                    <button
                        id='create-termin-location-button'
                        class='btn btn-secondary'
                        onclick='return olz.initOlzEditTerminLocationModal()'
                    >
                        <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                        Neuen Ort hinzufügen
                    </button>
                </div>
                ZZZZZZZZZZ;
        }

        $out .= <<<ZZZZZZZZZZ
            <div class='content-right'>
                <div style='padding:4px 3px 10px 3px;'>
                    {$creation_tools}
                    <p>
                        <a href='{$code_href}termine/orte' class='linkint'>
                            Alle Termin-Orte
                        </a>
                    </p>
                </div>
            </div>
            <div class='content-middle'>
            ZZZZZZZZZZ;

        $name = $termin_location->getName() ?? '';
        $name = $termin_location->getName() ?? '';
        $details = $termin_location->getDetails() ?? '';
        $latitude = $termin_location->getLatitude() ?? '';
        $longitude = $termin_location->getLongitude() ?? '';
        $image_ids = $termin_location->getImageIds();

        $out .= "<div class='olz-termin-location-detail'>";

        // Editing Tools
        $is_owner = $user && intval($termin_location->getOwnerUser()?->getId() ?? 0) === intval($user->getId());
        $has_termine_permissions = $this->authUtils()->hasPermission('termine');
        $can_edit = $is_owner || $has_termine_permissions;
        if ($can_edit) {
            $json_id = json_encode(intval($id));
            $out .= <<<ZZZZZZZZZZ
                <div>
                    <button
                        id='edit-termin-location-button'
                        class='btn btn-primary'
                        onclick='return olz.editTerminLocation({$json_id})'
                    >
                        <img src='{$code_href}assets/icns/edit_white_16.svg' class='noborder' />
                        Bearbeiten
                    </button>
                    <button
                        id='delete-termin-location-button'
                        class='btn btn-danger'
                        onclick='return olz.deleteTerminLocation({$json_id})'
                    >
                        <img src='{$code_href}assets/icns/delete_white_16.svg' class='noborder' />
                        Löschen
                    </button>
                </div>
                ZZZZZZZZZZ;
        }

        $out .= "<h1>{$name}</h1>";

        $out .= OlzLocationMap::render([
            'name' => $name,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'zoom' => 13,
        ]);

        $details_html = $this->htmlUtils()->renderMarkdown($details);
        $details_html = $termin_location->replaceImagePaths($details_html);
        $details_html = $termin_location->replaceFilePaths($details_html);
        $out .= "<div>{$details_html}</div>";

        if ($image_ids && count($image_ids) > 0) {
            $out .= "<h3>Bilder</h3><div class='lightgallery gallery-container'>";
            foreach ($image_ids as $image_id) {
                $out .= "<div class='gallery-image'>";
                $out .= $this->imageUtils()->olzImage(
                    'termin_locations',
                    $id,
                    $image_id,
                    110,
                    'gallery[myset]'
                );
                $out .= "</div>";
            }
            $out .= "</div>";
        }

        $out .= "</div>"; // olz-termin-location-detail
        $out .= "</div>"; // content-middle

        $out .= OlzFooter::render();

        return $out;
    }

    protected function getTerminLocationById(int $id): ?TerminLocation {
        $termin_location_repo = $this->entityManager()->getRepository(TerminLocation::class);
        return $termin_location_repo->findOneBy([
            'id' => $id,
            'on_off' => 1,
        ]);
    }
}
