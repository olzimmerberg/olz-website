<?php

namespace Olz\Apps\Logs\Utils;

use Olz\Utils\WithUtilsTrait;

class LineLocation {
    public function __construct(
        public LogFileInterface $logFile,
        public int $lineNumber, // -1 = last line
    ) {
    }
}

class ReadResult {
    public function __construct(
        public array $lines,
        public LineLocation|null $previous,
        public LineLocation|null $next,
    ) {
    }
}

abstract class BaseLogsChannel {
    use WithUtilsTrait;

    public const LOG_LEVELS = [
        'debug',
        'info',
        'notice',
        'warning',
        'error',
        'critical',
        'alert',
        'emergency',
    ];

    public const INDEX_FILE_VERSION = '1.1';

    abstract public static function getId(): string;

    abstract public static function getName(): string;

    // Page size; number of lines of log entires.
    public static $pageSize = 1000;

    // Assumes there are files, though...
    abstract protected function getLogFileBefore(LogFileInterface $log_file): LogFileInterface;

    abstract protected function getLogFileAfter(LogFileInterface $log_file): LogFileInterface;

    public function continueReading(
        LineLocation $line_location,
        string $mode,
        array $query,
    ): ReadResult {
        if ($mode === 'previous') {
            $line_limit = self::$pageSize;
            $time_limit = time() + 10;
            return $this->readMatchingLinesBefore($line_location, $query, $line_limit, $time_limit);
        }
        if ($mode === 'next') {
            $line_limit = self::$pageSize;
            $time_limit = time() + 10;
            return $this->readMatchingLinesAfter($line_location, $query, $line_limit, $time_limit);
        }
        throw new \Exception("Mode must be 'previous' or 'next', was '{$mode}'.");
    }

    public function readAroundDateTime(\DateTime $date_time, array $query): ReadResult {
        $line_location = $this->getLineLocationForDateTime($date_time);
        $line_limit = floor(self::$pageSize / 2) + 1;
        $time_limit = time() + 5;
        $lines_before = $this->readMatchingLinesBefore($line_location, $query, $line_limit, $time_limit);
        $line_limit = self::$pageSize - count($lines_before->lines) + 1;
        $time_limit = time() + 5;
        $lines_after = $this->readMatchingLinesAfter($line_location, $query, $line_limit, $time_limit);
        return new ReadResult([
            ...array_slice($lines_before->lines, 0, count($lines_before->lines) - 1),
            '---',
            ...$lines_after->lines,
        ], $lines_before->previous, $lines_after->next);
    }

    protected function getOrCreateIndex(LogFileInterface $log_file) {
        $file_path = $log_file->getPath();
        $index_path = "{$file_path}.index.json.gz";
        if (is_file($index_path)) {
            $index = $this->readIndexFile($index_path);
            $version_matches = self::INDEX_FILE_VERSION === ($index['version'] ?? '1.0');
            $modified_matches = $log_file->modified() === $index['modified'];
            $is_cache_hit = $version_matches && $modified_matches;
            if ($is_cache_hit) {
                return $index;
            }
            unlink($index_path);
        }
        // cache miss
        $index = $this->indexFile($log_file);
        try {
            $this->writeIndexFile($index_path, $index);
        } catch (\Throwable $th) {
            // ignore; best effort!
        }
        return $index;
    }

    protected function indexFile(LogFileInterface $log_file) {
        $file_path = $log_file->getPath();
        $index = [];
        $index['version'] = self::INDEX_FILE_VERSION;
        $index['modified'] = $log_file->modified();
        $index['start_date'] = null;
        $index['lines'] = [0];
        $fp = $log_file->open('r');
        $log_file->seek($fp, 0, SEEK_END);
        $file_size = $log_file->tell($fp);
        $log_file->seek($fp, 0, SEEK_SET);
        while (!$log_file->eof($fp)) {
            $line = $log_file->gets($fp);
            if ($index['start_date'] === null) {
                $truncated_line = substr($line, 0, $this->getDateMaxPosition());
                $date_time = $this->parseDateTimeOfLine($truncated_line);
                if ($date_time) {
                    $index['start_date'] = $date_time->format('Y-m-d H:i:s');
                }
            }
            $line_index = $log_file->tell($fp);
            if ($line_index !== $file_size) {
                $index['lines'][] = $line_index;
            }
        }
        fclose($fp);
        $index['lines'][] = $file_size;
        return $index;
    }

    protected function readIndexFile(string $index_path): array {
        return json_decode(gzdecode(file_get_contents($index_path)), true);
    }

    protected function writeIndexFile(string $index_path, array $content): void {
        file_put_contents($index_path, gzencode(json_encode($content)));
    }

