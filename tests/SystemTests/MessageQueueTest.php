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
final class MessageQueueTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'dev_rw'])]
    public function testMessageQueueOnDev(): void {
        $result = $this->runCommand('olz:test', null);
        $this->assertStringNotContainsString('    test_message: 2020-08-15 12:51:00', $result);
        try {
            // This will fail with HTTP 500, but still consume the test message
            $result = $this->runCommand('messenger:consume', '--time-limit=1');
        } catch (\Throwable $th) {
            // ignore
        }
        $result = $this->runCommand('olz:test', null);
        $this->assertStringContainsString('    test_message: 2020-08-15 12:51:00', $result);
    }

    #[OnlyInModes(['prod'])]
    public function testMessageQueueOnProd(): void {
        $result = $this->runCommand('olz:test', null);
        // TODO: Implement
        $this->assertSame('', $result);
    }
}
