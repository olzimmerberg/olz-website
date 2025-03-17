<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Kernel;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\SymfonyUtils;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Utils\SymfonyUtils
 */
final class SymfonyUtilsIntegrationTest extends IntegrationTestCase {
    public function testSymfonyUtilsFromEnv(): void {
        $utils = $this->getSut();

        $this->assertSame(SymfonyUtils::class, get_class($utils));
    }

    public function testSymfonyUtilsCallCommand(): void {
        global $kernel, $_SERVER;
        $kernel = new Kernel('dev', true);

        $utils = $this->getSut();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $utils->callCommand('olz:test', $input, $output);

        $this->assertMatchesRegularExpression(
            '/Data path\: .*\/IntegrationTests\/document-root\//',
            $output->fetch()
        );
    }

    protected function getSut(): SymfonyUtils {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(SymfonyUtils::class);
    }
}