    protected function readMatchingLinesBefore(
        LineLocation $line_location,
        array $query,
        int $line_limit,
        float $time_limit,
    ): ReadResult {
        $log_file = $line_location->logFile;
        $file_index = $this->getOrCreateIndex($log_file);
        $fp = $log_file->open('r');

        $continuation_location = clone $line_location;
        if ($continuation_location->lineNumber === -1) {
            // last line is empty
            $continuation_location->lineNumber = count($file_index['lines']) - 2;
        }
        $matching_lines = [];
        while (count($matching_lines) < $line_limit && time() <= $time_limit) {
            $index = $file_index['lines'][$continuation_location->lineNumber];
            $log_file->seek($fp, $index);
            $line = $this->escapeSpecialChars($log_file->gets($fp));
            if ($line === false) {
                $line = '';
            }
            if ($this->isLineMatching($line, $query)) {
                array_unshift($matching_lines, $line);
            }
            $continuation_location->lineNumber--;
            if ($continuation_location->lineNumber < 0) {
                try {
                    $log_file_before = $this->getLogFileBefore($log_file);
                    $this->log()->debug("log_file_before {$log_file_before->getPath()}");
                    $prev_file_location = new LineLocation($log_file_before, -1);
                    $new_line_limit = $line_limit - count($matching_lines);
                    $result = $this->readMatchingLinesBefore($prev_file_location, $query, $new_line_limit, $time_limit);
                    $matching_lines = [
                        ...$result->lines,
                        ...$matching_lines,
                    ];
                    $continuation_location = $result->previous;
                } catch (\Throwable $th) {
                    // Then, that's all we can do
                    $continuation_location = null;
                }
                break;
            }
        }

        $log_file->close($fp);
        return new ReadResult($matching_lines, $continuation_location, $line_location);
    }

    protected function readMatchingLinesAfter(
        LineLocation $line_location,
        array $query,
        int $line_limit,
        float $time_limit,
    ): ReadResult {
        $log_file = $line_location->logFile;
        $file_index = $this->getOrCreateIndex($log_file);
        $fp = $log_file->open('r');
        // last line is empty
        $number_of_lines = count($file_index['lines']) - 1;

        $continuation_location = clone $line_location;
        if ($continuation_location->lineNumber === -1) {
            // last line is empty
            $continuation_location->lineNumber = count($file_index['lines']) - 2;
        }
        $matching_lines = [];
        while (count($matching_lines) < $line_limit && time() <= $time_limit) {
            $index = $file_index['lines'][$continuation_location->lineNumber];
            $log_file->seek($fp, $index);
            $line = $this->escapeSpecialChars($log_file->gets($fp));
            if ($line === false) {
                $line = '';
            }
            if ($this->isLineMatching($line, $query)) {
                array_push($matching_lines, $line);
            }
            $continuation_location->lineNumber++;
            if ($continuation_location->lineNumber >= $number_of_lines) {
                try {
                    $log_file_after = $this->getLogFileAfter($log_file);
                    $this->log()->debug("log_file_after {$log_file_after->getPath()}");
                    $next_file_location = new LineLocation($log_file_after, 0);
                    $new_line_limit = $line_limit - count($matching_lines);
                    $result = $this->readMatchingLinesAfter($next_file_location, $query, $new_line_limit, $time_limit);
                    $matching_lines = [
                        ...$matching_lines,
                        ...$result->lines,
                    ];
                    $continuation_location = $result->next;
                } catch (\Throwable $th) {
                    // Then, that's all we can do
                    $continuation_location = null;
                }
                break;
            }
        }

        $log_file->close($fp);
        return new ReadResult($matching_lines, $line_location, $continuation_location);
    }

    protected function isLineMatching(string $line, array $query): bool {
        $min_log_level = $query['minLogLevel'] ?? null;
        if (!$this->isLineMatchingMinLogLevel($line, $min_log_level)) {
            return false;
        }
        $text_search = $query['textSearch'] ?? null;
        if (!$this->isLineMatchingTextSearch($line, $text_search)) {
            return false;
        }
        return true;
    }

    protected function isLineMatchingMinLogLevel(string $line, string|null $min_log_level): bool {
        if (!$min_log_level) {
            return true;
        }
        $log_levels = self::LOG_LEVELS;
        $level_pos = array_search($min_log_level, $log_levels);
        if ($level_pos === false) {
            return true;
        }
        $matching_log_levels = array_slice($log_levels, $level_pos);
        $log_levels_regex = implode('|', array_map(function ($log_level) {
            return '\.'.strtoupper($log_level);
        }, $matching_log_levels));
        return (bool) preg_match("/{$log_levels_regex}/", $line);
    }

    protected function isLineMatchingTextSearch(string $line, string|null $text_search): bool {
        if (!$text_search) {
            return true;
        }
        $esc_text_search = preg_quote($text_search, '/');
        return (bool) preg_match("/{$esc_text_search}/", $line);
    }

    protected function escapeSpecialChars(?string $line): string {
        $line = iconv('UTF-8', "UTF-8//IGNORE", $line);
        return html_entity_decode(htmlspecialchars($line)) ?? '';
    }

    // Override this function, if you have a different date format.
    protected function parseDateTimeOfLine(string $line): \DateTime|null {
        $res = preg_match('/(\d{4}\-\d{2}\-\d{2})(T|\s+)(\d{2}\:\d{2}\:\d{2})/', $line, $matches);
        if (!$res) {
            return null;
        }
        try {
            return new \DateTime("{$matches[1]} {$matches[3]}");
        } catch (\Throwable $th) {
            return null;
        }
    }

    // Override this function, if you have a different date placement within the log line.
    protected function getDateMaxPosition(): int {
        return 22;
    }
}
