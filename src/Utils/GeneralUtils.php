<?php

namespace Olz\Utils;

class GeneralUtils {
    use WithUtilsTrait;

    // Error handling

    /**
     * @phpstan-assert !null $value
     *
     * @param string|callable(): string $error_message
     */
    public function checkNotNull(mixed $value, string|callable $error_message): void {
        if ($value === null) {
            $message = $this->getCheckErrorMessage($error_message);
            $this->log()->error($message);
            throw new \Exception($message);
        }
    }

    /**
     * @phpstan-assert !false $value
     *
     * @param string|callable(): string $error_message
     */
    public function checkNotFalse(mixed $value, string|callable $error_message): void {
        if ($value === false) {
            $message = $this->getCheckErrorMessage($error_message);
            $this->log()->error($message);
            throw new \Exception($message);
        }
    }

    /**
     * @phpstan-assert !bool $value
     *
     * @param string|callable(): string $error_message
     */
    public function checkNotBool(mixed $value, string|callable $error_message): void {
        if (is_bool($value)) {
            $message = $this->getCheckErrorMessage($error_message);
            $this->log()->error($message);
            throw new \Exception($message);
        }
    }

    /**
     * @phpstan-assert !'' $value
     *
     * @param string|callable(): string $error_message
     */
    public function checkNotEmpty(mixed $value, string|callable $error_message): void {
        if ($value === '') {
            $message = $this->getCheckErrorMessage($error_message);
            $this->log()->error($message);
            throw new \Exception($message);
        }
    }

    /** @param string|callable(): string $error_message */
    protected function getCheckErrorMessage(string|callable $error_message): string {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $app_env = $this->envUtils()->getAppEnv();
        $is_test = $app_env === 'test';
        $file = basename($trace[1]['file'] ?? '');
        $line = $is_test ? "***" : $trace[1]['line'] ?? '';
        $callsite = "{$file}:{$line}";
        if (is_string($error_message)) {
            return "{$callsite} {$error_message}";
        }
        $computed_error_message = $error_message();
        return "{$callsite} {$computed_error_message}";
    }

    // Escape

    /** @param array<string> $tokens */
    public function escape(string $string, array $tokens): string {
        $esc_tokens = array_map(fn ($token) => "\\{$token}", $tokens);
        return str_replace($tokens, $esc_tokens, $string);
    }

    /** @param array<string> $tokens */
    public function unescape(string $string, array $tokens): string {
        $esc_tokens = array_map(fn ($token) => "\\{$token}", $tokens);
        return str_replace($esc_tokens, $tokens, $string);
    }

    // Base64

