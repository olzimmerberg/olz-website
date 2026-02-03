<?php

namespace Olz\Apps\Logs\Utils;

use Olz\Utils\WithUtilsTrait;

class HybridLogFile implements LogFileInterface {
    use WithUtilsTrait;

    protected ?LogFileInterface $plainLogFile;

    public function __construct(
        public string $gzPath,
        public string $plainPath,
        public string $indexPath,
    ) {
    }

    public function getPath(): string {
        return $this->gzPath;
    }

    public function getIndexPath(): string {
        return $this->indexPath;
    }

    public function exists(): bool {
        return is_file($this->gzPath) || is_file($this->plainPath);
    }

    public function modified(): int {
        $result = match (true) {
            is_file($this->gzPath) => filemtime($this->gzPath),
            is_file($this->plainPath) => filemtime($this->plainPath),
            default => false,
        };
        $this->generalUtils()->checkNotBool($result, "filemtime({$this->gzPath}) failed");
        return $result;
    }

    /** @return resource */
    public function open(string $mode): mixed {
        if (!is_file($this->plainPath)) {
            $this->copyToPlain();
        }
        $this->plainLogFile = new PlainLogFile($this->plainPath, $this->indexPath);
        $this->generalUtils()->checkNotNull($this->plainLogFile, "No plain log file");
        return $this->plainLogFile->open($mode);
    }

    /** @param resource $fp */
    public function seek(mixed $fp, int $offset, int $whence = SEEK_SET): int {
        $this->generalUtils()->checkNotNull($this->plainLogFile, "No plain log file");
        return $this->plainLogFile->seek($fp, $offset, $whence);
    }

    /** @param resource $fp */
    public function tell(mixed $fp): int {
        $this->generalUtils()->checkNotNull($this->plainLogFile, "No plain log file");
        return $this->plainLogFile->tell($fp);
    }

    /** @param resource $fp */
    public function eof(mixed $fp): bool {
        $this->generalUtils()->checkNotNull($this->plainLogFile, "No plain log file");
        return $this->plainLogFile->eof($fp);
    }

    /** @param resource $fp */
    public function gets(mixed $fp): ?string {
        $this->generalUtils()->checkNotNull($this->plainLogFile, "No plain log file");
        return $this->plainLogFile->gets($fp);
    }

    /** @param resource $fp */
    public function close(mixed $fp): bool {
        $this->generalUtils()->checkNotNull($this->plainLogFile, "No plain log file");
        $result = $this->plainLogFile->close($fp);
        $this->plainLogFile = null;
        if (is_file($this->gzPath)) {
            $this->deletePlain();
        }
        return $result;
    }

    public function optimize(): void {
        $has_gz = is_file($this->gzPath);
        $has_plain = is_file($this->plainPath);
        $pretty_has_gz = $has_gz ? '✅' : '🚫';
        $pretty_has_plain = $has_plain ? '✅' : '🚫';
        $this->log()->debug("Optimizing hybrid log file {$this->plainPath} {$pretty_has_plain} / {$this->gzPath} {$pretty_has_gz}...");
        if (!$has_gz && $has_plain) {
            $this->copyToGz();
        }
        if ($has_plain) {
            $this->deletePlain();
        }
    }

    protected function copyToGz(): void {
        $this->log()->debug("Optimize hybrid log file {$this->plainPath} -> {$this->gzPath}");
        $fp = fopen($this->plainPath, 'r');
        $gzp = gzopen($this->gzPath, 'wb');
        $this->generalUtils()->checkNotBool($fp, 'fopen failed');
        $this->generalUtils()->checkNotBool($gzp, 'gzopen failed');
        while ($buf = fread($fp, 1024)) {
            gzwrite($gzp, $buf, 1024);
        }
        fclose($fp);
        gzclose($gzp);
    }

    protected function copyToPlain(): void {
        $this->log()->debug("Cache hybrid log file {$this->gzPath} -> {$this->plainPath}");
        $gzp = gzopen($this->gzPath, 'rb');
        $fp = fopen($this->plainPath, 'w');
        $this->generalUtils()->checkNotBool($gzp, 'gzopen failed');
        $this->generalUtils()->checkNotBool($fp, 'fopen failed');
        while ($buf = gzread($gzp, 1024)) {
            fwrite($fp, $buf, 1024);
        }
        gzclose($gzp);
        fclose($fp);
    }

    protected function deletePlain(): void {
        $this->log()->debug("Remove redundant hybrid log file {$this->plainPath}");
        unlink($this->plainPath);
    }

    public function purge(): void {
        if (is_file($this->gzPath)) {
            unlink($this->gzPath);
            $this->log()->info("Removed old gz log file {$this->gzPath}");
        }
        if (is_file($this->plainPath)) {
            unlink($this->plainPath);
            $this->log()->info("Removed old plain log file {$this->plainPath}");
        }
        if (is_file($this->indexPath)) {
            unlink($this->indexPath);
            $this->log()->info("Removed old log index file {$this->indexPath}");
        }
    }

    public function __toString(): string {
        return "HybridLogFile({$this->gzPath}, {$this->plainPath}, {$this->indexPath})";
    }

    public function serialize(): string {
        return json_encode([
            'class' => self::class,
            'plainPath' => $this->plainPath,
            'gzPath' => $this->gzPath,
            'indexPath' => $this->indexPath,
        ]) ?: '{}';
    }

    public static function deserialize(string $serialized): ?LogFileInterface {
        $deserialized = json_decode($serialized, true);
        if ($deserialized['class'] !== self::class) {
            return null;
        }
        return new self(
            $deserialized['plainPath'],
            $deserialized['gzPath'],
            $deserialized['indexPath'],
        );
    }
}
