<?php

namespace Olz\Suche\Components\OlzSuche;

use Olz\Components\Common\OlzPostingListItem\OlzPostingListItem;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{anfrage: string}> */
class OlzSucheParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzSuche extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function getTitle(): string {
        return "Suche";
    }

    public function getDescription(string $pretty_terms): string {
        return "Stichwort-Suche nach \"{$pretty_terms}\" auf der Website der OL Zimmerberg.";
    }

    public string $description = "Stichwort-Suche auf der Website der OL Zimmerberg.";

    public function searchSqlWhenHasAccess(array $terms): string|array|null {
        $code_href = $this->envUtils()->getCodeHref();
        $db = $this->dbUtils()->getDb();
        $esc_title = $db->real_escape_string($this->getTitle());
        $esc_content = $db->real_escape_string($this->getDescription('Suche'));
        $where = implode(' AND ', array_map(function ($term) {
            return <<<ZZZZZZZZZZ
                (
                    title LIKE '%{$term}%'
                    OR text LIKE '%{$term}%'
                )
                ZZZZZZZZZZ;
        }, $terms));
        return [
            'with' => [
                <<<ZZZZZZZZZZ
                    base_suche AS (
                        SELECT
                            '{$code_href}suche?anfrage=Suche' AS link,
                            '{$code_href}assets/icns/magnifier_16.svg' AS icon,
                            NULL AS date,
                            '{$esc_title}' AS title,
                            '{$esc_content}' AS text
                    )
                    ZZZZZZZZZZ,
            ],
            'query' => <<<ZZZZZZZZZZ
                    SELECT
                        link, icon, date, title, text,
                        0.9 AS time_relevance
                    FROM base_suche
                    WHERE {$where}
                ZZZZZZZZZZ,
        ];
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $params = $this->httpUtils()->validateGetParams(OlzSucheParams::class);

        $terms = preg_split('/[\s,\.;]+/', $params['anfrage']);
        $this->generalUtils()->checkNotFalse($terms, "Could not split search terms '{$params['anfrage']}'");
        $pretty_terms = implode(', ', $terms);
        $esc_pretty_terms = htmlspecialchars($pretty_terms);

        $out = OlzHeader::render([
            'title' => "\"{$pretty_terms}\" - {$this->getTitle()}",
            'description' => $this->getDescription($pretty_terms),
        ]);

        $out .= <<<'ZZZZZZZZZZ'
            <div class='content-right'>
            </div>
            <div class='content-middle olz-suche'>
            ZZZZZZZZZZ;

        $out .= "<h1>Suchresultate für \"{$esc_pretty_terms}\"</h1>";

        if (($terms[0] ?? '') === '') {
            $out .= "<p><i>Keine Resultate</i></p>";
            $out .= OlzFooter::render();
            return $out;
        }

        $start_time = microtime(true);

        $results = $this->searchUtils()->getSearchResults($terms);
        foreach ($results as $result) {
            $pretty_date = null;
            if ($result['date']) {
                $pretty_date = $this->dateUtils()->olzDate("tt.mm.jj", $result['date']);
                $date_formattings = implode(' ', $this->searchUtils()->getDateFormattings($result['date']));
                $is_date_matching = false;
                foreach ($terms as $term) {
                    $esc_term = preg_quote($term);
                    if (preg_match("/{$esc_term}/i", $date_formattings)) {
                        $is_date_matching = true;
                        break;
                    }
                }
                if ($is_date_matching) {
                    $pretty_date = $this->searchUtils()->highlight($pretty_date, [$pretty_date]);
                }
            }
            $pretty_debug = $this->authUtils()->hasPermission('all') ? "<pre>{$result['debug']}</pre>" : '';
            $out .= OlzPostingListItem::render([
                'link' => $result['link'],
                'icon' => $result['icon'],
                'date' => $pretty_date,
                'title' => $this->searchUtils()->highlight($result['title'], $terms),
                'text' => $pretty_debug.$this->searchUtils()->highlight($result['text'] ?? '', $terms),
            ]);
        }
        if (count($results) === 0) {
            $out .= "<p><i>Keine Resultate</i></p>";
        }

        $duration = microtime(true) - $start_time;
        $pretty_duration = number_format($duration, 3, '.', '\'');
        $this->log()->info("Search for '{$pretty_terms}' took {$pretty_duration}s.");

        $out .= "</div>";

        $out .= OlzFooter::render();
        return $out;
    }
}
