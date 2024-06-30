<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Utils\ImageUtils;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:clean-data')]
class CleanDataCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $data_path = $this->envUtils()->getDataPath();
        $paths = array_values(ImageUtils::TABLES_IMG_DIRS);
        sort($paths);
        foreach ($paths as $path) {
            $entities_path = "{$data_path}{$path}";
            if (!is_dir($entities_path)) {
                $this->logAndOutput("Creating directory {$entities_path}...");
                $this->mkdir($entities_path, 0o777, true);
            }
            $handle = $this->opendir($entities_path);
            if (!$handle) {
                $this->logAndOutput("Failed to open directory {$entities_path}");
                return Command::FAILURE;
            }
            while (false !== ($entry = $this->readdir($handle))) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                if (strval(intval($entry)) !== $entry) {
                    $this->logAndOutput("Invalid entity ID: {$entry}");
                    continue;
                }
                $entity_img_path = "{$entities_path}{$entry}/";
                $this->cleanThumbDir($entity_img_path);
            }
            $this->closedir($handle);
        }
        return Command::SUCCESS;
    }

    private function cleanThumbDir(string $entity_img_path): void {
        $thumb_dir = "{$entity_img_path}thumb/";
        if (!is_dir($thumb_dir)) {
            $this->logAndOutput("Creating directory {$thumb_dir}...");
            $this->mkdir($thumb_dir, 0o777, true);
        }
        $handle = $this->opendir($thumb_dir);
        if (!$handle) {
            $this->logAndOutput("Failed to open directory {$thumb_dir}");
            return;
        }
        while (false !== ($entry = $this->readdir($handle))) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $is_valid_thumb = preg_match('/^[a-zA-Z0-9_-]{24}\.jpg\$(32|64|128|256|512)\.jpg$/', $entry);
            if (!$is_valid_thumb) {
                $entry_path = "{$thumb_dir}{$entry}";
                $this->logAndOutput("Invalid thumb: {$entry_path}");
                $this->unlink($entry_path);
            }
        }
        $this->closedir($handle);
    }

    /** @return bool|resource */
    protected function opendir(string $path): mixed {
        return @opendir($path);
    }

    /** @param resource|null $handle */
    protected function readdir(mixed $handle): bool|string {
        return @readdir($handle);
    }

    /** @param resource|null $handle */
    protected function closedir(mixed $handle): void {
        @closedir($handle);
    }

    // @codeCoverageIgnoreStart
    // Reason: Mocked in tests.

    protected function mkdir(string $directory, int $permissions, bool $recursive): void {
        mkdir($directory, $permissions, $recursive);
    }

    protected function unlink(string $path): void {
        unlink($path);
    }

    // @codeCoverageIgnoreEnd
}
