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
        $this->assertStringNotContainsString('    test_message: 2020-08-15 16:51:00', $result);
        try {
            // This will fail with HTTP 500, but still consume the test message
            $result = $this->runCommand('messenger:consume', '--time-limit=1');
        } catch (\Throwable $th) {
            // ignore
        }
        $result = $this->runCommand('olz:test', null);
        $this->assertStringContainsString('    test_message: 2020-08-15 16:51:00', $result);
    }

    #[OnlyInModes(['prod'])]
    public function testIsMessageQueueRunningOnProd(): void {
        $result = $this->runCommand('olz:test', null);

        $last_on_continuously = $this->getThrottlingDateTime($result, 'on_continuously');
        $this->assertNotNull($last_on_continuously);
        $this->assertGreaterThan(new \DateTime('5 minutes 10 seconds ago'), $last_on_continuously);
        $this->assertLessThanOrEqual(new \DateTime('now'), $last_on_continuously);

        $last_on_daily = $this->getThrottlingDateTime($result, 'on_daily');
        $this->assertNotNull($last_on_daily);
        $this->assertGreaterThan(new \DateTime('1 day 2 hours ago'), $last_on_daily);
        $this->assertLessThanOrEqual(new \DateTime('now'), $last_on_daily);

        sleep(3); // give some time for the queue to process the message
        $result = $this->runCommand('olz:test', null);

        $last_test_message = $this->getThrottlingDateTime($result, 'test_message');
        $this->assertNotNull($last_test_message);
        $this->assertGreaterThan(new \DateTime('10 seconds ago'), $last_test_message);
        $this->assertLessThanOrEqual(new \DateTime('now'), $last_test_message);
    }

    protected function getThrottlingDateTime(
        string $test_command_output,
        string $throttling_ident,
    ): ?\DateTime {
        $esc_ident = preg_quote($throttling_ident);
        $pattern = "/\\s+{$esc_ident}\\: (\\d{4}-\\d{2}\\-\\d{2} \\d{2}\\:\\d{2}\\:\\d{2})\\s+/m";
        $has_match = preg_match($pattern, $test_command_output, $matches);
        if (!$has_match) {
            return null;
        }
        $time = strtotime($matches[1]) ?: null;
        return is_int($time) ? new \DateTime(date('Y-m-d H:i:s', $time)) : null;
    }
}