    public function base64EncodeUrl(string $string): string {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($string));
    }

    public function base64DecodeUrl(string $string): string {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $string));
    }

    // Crypto

    public function encrypt(string $key, mixed $data): string {
        $plaintext = json_encode($data);
        if (!$plaintext) {
            throw new \Exception("encrypt: json_encode failed");
        }
        $algo = 'aes-256-gcm';
        $iv = $this->getRandomIvForAlgo($algo);
        $ciphertext = openssl_encrypt($plaintext, $algo, $key, OPENSSL_RAW_DATA, $iv, $tag);
        if (!$ciphertext) {
            throw new \Exception("encrypt: openssl_encrypt failed");
        }
        $result = json_encode([
            'algo' => $algo,
            'iv' => $this->base64EncodeUrl($iv),
            'tag' => $tag ? $this->base64EncodeUrl($tag) : null,
            'ciphertext' => $this->base64EncodeUrl($ciphertext),
        ]);
        if (!$result) {
            throw new \Exception("encrypt: this should never happen");
        }
        return $this->base64EncodeUrl($result);
    }

    protected function getRandomIvForAlgo(string $algo): string {
        $length = openssl_cipher_iv_length($algo);
        if ($length === false) {
            throw new \Exception("Unknown openssl_cipher_iv_length({$algo})");
        }
        if ($length === 0) {
            return '';
        }
        return openssl_random_pseudo_bytes($length);
    }

    public function decrypt(string $key, string $token): mixed {
        $decrypt_data = json_decode($this->base64DecodeUrl($token), true);
        if (!$decrypt_data) {
            throw new \Exception("decrypt: json_decode failed");
        }
        $ciphertext = $this->base64DecodeUrl($decrypt_data['ciphertext']);
        $algo = $decrypt_data['algo'] ?? 'aes-256-gcm';
        $iv = $this->base64DecodeUrl($decrypt_data['iv']);
        $tag = $this->base64DecodeUrl($decrypt_data['tag']);
        $plaintext = openssl_decrypt($ciphertext, $algo, $key, OPENSSL_RAW_DATA, $iv, $tag);
        if (!$plaintext) {
            throw new \Exception("decrypt: openssl_decrypt failed");
        }
        return json_decode($plaintext, true);
    }

    public function hash(string $key, string $data): string {
        $result = hash_hmac('sha256', $data, $key, true);
        return $this->base64EncodeUrl($result);
    }

    // Algorithms

    /** @return array{0: int, 1: int<-1, 1>} */
    public function binarySearch(callable $compare_fn, int $start, int $end): array {
        $search_start = $start;
        $search_end = $end;
        if ($search_start === $search_end) {
            return [0, 0];
        }
        while ($search_start < $search_end) {
            $probe_index = (int) floor(($search_start + $search_end) / 2);
            $result = $compare_fn($probe_index);
            if ($result < 0) {
                $search_end = $probe_index;
            } elseif ($result > 0) {
                $search_start = $probe_index + 1;
            } else {
                return [$probe_index, 0];
            }
        }
        if ($search_start === $end) {
            return [$end - 1, 1];
        }
        $result = $compare_fn($search_start);
        if ($result < 0) {
            return [$search_start, -1];
        }
        if ($result > 0) {
            return [$search_start, 1];
        }
        return [$search_start, 0];
    }

    // Debugging

    /** @param array<array<string, mixed>> $trace */
    public function getPrettyTrace(array $trace): string {
        $output = 'Stack trace:'.PHP_EOL;

        $trace_len = count($trace);
        for ($i = 0; $i < $trace_len; $i++) {
            $entry = $trace[$i];

            $func = $entry['function'].'(';
            $args_len = count($entry['args']);
            for ($j = 0; $j < $args_len; $j++) {
                $func .= json_encode($entry['args'][$j]);
                if ($j < $args_len - 1) {
                    $func .= ', ';
                }
            }
            $func .= ')';

            $output .= "#{$i} {$entry['file']}:{$entry['line']} - {$func}\n";
        }
        return $output;
    }

    /** @param array<array<string, mixed>> $trace */
    public function getTraceOverview(array $trace, int $first_index = 1): string {
        $output = '';
        $trace_len = count($trace);
        $last_class_name = null;
        for ($i = $trace_len - 1; $i >= $first_index; $i--) {
            $entry = $trace[$i];

            $class_name = $entry['class'] ?? '';
            if (
                $class_name === ''
                || $class_name === $last_class_name
                || substr($class_name, 0, 4) !== 'Olz\\'
            ) {
                continue;
            }
            $reflection_class = new \ReflectionClass($class_name);
            if ($reflection_class->isAbstract()) {
                continue;
            }
            $last_class_name = $class_name;
            $base_class_name = substr($class_name, strrpos($class_name, '\\') + 1);

            if ($output !== '') {
                $output .= ">";
            }
            $output .= "{$base_class_name}";
        }
        return $output;
    }

    /** @return array<mixed> */
    public function measureLatency(callable $fn): array {
        $before = microtime(true);
        $result = $fn();
        $duration = round((microtime(true) - $before) * 1000, 1);
        $msg = "took {$duration}ms";
        return [$result, $msg];
    }

    // Tools

    public function removeRecursive(string $path): void {
        if (is_dir($path)) {
            $entries = scandir($path) ?: [];
            foreach ($entries as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                $entry_path = realpath("{$path}/{$entry}");
                if (!$entry_path) {
                    continue;
                }
                $this->removeRecursive($entry_path);
            }
            rmdir($path);
        } elseif (is_file($path)) {
            unlink($path);
        }
    }

    public static function fromEnv(): self {
        return new self();
    }
}
