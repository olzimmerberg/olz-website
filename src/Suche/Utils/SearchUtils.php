<?php

namespace Olz\Suche\Utils;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Olz\Apps\Anmelden\Components\OlzAnmelden\OlzAnmelden;
use Olz\Apps\Commands\Components\OlzCommands\OlzCommands;
use Olz\Apps\Files\Components\OlzFiles\OlzFiles;
use Olz\Apps\Logs\Components\OlzLogs\OlzLogs;
use Olz\Apps\Members\Components\OlzMembers\OlzMembers;
use Olz\Apps\Monitoring\Components\OlzMonitoring\OlzMonitoring;
use Olz\Apps\Newsletter\Components\OlzNewsletter\OlzNewsletter;
use Olz\Apps\Oev\Components\OlzOev\OlzOev;
use Olz\Apps\Panini2024\Components\OlzPanini2024\OlzPanini2024;
use Olz\Apps\Panini2024\Components\OlzPanini2024All\OlzPanini2024All;
use Olz\Apps\Panini2024\Components\OlzPanini2024Masks\OlzPanini2024Masks;
use Olz\Apps\Quiz\Components\OlzQuiz\OlzQuiz;
use Olz\Apps\Results\Components\OlzResults\OlzResults;
use Olz\Apps\SearchEngines\Components\OlzSearchEngines\OlzSearchEngines;
use Olz\Apps\Statistics\Components\OlzStatistics\OlzStatistics;
use Olz\Apps\Youtube\Components\OlzYoutube\OlzYoutube;
use Olz\Components\Auth\OlzEmailReaktion\OlzEmailReaktion;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\OlzHtmlSitemap\OlzHtmlSitemap;
use Olz\Components\OtherPages\OlzDatenschutz\OlzDatenschutz;
use Olz\Components\OtherPages\OlzFuerEinsteiger\OlzFuerEinsteiger;
use Olz\Components\OtherPages\OlzMaterial\OlzMaterial;
use Olz\Faq\Components\OlzFaqDetail\OlzFaqDetail;
use Olz\Faq\Components\OlzFaqList\OlzFaqList;
use Olz\Karten\Components\OlzKarteDetail\OlzKarteDetail;
use Olz\Karten\Components\OlzKarten\OlzKarten;
use Olz\News\Components\OlzNewsDetail\OlzNewsDetail;
use Olz\News\Components\OlzNewsList\OlzNewsList;
use Olz\Roles\Components\OlzRolePage\OlzRolePage;
use Olz\Roles\Components\OlzVerein\OlzVerein;
use Olz\Service\Components\OlzService\OlzService;
use Olz\Startseite\Components\OlzStartseite\OlzStartseite;
use Olz\Suche\Components\OlzSuche\OlzSuche;
use Olz\Termine\Components\OlzTerminDetail\OlzTerminDetail;
use Olz\Termine\Components\OlzTermineList\OlzTermineList;
use Olz\Termine\Components\OlzTerminLocationDetail\OlzTerminLocationDetail;
use Olz\Termine\Components\OlzTerminLocationsList\OlzTerminLocationsList;
use Olz\Termine\Components\OlzTerminTemplateDetail\OlzTerminTemplateDetail;
use Olz\Termine\Components\OlzTerminTemplatesList\OlzTerminTemplatesList;
use Olz\Users\Components\OlzUserDetail\OlzUserDetail;
use Olz\Utils\WithUtilsTrait;

/**
 * @phpstan-type SearchResult array{
 *   score: float,
 *   link: non-empty-string,
 *   icon: ?non-empty-string,
 *   date: ?\DateTime,
 *   title: non-empty-string,
 *   text: ?non-empty-string,
 * }
 * @phpstan-type PageSearchResults array{
 *   title: non-empty-string,
 *   bestScore: ?float,
 *   results: array<SearchResult>,
 * }
 */
class SearchUtils {
    use WithUtilsTrait;

