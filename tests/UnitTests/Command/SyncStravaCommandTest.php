<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\SyncStravaCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\SyncStravaCommand
 */
final class SyncStravaCommandTest extends UnitTestCase {
    public function testSyncStravaCommand(): void {
        $input = new ArrayInput(['year' => '2020']);
        $output = new BufferedOutput();

        $job = new SyncStravaCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\SyncStravaCommand...',
            'INFO Syncing Strava for 2020...',
            'INFO Syncing StravaLink (ID: 12)...',
            'DEBUG Strava token refresh...',
            'DEBUG Strava token refresh response:',
            'DEBUG Strava token refreshed',
            'DEBUG Fetched 0 activities...',
            'INFO Syncing StravaLink (ID: 123)...',
            'DEBUG Strava token refresh...',
            'DEBUG Strava token refresh response:',
            'DEBUG Strava token refreshed',
            'DEBUG Fetched 0 activities...',
            'INFO Syncing StravaLink (ID: 1234)...',
            'DEBUG Strava token refresh...',
            'DEBUG Strava token refresh response:',
            'DEBUG Strava token refreshed',
            'DEBUG Fetched 0 activities...',
            'INFO Successfully ran command Olz\Command\SyncStravaCommand.',
        ], $this->getLogs());

        $this->assertSame([], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }
}
