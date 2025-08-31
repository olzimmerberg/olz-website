<?php

namespace Olz\Suche\Utils;

use Olz\Utils\WithUtilsTrait;

class SearchUtils {
    use WithUtilsTrait;

    /** @param array<string> $search_terms */
    public function getCutout(string $text, array $search_terms): string {
        $length_a = 40;
        $length_b = 40;

        $lowercase_text = strtolower($text);
        $start = 0;
        foreach ($search_terms as $search_term) {
            $search_key = strtolower($search_term);
            $start = strpos($lowercase_text, $search_key);
            if ($start > 0) {
                break;
            }
        }
        $prefix = "...";
        $suffix = "...";
        if (($start - $length_a) < 0) {
            $start = $length_a;
            $prefix = "";
        }
        if (strlen($text) < ($length_a + $length_b)) {
            $suffix = "";
        }
        $text = substr($text, $start - $length_a, $length_a + $length_b);
        return "{$prefix}{$text}{$suffix}";
    }

    /** @param array<string> $search_terms */
    public function highlight(string $text, array $search_terms): string {
        $start_token = '\[';
        $end_token = '\]';
        $tokens = [$start_token, $end_token];
        $text = $this->generalUtils()->escape($text, $tokens);
        foreach ($search_terms as $term) {
            $esc_term = preg_quote($this->generalUtils()->escape($term, $tokens), '/');
            $text = preg_replace(
                "/(?<!\\\\)({$esc_term})/i",
                "{$start_token}\\1{$end_token}",
                $text ?? '',
            );
        }
        $start_tag = '<span class="highlight">';
        $end_tag = '</span>';
        $esc_start_token = preg_quote($start_token, '/');
        $esc_end_token = preg_quote($end_token, '/');
        $text = preg_replace(
            ["/(?<!\\\\){$esc_start_token}/", "/(?<!\\\\){$esc_end_token}/"],
            [$start_tag, $end_tag],
            $text ?? '',
        );
        return $this->generalUtils()->unescape($text ?? '', $tokens);
    }

    public static function fromEnv(): self {
        return new self();
    }
}
