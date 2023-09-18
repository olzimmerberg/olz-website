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
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $validated_get_params = $http_utils->validateGetParams([
            'filter' => new FieldTypes\StringField(['allow_null' => true]),
            'id' => new FieldTypes\IntegerField(['allow_null' => true]),
        ], $_GET);

        $out = '';

        $out .= OlzHeader::render([
            'title' => 'Termin-Orte',
            'description' => "Orte, an denen Anlässe der OL Zimmerberg stattfinden.",
        ]);

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
        $out .= "<script>window.olzTerminLocationsList = {$locations_json};</script>";

        $out .= <<<'ZZZZZZZZZZ'
        <div class='content-full'>
            <h1>Termin-Orte</h1>
            <div id='map'></div>
        </div>
        ZZZZZZZZZZ;

        $out .= OlzFooter::render();

        return $out;
    }
}
