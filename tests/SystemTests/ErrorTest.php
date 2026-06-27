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
        $this->loadUrl("{$this->getUrl()}/400");
        $this->screenshot('error_400');

        $this->loadUrl("{$this->getUrl()}/401");
        $this->screenshot('error_401');

        $this->loadUrl("{$this->getUrl()}/403");
        $this->screenshot('error_403');

        $this->loadUrl("{$this->getUrl()}/404");
        $this->screenshot('error_404');

        $this->loadUrl("{$this->getUrl()}/500");
        $this->screenshot('error_500');

        $this->loadUrl("{$this->getUrl()}/529");
        $this->screenshot('error_529');

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/error";
    }
}
