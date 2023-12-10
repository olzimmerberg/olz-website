<?php

namespace Olz\Termine\Components\OlzTerminLocationsList;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Termine\TerminLocation;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzTerminLocationsList extends OlzComponent {
    public function getHtml($args = []): string {
        $code_href = $this->envUtils()->getCodeHref();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $validated_get_params = $http_utils->validateGetParams([
            'filter' => new FieldTypes\StringField(['allow_null' => true]),
            'id' => new FieldTypes\IntegerField(['allow_null' => true]),
        ], $_GET);

        $out = '';

        $out .= OlzHeader::render([
            'back_link' => "{$code_href}termine",
            'title' => 'Termin-Orte',
            'description' => "Orte, an denen Anlässe der OL Zimmerberg stattfinden.",
            'norobots' => true,
        ]);

        // Creation Tools
        $has_termine_permissions = $this->authUtils()->hasPermission('termine');
        $creation_tools = '';
        if ($has_termine_permissions) {
            $creation_tools .= <<<ZZZZZZZZZZ
            <div class='create-termin-location-container'>
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

        $termin_location_repo = $this->entityManager()->getRepository(TerminLocation::class);
        $termin_locations = $termin_location_repo->findAll();
        $locations_data = array_map(function (TerminLocation $termin_location) {
            return [
                'name' => $termin_location->getName(),
                'lat' => $termin_location->getLatitude(),
                'lng' => $termin_location->getLongitude(),
            ];
        }, $termin_locations);
        $locations_json = json_encode($locations_data);

        $out .= <<<ZZZZZZZZZZ
        <div class='content-full'>
            {$creation_tools}
            <h1>Termin-Orte</h1>
            <div id='olz-termin-locations-map'></div>
            <script>olz.olzTerminLocationsMapRender({$locations_json});</script>
        </div>
        ZZZZZZZZZZ;

        $out .= OlzFooter::render();

        return $out;
    }
}
