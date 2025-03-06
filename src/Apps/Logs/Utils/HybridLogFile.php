<?php

namespace Olz\Apps\Logs\Utils;

use Olz\Utils\WithUtilsTrait;

class HybridLogFile implements LogFileInterface {
    use WithUtilsTrait;

    protected ?LogFileInterface $logFile;

    public function __construct(
        public string $plainPath,
        public string $gzPath,
        public string $indexPath,
        public HybridState $state = HybridState::KEEP,
    ) {
        $log_file = null;
        if (is_file($gzPath)) {
            $log_file = new GzLogFile($gzPath, $indexPath);
        }
        if (is_file($plainPath)) {
            $log_file = new PlainLogFile($plainPath, $indexPath);
        }
        $this->logFile = $log_file;
    }

    public function getPath(): string {
        $this->generalUtils()->checkNotNull($this->logFile, "Inexistent hybrid log file {$this}");
        return $this->logFile->getPath();
    }

    public function getIndexPath(): string {
        return $this->indexPath;
    }

    public function exists(): bool {
        return $this->logFile?->exists() ?? false;
    }

    public function modified(): int {
        $this->generalUtils()->checkNotNull($this->logFile, "Inexistent hybrid log file {$this}");
        return $this->logFile->modified();
    }

    /** @return resource */
    public function open(string $mode): mixed {
        $this->generalUtils()->checkNotNull($this->logFile, "Inexistent hybrid log file {$this}");
        return $this->logFile->open($mode);
    }

    /** @param resource $fp */
    public function seek(mixed $fp, int $offset, int $whence = SEEK_SET): int {
        $this->generalUtils()->checkNotNull($this->logFile, "Inexistent hybrid log file {$this}");
        return $this->logFile->seek($fp, $offset, $whence);
    }

    /** @param resource $fp */
    public function tell(mixed $fp): int {
        $this->generalUtils()->checkNotNull($this->logFile, "Inexistent hybrid log file {$this}");
        return $this->logFile->tell($fp);
    }

    /** @param resource $fp */
    public function eof(mixed $fp): bool {
        $this->generalUtils()->checkNotNull($this->logFile, "Inexistent hybrid log file {$this}");
        return $this->logFile->eof($fp);
    }

    /** @param resource $fp */
    public function gets(mixed $fp): ?string {
        $this->generalUtils()->checkNotNull($this->logFile, "Inexistent hybrid log file {$this}");
        return $this->logFile->gets($fp);
    }

    /** @param resource $fp */
    public function close(mixed $fp): bool {
        $this->generalUtils()->checkNotNull($this->logFile, "Inexistent hybrid log file {$this}");
        return $this->logFile->close($fp);
    }

    public function optimize(): void {
        if ($this->state === HybridState::KEEP) {
            return;
        }
        $has_gz = is_file($this->gzPath);
        $has_plain = is_file($this->plainPath);
        $want_gz = match ($this->state) {
            HybridState::PREFER_GZ => true,
            HybridState::PREFER_BOTH => true,
            default => false,
        };
        $want_plain = match ($this->state) {
            HybridState::PREFER_PLAIN => true,
            HybridState::PREFER_BOTH => true,
            default => false,
        };
        if (!$has_gz && $want_gz) {
            $this->log()->info("Copy hybrid log file {$this->plainPath} -> {$this->gzPath}");
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
        if (!$has_plain && $want_plain) {
            $this->log()->info("Copy hybrid log file {$this->gzPath} -> {$this->plainPath}");
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
        if ($has_gz && !$want_gz) {
            $this->log()->info("Remove redundant hybrid log file {$this->gzPath}");
            unlink($this->gzPath);
        }
        if ($has_plain && !$want_plain) {
            $this->log()->info("Remove redundant hybrid log file {$this->plainPath}");
            unlink($this->plainPath);
        }
    }

    public function __toString(): string {
        return "HybridLogFile({$this->plainPath}, {$this->gzPath}, {$this->indexPath}, {$this->state->value})";
    }

    public function serialize(): string {
        return json_encode([
            'class' => self::class,
            'plainPath' => $this->plainPath,
            'gzPath' => $this->gzPath,
            'state' => $this->state,
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
            $deserialized['state'],
        );
    }
}
