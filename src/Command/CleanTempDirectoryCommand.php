<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:clean-temp-directory')]
class CleanTempDirectoryCommand extends OlzCommand {
    protected string $temp_realpath;
    protected int $clean_older_than;

    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $data_path = $this->envUtils()->getDataPath();
        $temp_path = "{$data_path}temp";
        $this->temp_realpath = realpath($temp_path);
        $now = strtotime($this->dateUtils()->getCurrentDateInFormat('Y-m-d H:i:s'));
        $cleaning_delay = 86400 * 2;
        $this->clean_older_than = $now - $cleaning_delay;

        $this->recursiveCleanDirectory($temp_path);

        return Command::SUCCESS;
    }

    private function recursiveCleanDirectory(string $directory): void {
        $handle = $this->opendir($directory);
        if (!$handle) {
            $this->log()->warning("Failed to open directory {$directory}");
            return;
        }
        while (false !== ($entry = $this->readdir($handle))) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $entry_path = "{$directory}/{$entry}";
            if (!$this->shouldEntryBeRemoved($entry_path)) {
                continue;
            }
            $entry_realpath = realpath($entry_path);
            if (is_dir($entry_path)) {
                $this->recursiveCleanDirectory($entry_path);
                $this->rmdir($entry_realpath); // Remove directory if it's empty
            } elseif (is_file($entry_path)) {
                $this->unlink($entry_realpath);
            }
        }
        $this->closedir($handle);
    }

    private function shouldEntryBeRemoved(string $entry_path): bool {
        $last_modification_date = max([
            $this->filemtime($entry_path),
            $this->filectime($entry_path),
        ]);
        if ($last_modification_date >= $this->clean_older_than) {
            return false;
        }
        $entry_realpath = realpath($entry_path);
        // Double check we're not doing something stupid!
        if (substr($entry_realpath, 0, strlen($this->temp_realpath)) !== $this->temp_realpath) {
            // @codeCoverageIgnoreStart
            // Reason: Should never happen in reality.
            return false;
            // @codeCoverageIgnoreEnd
        }
        return true;
    }

    /** @return bool|resource */
    protected function opendir(string $path): mixed {
        return opendir($path);
    }

    /** @param resource|null $handle */
    protected function readdir(mixed $handle): bool|string {
        return readdir($handle);
    }

    /** @param resource|null $handle */
    protected function closedir(mixed $handle): void {
        closedir($handle);
    }

    protected function filemtime(string $path): bool|int {
        // @codeCoverageIgnoreStart
        // Reason: Mocked in tests.
        return filemtime($path);
        // @codeCoverageIgnoreEnd
    }

    protected function filectime(string $path): bool|int {
        // @codeCoverageIgnoreStart
        // Reason: Mocked in tests.
        return filectime($path);
        // @codeCoverageIgnoreEnd
    }

    // @codeCoverageIgnoreStart
    // Reason: Mocked in tests.

    protected function rmdir(string $path): void {
        rmdir($path);
    }

    protected function unlink(string $path): void {
        unlink($path);
    }

    // @codeCoverageIgnoreEnd
}
