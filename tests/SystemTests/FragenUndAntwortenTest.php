<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class FragenUndAntwortenTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw', 'dev', 'staging', 'prod'])]
    public function testFragenUndAntwortenReadOnly(): void {
        $browser = $this->getBrowser();

        $browser->get($this->getUrl());
        $this->screenshot('fragen_und_antworten');

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/fragen_und_antworten";
    }
}
