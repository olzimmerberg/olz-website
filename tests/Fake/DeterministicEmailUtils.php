<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\EmailUtils;

class DeterministicEmailUtils extends EmailUtils {
    public $fake_olz_mailer;

    public function __construct() {
        $this->setEnvUtils(new FakeEnvUtils());
        $this->setGeneralUtils(new DeterministicGeneralUtils());
    }

    public function createEmail() {
        return $this->fake_olz_mailer;
    }

    protected function getRandomEmailVerificationToken() {
        return 'veryrandom';
    }
}
