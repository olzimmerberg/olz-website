<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\CleanThumbsCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class FakeCleanThumbsCommand extends CleanThumbsCommand {
    /** @var bool|resource|null */
    public mixed $opendir_override_result = null;

    public ?int $filemtime_response = null;

    /** @var array<string> */
    public array $rmdir_calls = [];
    /** @var array<string> */
    public array $unlink_calls = [];

    /** @return bool|resource */
    protected function opendir(string $path): mixed {
        if ($this->opendir_override_result !== null) {
            return $this->opendir_override_result;
        }
        return parent::opendir($path);
    }

    protected function filemtime(string $path): bool|int {
        if ($this->filemtime_response !== null) {
            return $this->filemtime_response;
        }
        return 0;
    }

    protected function filectime(string $path): bool|int {
        return 0;
    }

    protected function rmdir(string $path): void {
        $this->rmdir_calls[] = $path;
    }

    protected function unlink(string $path): void {
        $this->unlink_calls[] = $path;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\CleanThumbsCommand
 */
final class CleanThumbsCommandTest extends UnitTestCase {
    public function testCleanThumbsCommandCleansUp(): void {
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
        file_put_contents("{$data_path}img/news/121/thumb/abcdefghijklmnopqrstuvwx.jpg$128.jpg", "test");
        file_put_contents("{$data_path}img/news/121/thumb/abcdefghijklmnopqrstuvwx.jpg_128.jpg", "test");
        file_put_contents("{$data_path}img/news/121/thumb/abcdefghijklmnopqrstuvwx.jpg_128x96.jpg", "test");
        file_put_contents("{$data_path}img/news/121/thumb/abcdefghijklmnopqrstuv-_.jpg$128.jpg", "test");
        mkdir("{$data_path}img/roles/", 0o777, true);
        mkdir("{$data_path}img/snippets/", 0o777, true);
        mkdir("{$data_path}img/termine/", 0o777, true);
        mkdir("{$data_path}img/termin_labels/", 0o777, true);
        mkdir("{$data_path}img/termin_locations/", 0o777, true);
        mkdir("{$data_path}img/termin_templates/", 0o777, true);
        mkdir("{$data_path}img/weekly_picture/", 0o777, true);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new FakeCleanThumbsCommand();
        $job->run($input, $output);

        $this->assertEqualsCanonicalizing([
        ], $job->rmdir_calls);
        $this->assertEqualsCanonicalizing([
        ], $job->unlink_calls);
        $this->assertEqualsCanonicalizing([
            'INFO Running command Olz\Tests\UnitTests\Command\FakeCleanThumbsCommand...',
            'INFO No such directory data-path/img/news/10/thumb/',
            'INFO Invalid thumb: data-path/img/news/121/thumb/abcdefghijklmnopqrstuvwx.jpg_128.jpg',
            'INFO Invalid thumb: data-path/img/news/121/thumb/abcdefghijklmnopqrstuvwx.jpg_128x96.jpg',
            'INFO Successfully ran command Olz\Tests\UnitTests\Command\FakeCleanThumbsCommand.',
        ], $this->getLogs());
    }
}
