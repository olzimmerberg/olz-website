<?php

namespace Olz\Termine\Components\OlzTerminLocationDetail;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzLocationMap\OlzLocationMap;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\ImageUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzTerminLocationDetail extends OlzComponent {
    public function getHtml($args = []): string {
        $code_href = $this->envUtils()->getCodeHref();
        $db = $this->dbUtils()->getDb();
        $image_utils = ImageUtils::fromEnv();
        $user = $this->authUtils()->getCurrentUser();
        $this->httpUtils()->validateGetParams([
            'filter' => new FieldTypes\StringField(['allow_null' => true]),
            'id' => new FieldTypes\IntegerField(['allow_null' => true]),
        ], $_GET);
        $id = $args['id'] ?? null;

        $out = '';

        $sql = "SELECT * FROM termin_locations WHERE (id = '{$id}') AND (on_off = '1')";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();

        if (!$row) {
            $this->httpUtils()->dieWithHttpError(404);
        }

        $title = $row['name'] ?? '';
        $back_link = "{$code_href}termine";
        if ($_GET['filter'] ?? null) {
            $enc_filter = urlencode($_GET['filter']);
            $back_link = "{$code_href}termine?filter={$enc_filter}";
            if ($_GET['id'] ?? null) {
                $enc_id = urlencode($_GET['id']);
                $back_link = "{$code_href}termine/{$enc_id}?filter={$enc_filter}";
            }
        }
        $out .= OlzHeader::render([
            'back_link' => $back_link,
            'title' => "{$title} - Orte",
            'description' => "Orientierungslauf-Wettkämpfe, OL-Wochen, OL-Weekends, Trainings und Vereinsanlässe der OL Zimmerberg.",
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
            </div>
        </div>
        <div class='content-middle'>
        ZZZZZZZZZZ;

        $name = $row['name'] ?? '';
        $details = $row['details'] ?? '';
        $latitude = $row['latitude'] ?? '';
        $longitude = $row['longitude'] ?? '';
        $image_ids = json_decode($row['image_ids'] ?? 'null', true);

        $out .= "<div class='olz-termin-location-detail'>";

        // Editing Tools
        $is_owner = $user && intval($row['owner_user_id'] ?? 0) === intval($user->getId());
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
            'latitude' => $latitude,
            'longitude' => $longitude,
            'zoom' => 13,
            'width' => 720,
            'height' => 420,
        ]);

        $details_html = $this->htmlUtils()->renderMarkdown($details);
        $out .= "<div>{$details_html}</div>";

        if ($image_ids && count($image_ids) > 0) {
            $out .= "<h3>Bilder</h3><div class='lightgallery gallery-container'>";
            foreach ($image_ids as $image_id) {
                $out .= "<div class='gallery-image'>";
                $out .= $image_utils->olzImage(
                    'termin_locations', $id, $image_id, 110, 'gallery[myset]');
                $out .= "</div>";
            }
            $out .= "</div>";
        }

        $out .= "</div>"; // olz-termin-location-detail
        $out .= "</div>"; // content-middle

        $out .= OlzFooter::render();

        return $out;
    }
}
