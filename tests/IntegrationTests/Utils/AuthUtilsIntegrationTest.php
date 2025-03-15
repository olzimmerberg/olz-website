<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\AuthUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\AuthUtils
 */
final class AuthUtilsIntegrationTest extends IntegrationTestCase {
    public function testAuthUtilsFromEnv(): void {
        $utils = $this->getSut();

        $this->assertSame(AuthUtils::class, get_class($utils));
    }

    protected function getSut(): AuthUtils {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(AuthUtils::class);
    }
}
