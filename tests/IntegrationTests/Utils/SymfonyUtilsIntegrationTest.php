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
        $symfony_utils = SymfonyUtils::fromEnv();

        $this->assertSame(false, !$symfony_utils);
    }

    public function testSymfonyUtilsCallCommand(): void {
        global $kernel, $_SERVER;
        $kernel = new Kernel('dev', true);

        $symfony_utils = new SymfonyUtils();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $symfony_utils->callCommand('olz:test', $input, $output);

        $this->assertMatchesRegularExpression(
            '/^Data path\: .*\/IntegrationTests\/document-root\//',
            $output->fetch()
        );
    }
}
