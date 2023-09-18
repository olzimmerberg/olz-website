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
        require_once __DIR__.'/../../../../_/library/wgs84_ch1903/wgs84_ch1903.php';
        if ($latitude !== null && $longitude !== null) {
            $lat = number_format($latitude, 6, '.', '');
            $lng = number_format($longitude, 6, '.', '');
            $xkoord = WGStoCHy($latitude, $longitude);
            $ykoord = WGStoCHx($latitude, $longitude);
        } elseif ($xkoord !== null && $ykoord !== null) {
            $lat = number_format(CHtoWGSlat($xkoord, $ykoord), 6, '.', '');
            $lng = number_format(CHtoWGSlng($xkoord, $ykoord), 6, '.', '');
        } else {
            throw new \Exception("Either xkoord/ykoord or latitude/longitude must be set in OlzLocationMap");
        }

        $random = microtime(true).rand();
        $hash = md5("{$lat}/{$lng}/{$random}");
        $enc_hash = json_encode($hash);
        $enc_name = json_encode($name);
        $enc_lat = json_encode($lat);
        $enc_lng = json_encode($lng);

        $lv95_e = $xkoord + 2000000;
        $lv95_n = $ykoord + 1000000;
        $swisstopo_url = "https://map.geo.admin.ch/?lang=de&bgLayer=ch.swisstopo.pixelkarte-farbe&layers=ch.bav.haltestellen-oev&E={$lv95_e}&N={$lv95_n}&zoom=8&crosshair=marker";
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
                olz.olzLocationMapRender({$enc_hash}, {$enc_name}, {$enc_lat}, {$enc_lng});
            </script>
        </a>
        ZZZZZZZZZZ;
    }
}
