<?php

// =============================================================================
// Strukturierte Daten (siehe https://schema.org) zur OL Zimmerberg.
// =============================================================================

function olz_organization_data($args = []): string {
    return <<<'ZZZZZZZZZZ'
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "url": "https://olzimmerberg.ch",
        "logo": "https://olzimmerberg.ch/_/icns/olz_logo_mit_hintergrund.svg",
        "name": "OL Zimmerberg",
        "description": "Die OL Zimmerberg (Orientierungsläufer*innen Zimmerberg) sind ein Orientierungslauf-Sportverein in der Region um den Zimmerberg am linken Zürichseeufer und im Sihltal. Unsere Mitglieder kommen aus Kilchberg, Rüschlikon, Thalwil, Oberrieden, Horgen, Au ZH, Wädenswil, Richterswil, Schönenberg, Hirzel, Langnau am Albis, Gattikon, Adliswil und nahe gelegenen Teilen der Stadt Zürich (Wollishofen, Enge, Leimbach, Friesenberg).",
        "foundingDate": "2006-01-13",
        "sameAs": [
            "https://www.youtube.com/channel/UCMhMdPRJOqdXHlmB9kEpmXQ",
            "https://www.strava.com/clubs/ol-zimmerberg-158910",
            "https://www.facebook.com/olzimmerberg/",
            "https://www.instagram.com/olzimmerberg/",
            "https://github.com/olzimmerberg"
        ]
    }
    </script>
    ZZZZZZZZZZ;
}
