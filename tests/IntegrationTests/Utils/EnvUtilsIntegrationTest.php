<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\EnvUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\EnvUtils
 */
final class EnvUtilsIntegrationTest extends IntegrationTestCase {
    public function testEnvUtilsFromEnv(): void {
        $utils = $this->getSut();
        $this->assertSame(
            realpath(__DIR__.'/../../../private/').'/',
            $utils->getPrivatePath()
        );
        $this->assertMatchesRegularExpression(
            '/\/tests\/IntegrationTests\/document\-root\/$/',
            $utils->getDataPath()
        );
        $this->assertSame('/', $utils->getDataHref());
        $this->assertSame(
            realpath(__DIR__.'/../../..').'/',
            $utils->getCodePath()
        );
        $this->assertSame('/', $utils->getCodeHref());
        $this->assertSame('http://integration-test.host', $utils->getBaseHref());
    }

    protected function getSut(): EnvUtils {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(EnvUtils::class);
    }
}
