<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\SymfonyUtils;
use Olz\Kernel;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\SymfonyUtils
 */
final class SymfonyUtilsTest extends UnitTestCase {
    public function testSymfonyUtilsGetApplicationWithoutKernel(): void {
        $symfony_utils = new SymfonyUtils();

        $application = $symfony_utils->getApplication();

        $this->assertNull($application);
    }

    public function testSymfonyUtilsGetApplicationWithKernel(): void {
        global $kernel;
        $kernel = new Kernel('dev', true);
        $symfony_utils = new SymfonyUtils();

        $application = $symfony_utils->getApplication();

        $this->assertTrue($application instanceof Application);
    }
}
