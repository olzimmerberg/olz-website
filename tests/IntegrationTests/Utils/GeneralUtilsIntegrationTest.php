<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\GeneralUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\GeneralUtils
 */
final class GeneralUtilsIntegrationTest extends IntegrationTestCase {
    public function testGeneralUtilsFromEnv(): void {
        $general_utils = GeneralUtils::fromEnv();

        $this->assertTrue($general_utils instanceof GeneralUtils);
    }
}
