<?php

// =============================================================================
// Strukturierte Daten (siehe https://schema.org) zur OL Zimmerberg.
// =============================================================================

namespace Olz\Components\Schema\OlzEventData;

use Olz\Components\Common\OlzComponent;

/** @extends OlzComponent<array<string, mixed>> */
class OlzEventData extends OlzComponent {
    public function getHtml(mixed $args): string {
        $name = $args['name'] ?? '';
        $start_date = $args['start_date'] ?? null;
        $end_date = $args['end_date'] ?? null;
        $location = $args['location'] ?? null;
        $place = $location ? [
            '@type' => 'Place',
            'latitude' => $location['lat'],
            'longitude' => $location['lng'],
            'name' => $location['name'] ?? null,
            'address' => 'Unbekannt',
        ] : 'Unbekannt';
        $event_data = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $name,
            'startDate' => $start_date,
            'endDate' => $end_date,
            'location' => $place,
            'eventAttendanceMode' => 'OfflineEventAttendanceMode',
            'eventStatus' => 'EventScheduled',
        ];
        $json_event_data = json_encode($event_data);
        return <<<ZZZZZZZZZZ
            <script type="application/ld+json">
            {$json_event_data}
            </script>
            ZZZZZZZZZZ;
    }
}
