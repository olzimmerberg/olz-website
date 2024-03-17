<?php

namespace Olz\Components\Common\OlzLocationMap;

use Olz\Components\Common\OlzComponent;

class OlzLocationMap extends OlzComponent {
    public function getHtml($args = []): string {
        $xkoord = $args['xkoord'] ?? null;
        $ykoord = $args['ykoord'] ?? null;
        $latitude = $args['latitude'] ?? null;
        $longitude = $args['longitude'] ?? null;
        $zoom = $args['zoom'] ?? 13;
        $name = $args['name'] ?? '';

        $lat = null;
        $lng = null;
        if ($latitude !== null && $longitude !== null) {
            $lat = number_format($latitude, 6, '.', '');
            $lng = number_format($longitude, 6, '.', '');
            $xkoord = $this->mapUtils()->WGStoCHy($latitude, $longitude);
            $ykoord = $this->mapUtils()->WGStoCHx($latitude, $longitude);
        } elseif ($xkoord !== null && $ykoord !== null) {
            $lat = number_format($this->mapUtils()->CHtoWGSlat($xkoord, $ykoord), 6, '.', '');
            $lng = number_format($this->mapUtils()->CHtoWGSlng($xkoord, $ykoord), 6, '.', '');
        } else {
            throw new \Exception("Either xkoord/ykoord or latitude/longitude must be set in OlzLocationMap");
        }

        $random = microtime(true).rand();
        $hash = md5("{$lat}/{$lng}/{$random}");
        $enc_hash = json_encode($hash);
        $enc_name = json_encode($name);
        $enc_lat = json_encode($lat);
        $enc_lng = json_encode($lng);
        $enc_zoom = json_encode($zoom);

        $lv95_e = $xkoord + 2000000;
        $lv95_n = $ykoord + 1000000;
        $zoom_swisstopo = $zoom - 5;
        $swisstopo_url = "https://map.geo.admin.ch/?lang=de&bgLayer=ch.swisstopo.pixelkarte-farbe&layers=ch.bav.haltestellen-oev&E={$lv95_e}&N={$lv95_n}&zoom={$zoom_swisstopo}&crosshair=marker";
        return <<<ZZZZZZZZZZ
        <a
            href='{$swisstopo_url}'
            target='_blank'
            class='olz-location-map-link'
        >
            <div
                id='olz-location-map-render-{$hash}'
                class='olz-location-map-render test-flaky'
            >
            </div>
            <script>
                olz.olzLocationMapRender({$enc_hash}, {$enc_name}, {$enc_lat}, {$enc_lng}, {$enc_zoom});
            </script>
        </a>
        ZZZZZZZZZZ;
    }
}
