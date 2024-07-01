<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\CleanDataCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class FakeCleanDataCommand extends CleanDataCommand {
    /** @var bool|resource|null */
    public mixed $opendir_override_result = null;

    /** @var array<array{0: string, 1: int, 2: bool}> */
    public array $mkdir_calls = [];
    /** @var array<string> */
    public array $unlink_calls = [];

    /** @return bool|resource */
    protected function opendir(string $path): mixed {
        if ($this->opendir_override_result !== null) {
            return $this->opendir_override_result;
        }
        return parent::opendir($path);
    }

    protected function mkdir(string $directory, int $permissions, bool $recursive): void {
        $this->mkdir_calls[] = [$directory, $permissions, $recursive];
    }

    protected function unlink(string $path): void {
        $this->unlink_calls[] = $path;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\CleanDataCommand
 */
final class CleanDataCommandTest extends UnitTestCase {
    public function testCleanDataCommandCleansUp(): void {
        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        mkdir("{$data_path}img/karten/", 0o777, true);
        mkdir("{$data_path}img/news/10/img/", 0o777, true);
        mkdir("{$data_path}img/news/11/img/", 0o777, true);
        mkdir("{$data_path}img/news/11/thumb/", 0o777, true);
        mkdir("{$data_path}img/news/12/img/", 0o777, true);
        mkdir("{$data_path}img/news/12/thumb/", 0o777, true);
        file_put_contents("{$data_path}img/news/12/thumb/abcdefghijklmnopqrstuvwx.jpg$128.jpg", "test");
        mkdir("{$data_path}img/news/121/img/", 0o777, true);
        mkdir("{$data_path}img/news/121/thumb/", 0o777, true);
        file_put_contents("{$data_path}img/news/121/img/abcdefghijklmnopqrstuvwx.jpg", "test");
        file_put_contents("{$data_path}img/news/121/img/invalid.jpg", "test");
        file_put_contents("{$data_path}img/news/121/thumb/abcdefghijklmnopqrstuvwx.jpg$128.jpg", "test");
        file_put_contents("{$data_path}img/news/121/thumb/abcdefghijklmnopqrstuvwx.jpg_128.jpg", "test");
        file_put_contents("{$data_path}img/news/121/thumb/abcdefghijklmnopqrstuvwx.jpg_128x96.jpg", "test");
        file_put_contents("{$data_path}img/news/121/thumb/abcdefghijklmnopqrstuv-_.jpg$128.jpg", "test");
        mkdir("{$data_path}img/news/invalid/", 0o777, true);
        mkdir("{$data_path}img/roles/", 0o777, true);
        mkdir("{$data_path}img/snippets/", 0o777, true);
        mkdir("{$data_path}img/termine/", 0o777, true);
        mkdir("{$data_path}img/termin_labels/", 0o777, true);
        mkdir("{$data_path}img/termin_locations/", 0o777, true);
        mkdir("{$data_path}img/termin_templates/", 0o777, true);
        mkdir("{$data_path}img/weekly_picture/", 0o777, true);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new FakeCleanDataCommand();
        $result = $job->run($input, $output);

        $this->assertEqualsCanonicalizing([
            'INFO Running command Olz\Tests\UnitTests\Command\FakeCleanDataCommand...',
            'INFO Creating directory data-path/img/news/10/thumb/...',
            'INFO Error validating thumbs: Failed to open directory data-path/img/news/10/thumb/',
            'INFO Invalid entity ID: invalid',
            'INFO Invalid thumb: data-path/img/news/121/thumb/abcdefghijklmnopqrstuvwx.jpg_128.jpg',
            'INFO Invalid thumb: data-path/img/news/121/thumb/abcdefghijklmnopqrstuvwx.jpg_128x96.jpg',
            'INFO Invalid img: data-path/img/news/121/img/invalid.jpg',
            'INFO Successfully ran command Olz\Tests\UnitTests\Command\FakeCleanDataCommand.',
        ], $this->getLogs());
        $this->assertEqualsCanonicalizing([
            ["{$data_path}img/news/10/thumb/", 0o777, true],
        ], $job->mkdir_calls);
        $this->assertEqualsCanonicalizing([
            "{$data_path}img/news/121/thumb/abcdefghijklmnopqrstuvwx.jpg_128.jpg",
            "{$data_path}img/news/121/thumb/abcdefghijklmnopqrstuvwx.jpg_128x96.jpg",
        ], $job->unlink_calls);
        $this->assertEqualsCanonicalizing([
            [[], realpath(__DIR__.'/../../')."/Fake/../UnitTests/tmp/img/news/10/"],
            [[], realpath(__DIR__.'/../../')."/Fake/../UnitTests/tmp/img/news/11/"],
            [[], realpath(__DIR__.'/../../')."/Fake/../UnitTests/tmp/img/news/12/"],
            [
                ['abcdefghijklmnopqrstuvwx.jpg'],
                realpath(__DIR__.'/../../')."/Fake/../UnitTests/tmp/img/news/121/",
            ],
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
        $this->assertSame(Command::SUCCESS, $result);
    }

    public function testCleanDataCommandMissingEntityDir(): void {
        $data_path = WithUtilsCache::get('envUtils')->getDataPath();

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new FakeCleanDataCommand();
        $result = $job->run($input, $output);

        $this->assertEqualsCanonicalizing([
            'INFO Running command Olz\Tests\UnitTests\Command\FakeCleanDataCommand...',
            'INFO Creating directory data-path/img/karten/...',
            'ERROR Error running command Olz\Tests\UnitTests\Command\FakeCleanDataCommand: Failed to open directory data-path/img/karten/.',
        ], $this->getLogs());
        $this->assertEqualsCanonicalizing([
            ["{$data_path}img/karten/", 0o777, true],
        ], $job->mkdir_calls);
        $this->assertEqualsCanonicalizing([], $job->unlink_calls);
        $this->assertEqualsCanonicalizing([], WithUtilsCache::get('imageUtils')->generatedThumbnails);
        $this->assertSame(Command::FAILURE, $result);
    }
}
