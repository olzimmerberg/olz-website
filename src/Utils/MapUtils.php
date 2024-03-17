<?php

namespace Olz\Utils;

// Source: http://www.swisstopo.admin.ch/internet/swisstopo/en/home/topics/survey/sys/refsys/projections.html (see PDFs under "Documentation")
// Updated 9 dec 2014
// Please validate your results with NAVREF on-line service: http://www.swisstopo.admin.ch/internet/swisstopo/en/home/apps/calc/navref.html (difference ~ 1-2m)

class MapUtils {
    use WithUtilsTrait;

    // Convert WGS lat/long (° dec) to CH y
    public function WGStoCHy($lat, $long) {
        // Converts decimal degrees sexagesimal seconds
        $lat = $this->DECtoSEX($lat);
        $long = $this->DECtoSEX($long);

        // Auxiliary values (% Bern)
        $lat_aux = ($lat - 169028.66) / 10000;
        $long_aux = ($long - 26782.5) / 10000;

        // Process Y
        return 600072.37
            + 211455.93 * $long_aux
            - 10938.51 * $long_aux * $lat_aux
            - 0.36 * $long_aux * pow($lat_aux, 2)
            - 44.54 * pow($long_aux, 3);
    }

    // Convert WGS lat/long (° dec) to CH x
    public function WGStoCHx($lat, $long) {
        // Converts decimal degrees sexagesimal seconds
        $lat = $this->DECtoSEX($lat);
        $long = $this->DECtoSEX($long);

        // Auxiliary values (% Bern)
        $lat_aux = ($lat - 169028.66) / 10000;
        $long_aux = ($long - 26782.5) / 10000;

        // Process X
        return 200147.07
            + 308807.95 * $lat_aux
            + 3745.25 * pow($long_aux, 2)
            + 76.63 * pow($lat_aux, 2)
            - 194.56 * pow($long_aux, 2) * $lat_aux
            + 119.79 * pow($lat_aux, 3);
    }

    // Convert CH y/x to WGS lat
    public function CHtoWGSlat($y, $x) {
        // Converts military to civil and  to unit = 1000km
        // Auxiliary values (% Bern)
        $y_aux = ($y - 600000) / 1000000;
        $x_aux = ($x - 200000) / 1000000;

        // Process lat
        $lat = 16.9023892
            + 3.238272 * $x_aux
            - 0.270978 * pow($y_aux, 2)
            - 0.002528 * pow($x_aux, 2)
            - 0.0447 * pow($y_aux, 2) * $x_aux
            - 0.0140 * pow($x_aux, 3);

        // Unit 10000" to 1 " and converts seconds to degrees (dec)
        return $lat * 100 / 36;
    }

    // Convert CH y/x to WGS long
    public function CHtoWGSlng($y, $x) {
        // Converts military to civil and  to unit = 1000km
        // Auxiliary values (% Bern)
        $y_aux = ($y - 600000) / 1000000;
        $x_aux = ($x - 200000) / 1000000;

        // Process long
        $long = 2.6779094
            + 4.728982 * $y_aux
            + 0.791484 * $y_aux * $x_aux
            + 0.1306 * $y_aux * pow($x_aux, 2)
            - 0.0436 * pow($y_aux, 3);

        // Unit 10000" to 1 " and converts seconds to degrees (dec)
        return $long * 100 / 36;
    }

    // Convert DEC angle to SEX DMS
    protected function DECtoSEX($angle) {
        // Extract DMS
        $deg = intval($angle);
        $min = intval(($angle - $deg) * 60);
        $sec = ((($angle - $deg) * 60) - $min) * 60;

        // Result in sexagesimal seconds
        return $sec + $min * 60 + $deg * 3600;
    }
}
