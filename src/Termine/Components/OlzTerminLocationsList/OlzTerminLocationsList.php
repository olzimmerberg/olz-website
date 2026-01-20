<?php

namespace Olz\Termine\Components\OlzTerminLocationsList;

use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Termine\TerminLocation;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzTerminLocationsListParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzTerminLocationsList extends OlzRootComponent {
    public function hasAccess(): bool {
        return $this->authUtils()->hasPermission('termine');
    }

    public function searchSqlWhenHasAccess(array $terms): string|array|null {
        return null;
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzTerminLocationsListParams::class);
        $code_href = $this->envUtils()->getCodeHref();

        $out = OlzHeader::render([
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
        $termin_locations = $termin_location_repo->findBy(['on_off' => 1]);
        $locations_data = array_map(function (TerminLocation $termin_location) use ($code_href) {
            return [
                'url' => "{$code_href}termine/orte/{$termin_location->getId()}",
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
                <div id='olz-termin-locations-map' class='test-flaky'></div>
                <script>olz.olzTerminLocationsMapRender({$locations_json});</script>
            </div>
            ZZZZZZZZZZ;

        $out .= OlzFooter::render();

        return $out;
    }
}
