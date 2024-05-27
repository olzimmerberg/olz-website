<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\EmailUtils;

class DeterministicEmailUtils extends EmailUtils {
    public function __construct() {
        $this->setEnvUtils(new FakeEnvUtils());
        $this->setGeneralUtils(new DeterministicGeneralUtils());
    }

    public function renderMarkdown(string $markdown): string {
        return $markdown;
    }

    protected function getRandomEmailVerificationToken(): string {
        return 'veryrandom';
    }
}
