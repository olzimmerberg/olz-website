<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:clean-temp-directory')]
class CleanTempDirectoryCommand extends OlzCommand {
    protected $temp_realpath;
    protected $clean_older_than;

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

    private function recursiveCleanDirectory($directory) {
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

    private function shouldEntryBeRemoved($entry_path) {
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

    protected function opendir($path) {
        return opendir($path);
    }

    protected function readdir($handle) {
        return readdir($handle);
    }

    protected function closedir($handle) {
        return closedir($handle);
    }

    protected function filemtime($path) {
        // @codeCoverageIgnoreStart
        // Reason: Mocked in tests.
        return filemtime($path);
        // @codeCoverageIgnoreEnd
    }

    protected function filectime($path) {
        // @codeCoverageIgnoreStart
        // Reason: Mocked in tests.
        return filectime($path);
        // @codeCoverageIgnoreEnd
    }

    // @codeCoverageIgnoreStart
    // Reason: Mocked in tests.

    protected function rmdir($path) {
        rmdir($path);
    }

    protected function unlink($path) {
        unlink($path);
    }

    // @codeCoverageIgnoreEnd
}
