<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

class FakeTask {
    public $hasBeenRun = false;

    public function run() {
        $this->hasBeenRun = true;
    }
}
