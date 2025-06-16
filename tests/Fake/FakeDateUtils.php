<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\DateUtils;

class FakeDateUtils extends DateUtils {
    public function __construct() {
        parent::__construct('2020-03-13 19:30:00');
    }

    public function testOnlySetDate(?string $date): void {
        $this->date = $date;
    }
}
