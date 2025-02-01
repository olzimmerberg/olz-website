<?php

// =============================================================================
// Strukturierte Daten (siehe https://schema.org) zur OL Zimmerberg.
// =============================================================================

namespace Olz\Components\Schema\OlzMapData;

use Olz\Components\Common\OlzComponent;

/** @extends OlzComponent<array<string, mixed>> */
class OlzMapData extends OlzComponent {
    public function getHtml(mixed $args): string {
        $name = $args['name'] ?? '';
        $year = $args['year'] ?? '';
        $scale = $args['scale'] ?? '';
        $year_description = $year ? " (Stand {$year})" : '';
        $scale_description = $scale ? " im Massstab {$scale}" : '';
        $olz_link_data = [
            '@type' => 'SportsOrganization',
            'identifier' => 'https://olzimmerberg.ch',
            'url' => 'https://olzimmerberg.ch',
            'name' => 'OL Zimmerberg',
        ];
        $map_data = [
            '@context' => 'https://schema.org',
            '@type' => 'Map',
            'mapType' => 'https://schema.org/VenueMap',
            'name' => $name,
            'description' => "Die Orientierungslaufkarte \"{$name}\"{$year_description}{$scale_description}, herausgegeben von der OL Zimmerberg.",
            'maintainer' => $olz_link_data,
            'provider' => $olz_link_data,
        ];
        $json_map_data = json_encode($map_data);
        return <<<ZZZZZZZZZZ
            <script type="application/ld+json">
            {$json_map_data}
            </script>
            ZZZZZZZZZZ;
    }
}
