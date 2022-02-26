<?php

// =============================================================================
// Strukturierte Daten (siehe https://schema.org) zur OL Zimmerberg.
// =============================================================================

function olz_event_data($args = []): string {
    $name = $args['name'] ?? '';
    $start_date = $args['start_date'] ?? null;
    $end_date = $args['end_date'] ?? null;
    $location = $args['location'] ?? null;
    $place = $location ? [
        'latitude' => $location['lat'],
        'longitude' => $location['lng'],
    ] : null;
    $event_data = [
        '@context' => 'https://schema.org',
        '@type' => 'Event',
        'name' => $name,
        'startDate' => $start_date,
        'endDate' => $end_date,
        'location' => $place,
    ];
    $json_event_data = json_encode($event_data);
    return <<<ZZZZZZZZZZ
    <script type="application/ld+json">
    {$json_event_data}
    </script>
    ZZZZZZZZZZ;
}
