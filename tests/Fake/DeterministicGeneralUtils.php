<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\GeneralUtils;

class DeterministicGeneralUtils extends GeneralUtils {
    protected function getRandomIvForAlgo($algo) {
        $iv = '';
        for ($i = 0; $i < openssl_cipher_iv_length($algo); $i++) {
            $iv .= 'A';
        }
        return $iv;
    }

    public function measureLatency(callable $fn) {
        $result = $fn();
        $msg = "took 1234ms";
        return [$result, $msg];
    }
}
