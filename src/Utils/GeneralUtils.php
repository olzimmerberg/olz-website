<?php

namespace Olz\Utils;

class GeneralUtils {
    use WithUtilsTrait;

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
        $algo = 'aes-256-gcm';
        $iv = $this->getRandomIvForAlgo($algo);
        $ciphertext = openssl_encrypt($plaintext, $algo, $key, OPENSSL_RAW_DATA, $iv, $tag);
        return $this->base64EncodeUrl(json_encode([
            'algo' => $algo,
            'iv' => $this->base64EncodeUrl($iv),
            'tag' => $this->base64EncodeUrl($tag),
            'ciphertext' => $this->base64EncodeUrl($ciphertext),
        ]));
    }

    protected function getRandomIvForAlgo(string $algo): string {
        return openssl_random_pseudo_bytes(openssl_cipher_iv_length($algo));
    }

    public function decrypt(string $key, string $token): mixed {
        $decrypt_data = json_decode($this->base64DecodeUrl($token), true);
        if (!$decrypt_data) {
            return null;
        }
        $ciphertext = $this->base64DecodeUrl($decrypt_data['ciphertext']);
        $algo = $decrypt_data['algo'] ?? 'aes-256-gcm';
        $iv = $this->base64DecodeUrl($decrypt_data['iv']);
        $tag = $this->base64DecodeUrl($decrypt_data['tag']);
        $plaintext = openssl_decrypt($ciphertext, $algo, $key, OPENSSL_RAW_DATA, $iv, $tag);
        return json_decode($plaintext, true);
    }

    // Algorithms

    public function binarySearch(callable $compare_fn, int $start, int $end): int {
        $search_start = $start;
        $search_end = $end;
        while ($search_start < $search_end) {
            $probe_index = (int) floor(($search_start + $search_end) / 2);
            $result = $compare_fn($probe_index);
            if ($result < 0) {
                $search_end = $probe_index;
            } elseif ($result > 0) {
                $search_start = $probe_index + 1;
            } else {
                // TODO: Or do we want the first occurrence of multiple?
                return $probe_index;
            }
        }
        return $search_start;
    }

    // Debugging

    /** @param array<array<string, mixed>> $trace */
    public function getPrettyTrace(array $trace): string {
        $output = 'Stack trace:'.PHP_EOL;

        $trace_len = count($trace);
        for ($i = 1; $i < $trace_len; $i++) {
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

            $output .= '#'.($i - 1).' '.$entry['file'].':'.$entry['line'].' - '.$func.PHP_EOL;
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
            $entries = scandir($path);
            foreach ($entries as $entry) {
                if ($entry !== '.' && $entry !== '..') {
                    $entry_path = realpath("{$path}/{$entry}");
                    $this->removeRecursive($entry_path);
                }
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
