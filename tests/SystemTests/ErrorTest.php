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
final class ErrorTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw', 'dev', 'staging', 'prod'])]
    public function testErrorReadOnly(): void {
        $browser = $this->getBrowser();

        $browser->get("{$this->getUrl()}/400");
        $this->screenshot('error_400');

        $browser->get("{$this->getUrl()}/401");
        $this->screenshot('error_401');

        $browser->get("{$this->getUrl()}/403");
        $this->screenshot('error_403');

        $browser->get("{$this->getUrl()}/404");
        $this->screenshot('error_404');

        $browser->get("{$this->getUrl()}/500");
        $this->screenshot('error_500');

        $browser->get("{$this->getUrl()}/529");
        $this->screenshot('error_529');

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/error";
    }
}