    /** @var array<class-string<OlzRootComponent<mixed>>> */
    protected static array $all_page_classes = [
        // All classes that extend `OlzRootComponent` should be listed here:
        OlzAnmelden::class,
        OlzCommands::class,
        OlzFiles::class,
        OlzLogs::class,
        OlzMembers::class,
        OlzMonitoring::class,
        OlzNewsletter::class,
        OlzOev::class,
        OlzPanini2024::class,
        OlzPanini2024All::class,
        OlzPanini2024Masks::class,
        OlzQuiz::class,
        OlzResults::class,
        OlzSearchEngines::class,
        OlzStatistics::class,
        OlzYoutube::class,
        OlzEmailReaktion::class,
        OlzHtmlSitemap::class,
        OlzDatenschutz::class,
        OlzFuerEinsteiger::class,
        OlzMaterial::class,
        OlzFaqDetail::class,
        OlzFaqList::class,
        OlzKarteDetail::class,
        OlzKarten::class,
        OlzNewsDetail::class,
        OlzNewsList::class,
        OlzRolePage::class,
        OlzVerein::class,
        OlzService::class,
        OlzStartseite::class,
        OlzSuche::class,
        OlzTerminDetail::class,
        OlzTermineList::class,
        OlzTerminLocationDetail::class,
        OlzTerminLocationsList::class,
        OlzTerminTemplateDetail::class,
        OlzTerminTemplatesList::class,
        OlzUserDetail::class,
    ];

    /**
     * @param array<string> $terms
     *
     * @return array<PageSearchResults>
     */
    public function getSearchResults(array $terms): array {
        $results = [];
        foreach (self::$all_page_classes as $page_class) {
            $results[] = $this->getPageSearchResults($page_class, $terms);
        }
        usort($results, fn ($a, $b) => $b['bestScore'] <=> $a['bestScore']);
        return $results;
    }

    /**
     * @param class-string<OlzRootComponent<array<string, mixed>>> $page_class
     * @param array<string>                                        $terms
     *
     * @return PageSearchResults
     */
    protected function getPageSearchResults(string $page_class, array $terms): array {
        $page = new $page_class();
        $results = $page->getSearchResults($terms);
        usort($results, fn ($a, $b) => $b['score'] <=> $a['score']);
        $first_result = $results[0] ?? null;
        $best_score = $first_result['score'] ?? null;
        return [
            'title' => $page->getSearchTitle(),
            'bestScore' => $best_score,
            'results' => $results,
        ];
    }

    /** @return array<Expression> */
    public function getDateCriteria(string $field, string $term): array {
        $result = $this->dateUtils()->parseDateTimeRange($term);
        if ($result === null) {
            return [];
        }
        return [Criteria::expr()->andX(
            Criteria::expr()->gte($field, $result['start']),
            Criteria::expr()->lt($field, $result['end']),
        )];
    }

    /**
     * @param array<string> $terms
     * @param array{
     *   link: non-empty-string,
     *   icon?: ?non-empty-string,
     *   date?: ?\DateTime,
     *   title: non-empty-string,
     * } $defaults
     *
     * @return array<SearchResult>
     */
    public function getStaticSearchResults(
        string $content,
        array $terms,
        array $defaults,
    ): array {
        $search_space = "{$content} {$defaults['title']}";
        $analysis = $this->analyze($search_space, $defaults['date'] ?? null, $terms);
        if (!$analysis['hasAll']) {
            return [];
        }
        return [
            [
                'score' => $analysis['score'],
                'icon' => null,
                'date' => null,
                'text' => $this->searchUtils()->getCutout($content, $terms) ?: null,
                ...$defaults,
            ],
        ];
    }

    /**
     * @param array{
     *   link: non-empty-string,
     *   icon?: ?non-empty-string,
     *   date?: ?\DateTime,
     *   title: non-empty-string,
     *   text?: ?non-empty-string,
     * } $result
     * @param array<string> $terms
     *
     * @return SearchResult
     */
    public function getScoredSearchResult(
        array $result,
        array $terms,
    ): array {
        $text_str = $result['text'] ?? '';
        $search_space = "{$text_str} {$result['title']}";
        $analysis = $this->analyze($search_space, $result['date'] ?? null, $terms);
        return [
            'icon' => null,
            'date' => null,
            ...$result,
            'score' => $analysis['score'],
            'text' => $this->searchUtils()->getCutout($text_str, $terms) ?: null,
        ];
    }

    /** @return array<string> */
    public function getDateFormattings(?\DateTime $date): array {
        if ($date === null) {
            return [];
        }
        return [
            $date->format('Y-m-d'),
            $date->format('d.m.Y'),
            $date->format('j.n.Y'),
        ];
    }

