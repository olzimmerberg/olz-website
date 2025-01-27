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
final class SanityCheckTest extends SystemTestCase {
    #[OnlyInModes(['meta'])]
    public function testModeMeta(): void {
        $this->assertSame('meta', getenv('SYSTEM_TEST_MODE'));
    }

    #[OnlyInModes(['prod'])]
    public function testModeProd(): void {
        $this->assertSame('prod', getenv('SYSTEM_TEST_MODE'));
    }

    #[OnlyInModes(['staging_rw'])]
    public function testModeStagingRw(): void {
        $this->assertSame('staging_rw', getenv('SYSTEM_TEST_MODE'));
    }

    #[OnlyInModes(['staging'])]
    public function testModeStaging(): void {
        $this->assertSame('staging', getenv('SYSTEM_TEST_MODE'));
    }

    #[OnlyInModes(['dev_rw'])]
    public function testModeDevRw(): void {
        $this->assertSame('dev_rw', getenv('SYSTEM_TEST_MODE'));
    }

    #[OnlyInModes(['dev'])]
    public function testModeDev(): void {
        $this->assertSame('dev', getenv('SYSTEM_TEST_MODE'));
    }
}
