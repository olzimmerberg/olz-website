<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\SyncSolvCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\SyncSolvCommand
 */
final class SyncSolvCommandTest extends UnitTestCase {
    public function testSyncSolvCommand(): void {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new SyncSolvCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\SyncSolvCommand...',
            'INFO Successfully ran command Olz\Command\SyncSolvCommand.',
        ], $this->getLogs());

        $this->assertSame([
            'olz:sync-solv-events 2020',
            'olz:sync-solv-results 2020',
            'olz:sync-solv-assign-people ',
            'olz:sync-solv-merge-people ',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }

    public function testSyncSolvCommandFirstOfMonth(): void {
        $date_utils = new FixedDateUtils('2020-04-01 19:30:00');
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new SyncSolvCommand();
        $job->setDateUtils($date_utils);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\SyncSolvCommand...',
            'INFO Successfully ran command Olz\Command\SyncSolvCommand.',
        ], $this->getLogs());

        $this->assertSame([
            'olz:sync-solv-events 2020',
            'olz:sync-solv-events 2019',
            'olz:sync-solv-events 2021',
            'olz:sync-solv-events 2018',
            'olz:sync-solv-results 2020',
            'olz:sync-solv-assign-people ',
            'olz:sync-solv-merge-people ',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }
}