    /**
     * @param array<string> $terms
     *
     * @return array{score: float, hasAll: bool}
     */
    public function analyze(string $content, ?\DateTime $date, array $terms): array {
        $date_formattings = implode(' ', $this->getDateFormattings($date));
        $has_all = true;
        $sum_occurrences = 0;
        foreach ($terms as $term) {
            $esc_term = preg_quote($term);
            $num_occurrences = preg_match_all("/{$esc_term}/i", $content, $matches);
            if (preg_match("/{$esc_term}/i", $date_formattings)) {
                $num_occurrences++;
            }
            $sum_occurrences += $num_occurrences;
            if (!$num_occurrences) {
                $has_all = false;
            }
        }
        $score = round(1 - (1 / ($sum_occurrences / count($terms) + 1)), 5);
        return ['score' => $score, 'hasAll' => $has_all];
    }

    /** @param array<string> $search_terms */
    public function getCutout(string $text, array $search_terms, int $size = 100): string {
        $offsets_by_term = $this->getOffsets($text, $search_terms);

        $text_length = mb_strlen($text);
        $term_lengths = [];
        $term_scores = [];
        foreach ($search_terms as $search_term) {
            $term_length = mb_strlen($search_term);
            $term_lengths[] = $term_length;
            $term_scores[] = log($term_length) + 1;
        }

        $all_end_offsets = [];
        for ($i = 0; $i < count($offsets_by_term); $i++) {
            $term_length = $term_lengths[$i];
            foreach ($offsets_by_term[$i] as $offset) {
                $all_end_offsets[] = $offset + $term_length;
            }
        }
        $all_end_offsets[] = $text_length;
        sort($all_end_offsets);

        $best_cutout_start = 0;
        $best_cutout_end = 0;
        $best_cutout_score = 0;
        $start_idxs = array_map(fn () => -1, $search_terms);
        $end_idxs = array_map(fn () => -1, $search_terms);
        $after_all = $text_length + 1;
        foreach ($all_end_offsets as $offset) {
            $start = $offset;
            $score = 1;
            for ($i = 0; $i < count($search_terms); $i++) {
                $term_length = $term_lengths[$i];
                $offsets = $offsets_by_term[$i];
                while (($offsets[$end_idxs[$i] + 1] ?? $after_all) + $term_length <= $offset) {
                    $end_idxs[$i]++;
                }
                while (($offsets[$start_idxs[$i] + 1] ?? $after_all) < $offset - $size) {
                    $start_idxs[$i]++;
                }
                $num = $end_idxs[$i] - $start_idxs[$i];
                $term_score = $term_scores[$i];
                $score *= ($num * $term_score) + 1;
                if (($offsets[$start_idxs[$i] + 1] ?? $after_all) < $start) {
                    $start = $offsets[$start_idxs[$i] + 1];
                }
            }
            if ($score > $best_cutout_score) {
                $best_cutout_score = $score;
                $best_cutout_start = $start;
                $best_cutout_end = $offset;
            }
        }
        $best_cutout_length = $best_cutout_end - $best_cutout_start;
        $margin_size = ($size - $best_cutout_length) / 2;
        $offset = max(0, min($text_length - $size, $best_cutout_start - intval($margin_size)));
        return implode('', [
            $offset === 0 ? '' : '…',
            trim(mb_substr($text, $offset, $size)),
            ($offset + $size >= $text_length) ? '' : '…',
        ]);
    }

    /**
     * @param array<string> $search_terms
     *
     * @return array<array<int>>
     */
    public function getOffsets(string $text, array $search_terms): array {
        $text_length = mb_strlen($text);
        $offsets_by_term = [];
        foreach ($search_terms as $search_term) {
            $term_length = mb_strlen($search_term);
            $term_regex = preg_quote($search_term);
            $parts = preg_split("/({$term_regex})/ui", $text) ?: [];
            $part_lengths = array_map(fn ($part) => mb_strlen($part), $parts);
            unset($parts);
            $offsets = [];
            $offset = 0;
            $sanity_check = false;
            foreach ($part_lengths as $part_length) {
                $offset += $part_length;
                $sanity_check = $offset === $text_length;
                if (!$sanity_check) { // Don't add the last offset; it's just the text length
                    $offsets[] = $offset;
                }
                $offset += $term_length;
            }
            assert($sanity_check, 'Cutout offset sanity check failed');
            $offsets_by_term[] = $offsets;
        }
        return $offsets_by_term;
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
