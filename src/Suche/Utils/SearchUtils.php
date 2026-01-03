<?php

namespace Olz\Suche\Utils;

use Doctrine\ORM\Query\ResultSetMapping;
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
 *   link: non-empty-string,
 *   icon: ?non-empty-string,
 *   date: ?\DateTime,
 *   title: non-empty-string,
 *   text: ?non-empty-string,
 *   score: float,
 *   debug: string,
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
        $db = $this->dbUtils()->getDb();
        $esc_terms = array_map(fn ($term) => $db->real_escape_string($term), $terms);
        $num_terms = count($terms);

        $sub_sql = $page->searchSql($esc_terms);
        if ($sub_sql === null) {
            return [
                'title' => $page->getSearchTitle(),
                'bestScore' => null,
                'results' => [],
            ];
        }

        $idx = 0;
        $term_evaluation_sqls = [];
        $term_evaluation_columns = [];
        foreach ($terms as $term) {
            $esc_term = $db->real_escape_string(preg_quote($term));
            $term_evaluation_sqls[] = "get_quality(concatted_content, '{$esc_term}') AS term{$idx}_any";
            $term_evaluation_columns[] = "term{$idx}_any";
            // Add preference for word matches
            $term_evaluation_sqls[] = "get_quality(concatted_content, '(?=\\\\W|^){$esc_term}') AS term{$idx}_prefix";
            $term_evaluation_columns[] = "term{$idx}_prefix";
            $term_evaluation_sqls[] = "get_quality(concatted_content, '{$esc_term}(?=\\\\W|$)') AS term{$idx}_suffix";
            $term_evaluation_columns[] = "term{$idx}_suffix";
            $idx++;
        }
        // Add preference to term combination matches
        for ($num_combined = 2; $num_combined <= min(3, $num_terms); $num_combined++) {
            for ($start_combined = 0; $start_combined <= $num_terms - $num_combined; $start_combined++) {
                $combined_terms = array_slice($terms, $start_combined, $num_combined);
                $esc_combined_terms = $db->real_escape_string(implode('(\W{0,5}|\s*)', array_map(
                    fn ($term) => preg_quote($term),
                    $combined_terms
                )));
                $term_evaluation_sqls[] = "get_quality(concatted_content, '{$esc_combined_terms}') AS terms_{$start_combined}_{$num_combined}";
                $term_evaluation_columns[] = "terms_{$start_combined}_{$num_combined}";
                // TODO: Combined date formattings?
            }
        }
        $term_evaluation_sql = implode(',', $term_evaluation_sqls);
        $term_quality_sql = implode('+', $term_evaluation_columns);
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('link', 'link', 'string');
        $rsm->addScalarResult('icon', 'icon', 'string');
        $rsm->addScalarResult('date', 'date', 'date');
        $rsm->addScalarResult('title', 'title', 'string');
        $rsm->addScalarResult('text', 'text', 'string');
        $rsm->addScalarResult('score', 'score', 'string');
        $rsm->addScalarResult('time_relevance', 'time_relevance', 'string');
        $this->sqlExecute('DROP FUNCTION IF EXISTS get_quality');
        $this->sqlExecute(<<<'ZZZZZZZZZZ'
            CREATE FUNCTION get_quality (content TEXT, regex TEXT)
                RETURNS FLOAT DETERMINISTIC
                RETURN (LENGTH(content) - LENGTH(REGEXP_REPLACE(content, regex, ''))) / SQRT(LENGTH(content))
            ZZZZZZZZZZ);
        $sql = <<<ZZZZZZZZZZ
            WITH
                sub AS ({$sub_sql}),
                concatted AS (
                    SELECT
                        *,
                        CONCAT(IFNULL(title, ''), ' ', IFNULL(text, '')) AS concatted_content,
                        LEAST(1.0, GREATEST(0.0, time_relevance)) AS norm_time_relevance
                    FROM sub
                ),
                evaluated AS (
                    SELECT
                        *,
                        {$term_evaluation_sql}
                    FROM concatted
                ),
                scored AS (
                    SELECT 
                        *, 
                        (
                            (1 - 1 / (1 + ({$term_quality_sql}))) * norm_time_relevance
                        ) AS score
                    FROM evaluated
                )
            SELECT *
            FROM scored
            WHERE score > 0
            ORDER BY score DESC
            ZZZZZZZZZZ;
        $this->log()->debug("Search SQL: {$sql}");
        $sql_results = $this->entityManager()->createNativeQuery($sql, $rsm)->getArrayResult();
        $results = array_map(function ($row) use ($terms) {
            return [
                'link' => $row['link'],
                'icon' => $row['icon'],
                'date' => $row['date'],
                'title' => $row['title'],
                'text' => $this->searchUtils()->getCutout($row['text'] ?? '', $terms) ?: null,
                'score' => round($row['score'], 5),
                'debug' => implode(' / ', [
                    'Score: '.round($row['score'], 5),
                    'Time relevance: '.round($row['time_relevance'], 5),
                ]),
            ];
        }, $sql_results);
        $first_result = $results[0] ?? null;
        $best_score = $first_result['score'] ?? null;
        return [
            'title' => $page->getSearchTitle(),
            'bestScore' => $best_score,
            'results' => $results,
        ];
    }

    protected function sqlExecute(string $sql): void {
        $this->entityManager()->createNativeQuery($sql, new ResultSetMapping())->execute();
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

    public function getDateSql(string $field, string $term): ?string {
        $result = $this->dateUtils()->parseDateTimeRange($term);
        if ($result === null) {
            return null;
        }
        return <<<ZZZZZZZZZZ
            (
                {$field} >= '{$result['start']->format('Y-m-d')}'
                AND {$field} < '{$result['end']->format('Y-m-d')}'
            )
            ZZZZZZZZZZ;
    }

    /** @param array<string> $search_terms */
    public function getCutout(string $text, array $search_terms, int $size = 100): string {
        $text = $this->censorEmails($text);
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

    public function censorEmails(string $text): string {
        return preg_replace('/([A-Z0-9a-z._%+-]+)@([A-Za-z0-9.-]+)/', '***@***', $text) ?? '';
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
            $term_regex = preg_quote($search_term, '/');
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
        $offsets_by_term = $this->getOffsets($text, $search_terms);
        $term_lengths = array_map(fn ($term) => mb_strlen($term), $search_terms);

        $ranges = [];
        for ($i = 0; $i < count($offsets_by_term); $i++) {
            $term_length = $term_lengths[$i];
            foreach ($offsets_by_term[$i] as $offset) {
                $ranges[] = [$offset, $offset + $term_length];
            }
        }
        $merged_ranges = $this->normalizeRanges($ranges);

        $out = '';
        $start_tag = '<span class="highlight">';
        $end_tag = '</span>';
        $last_end = 0;
        foreach ($merged_ranges as $range) {
            $out .= mb_substr($text, $last_end, $range[0] - $last_end);
            $out .= $start_tag;
            $out .= mb_substr($text, $range[0], $range[1] - $range[0]);
            $out .= $end_tag;
            $last_end = $range[1];
        }
        $out .= mb_substr($text, $last_end);
        return $out;
    }

    /**
     * @param array<array{0:int, 1:int}> $ranges
     *
     * @return array<array{0:int, 1:int}>
     */
    public function normalizeRanges(array $ranges): array {
        usort($ranges, fn ($a, $b) => $a[0] <=> $b[0]);
        $normalized_ranges = [];
        $num_ranges = count($ranges);
        $i = 0;
        while ($i < $num_ranges) {
            $range = $ranges[$i];
            $start = $range[0];
            $end = $range[1];
            while ($i + 1 < $num_ranges && $ranges[$i + 1][0] <= $end) { // merge next range
                $end = max($end, $ranges[$i + 1][1]);
                $i++;
            }
            $normalized_ranges[] = [$start, $end];
            $i++;
        }
        return $normalized_ranges;
    }

    public static function fromEnv(): self {
        return new self();
    }
}
