<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\EmailUtils;

class DeterministicEmailUtils extends EmailUtils {
    public function __construct() {
        $this->setEnvUtils(new FakeEnvUtils());
        $this->setGeneralUtils(new DeterministicGeneralUtils());
    }

    public function renderMarkdown($markdown) {
        return $markdown;
    }

    protected function getRandomEmailVerificationToken() {
        return 'veryrandom';
    }
}
