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

    public function getSearchTitle(): string {
        return 'Suche';
    }

    public function getSearchResultsWhenHasAccess(array $terms): array {
        $code_href = $this->envUtils()->getCodeHref();
        $content = "{$this->getTitle()} - {$this->getDescription('Suche')}";
        return [
            ...$this->searchUtils()->getStaticSearchResults($content, $terms, [
                'link' => "{$code_href}suche?anfrage=Suche",
                'title' => $this->searchUtils()->highlight('Suche', $terms) ?: '?',
            ]),
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

        $sections = $this->searchUtils()->getSearchResults($terms);
        $has_results = false;
        foreach ($sections as $section) {
            if ($section['bestScore'] === null) {
                continue;
            }
            $has_results = true;
            $pretty_best_score = $this->authUtils()->hasPermission('all') ? " (Score: {$section['bestScore']})" : '';
            $out .= "<h2 class='bar green'>{$section['title']}{$pretty_best_score}</h2>";
            foreach ($section['results'] as $result) {
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
                $pretty_score = $this->authUtils()->hasPermission('all') ? " (Score: {$result['score']})" : '';
                $out .= OlzPostingListItem::render([
                    'link' => $result['link'],
                    'icon' => $result['icon'],
                    'date' => $pretty_date,
                    'title' => $this->searchUtils()->highlight($result['title'], $terms).$pretty_score,
                    'text' => $this->searchUtils()->highlight($result['text'] ?? '', $terms),
                ]);
            }
        }
        if (!$has_results) {
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
