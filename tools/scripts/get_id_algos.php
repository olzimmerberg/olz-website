<?php

use Olz\Utils\IdUtils;

require_once __DIR__.'/../../vendor/autoload.php';

class IdUtilsMutableAlgo extends IdUtils {
    public function setAlgo($new_algo) {
        $this->algo = $new_algo;
    }
}

$algos = openssl_get_cipher_methods();
$online_algos_json = file_get_contents('https://olzimmerberg.ch/tools.php/get-id-algos');
$online_algos = json_decode(explode("\n", $online_algos_json)[0], true);
$is_online_algo = [];
foreach ($online_algos as $online_algo) {
    $is_online_algo[$online_algo] = true;
}

$max_id = pow(2, 40) - 1;

foreach ($algos as $algo) {
    try {
        if (!($is_online_algo[$algo] ?? false)) {
            throw new \Exception("Must exist online.");
        }
        $id_utils = new IdUtilsMutableAlgo();
        $id_utils->setAlgo($algo);

        $external_id = $id_utils->toExternalId($max_id, 'Test');

        if (strlen($external_id) > 11) {
            throw new \Exception("Too long.");
        }

        $levenshtein3 = 0;
        $levenshtein4 = 0;
        $levenshtein5 = 0;

        for ($i = 0; $i < 100; $i++) {
            $id = rand(2, $max_id);
            $text = base64_encode(openssl_random_pseudo_bytes(4));
            $external_id1 = $id_utils->toExternalId($id, $text);
            $external_id2 = $id_utils->toExternalId($id, $text);
            $external_id3 = $id_utils->toExternalId($id, substr($text, 1));
            $external_id4 = $id_utils->toExternalId($id, substr($text, 0, strlen($text) - 1));
            $external_id5 = $id_utils->toExternalId($id - 1, $text);

            if ($external_id1 !== $external_id2) {
                throw new \Exception("Must be deterministic");
            }

            $levenshtein3 += levenshtein($external_id1, $external_id3);
            $levenshtein4 += levenshtein($external_id1, $external_id4);
            $levenshtein5 += levenshtein($external_id1, $external_id5);
        }

        $levenshtein3 = $levenshtein3 / strlen($external_id) / 100;
        $levenshtein4 = $levenshtein4 / strlen($external_id) / 100;
        $levenshtein5 = $levenshtein5 / strlen($external_id) / 100;

        if ($levenshtein3 < 0.9 || $levenshtein4 < 0.9 || $levenshtein5 < 0.9) {
            throw new \Exception("Not good");
        }

        echo "{$algo} External ID: {$external_id1} {$external_id2} {$external_id3} {$external_id4} {$external_id5}\n";
    } catch (\Exception $exc) {
        // ignore
    }
}
