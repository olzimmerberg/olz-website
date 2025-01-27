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
final class ServiceTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw', 'dev', 'staging', 'prod'])]
    public function testServiceReadOnly(): void {
        $browser = $this->getBrowser();

        $browser->get($this->getUrl());
        $this->screenshot('service');

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw', 'dev', 'staging'])]
    public function testServiceAuthenticated(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());
        $this->screenshot('service_authenticated');

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/service";
    }
}
