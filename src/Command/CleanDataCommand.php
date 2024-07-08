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
            $this->forEachDirectoryEntry(
                $entities_path,
                function ($entry) use ($entities_path) {
                    if (strval(intval($entry)) !== $entry) {
                        $this->logAndOutput("Invalid entity ID: {$entry}");
                        return;
                    }
                    $entity_img_path = "{$entities_path}{$entry}/";
                    $this->cleanThumbDir($entity_img_path);
                }
            );
        }
        return Command::SUCCESS;
    }

    private function cleanThumbDir(string $entity_img_path): void {
        $thumb_dir = "{$entity_img_path}thumb/";
        try {
            $this->forEachDirectoryEntry(
                $thumb_dir,
                function ($entry) use ($thumb_dir) {
                    $is_valid_thumb = preg_match('/^[a-zA-Z0-9_-]{24}\.jpg\$(32|64|128|256|512)\.jpg$/', $entry);
                    if (!$is_valid_thumb) {
                        $entry_path = "{$thumb_dir}{$entry}";
                        $this->logAndOutput("Invalid thumb: {$entry_path}");
                        $this->unlink($entry_path);
                    }
                }
            );
        } catch (\Throwable $th) {
            $this->logAndOutput("Error validating thumbs: {$th->getMessage()}");
        }

        $img_dir = "{$entity_img_path}img/";
        $image_ids = [];
        $this->forEachDirectoryEntry(
            $img_dir,
            function ($entry) use ($img_dir, &$image_ids) {
                $is_valid_img = preg_match('/^[a-zA-Z0-9_-]{24}\.jpg$/', $entry);
                if ($is_valid_img) {
                    $image_ids[] = $entry;
                } else {
                    $entry_path = "{$img_dir}{$entry}";
                    $this->logAndOutput("Invalid img: {$entry_path}");
                }
            }
        );
        try {
            $this->imageUtils()->generateThumbnails($image_ids, $entity_img_path);
        } catch (\Throwable $th) {
            $this->logAndOutput("Error generating thumbnails: {$th->getMessage()}");
        }
    }

    protected function forEachDirectoryEntry(string $directory, callable $callback): void {
        if (!is_dir($directory)) {
            $this->logAndOutput("Creating directory {$directory}...");
            $this->mkdir($directory, 0o777, true);
        }
        $handle = $this->opendir($directory);
        if (!$handle) {
            throw new \Exception("Failed to open directory {$directory}");
        }
        while (false !== ($entry = $this->readdir($handle))) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $callback($entry);
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
