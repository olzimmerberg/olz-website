<?php

namespace Olz\Roles\Components\OlzVerein;

use Olz\Common\Components\OlzFooter\OlzFooter;
use Olz\Common\Components\OlzHeader\OlzHeader;
use Olz\Common\Components\OlzRootComponent;
use Olz\Common\Utils\HttpParams;
use Olz\Entity\Measurement;
use Olz\Roles\Components\OlzOrganigramm\OlzOrganigramm;

/** @extends HttpParams<array{von?: ?string}> */
class OlzVereinParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzVerein extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function searchSqlWhenHasAccess(array $terms): string|array|null {
        $code_href = $this->envUtils()->getCodeHref();
        return $this->searchUtils()->getStaticResultQuery([
            'link' => "{$code_href}karten",
            'icon' => "{$code_href}assets/icns/link_map_16.svg",
            'title' => $this->getPageTitle(),
            'text' => $this->getPageDescription(),
        ], $terms);
    }

    public function getPageTitle(): string {
        return "Verein";
    }

    public function getPageDescription(): string {
        return "Die wichtigsten Kontaktadressen und eine Liste aller Vereinsorgane der OL Zimmerberg.";
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzVereinParams::class);
        $code_href = $this->envUtils()->getCodeHref();

        $out = OlzHeader::render([
            'title' => $this->getPageTitle(),
            'description' => $this->getPageDescription(),
            'canonical_url' => "{$code_href}verein",
        ]);

        $db = $this->dbUtils()->getDb();
        $result = $db->query("SELECT id, name FROM roles WHERE featured_position IS NOT NULL ORDER BY featured_position ASC");
        $featured_out = '';
        // @phpstan-ignore-next-line
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $title = $row['name'];
            $featured_out .= "<div><b><a href='javascript:olz.highlightOrganigramm(&quot;role-{$id}&quot;)' class='linkint'>{$title}</a></b></div>";
        }
        $out .= "<div class='content-full'><h1>Verein</h1><div id='organigramm'>";
        $out .= <<<ZZZZZZZZZZ
            <h2>Häufig gesucht</h2>
            {$featured_out}
            <div>
                <br />
                <div><b>PC-Konto</b></div>
                <div><b>IBAN: </b>CH91 0900 0000 8525 6448 8</div>
                <div><b>Empfänger: </b>OL Zimmerberg, 8800 Thalwil</div>
            </div>
            ZZZZZZZZZZ;

        $out .= OlzOrganigramm::render();

        $out .= "</div></div>";

        if ($this->authUtils()->hasPermission('vorstand')) {
            $out .= $this->getStatistikDiagram();
        }

        $out .= OlzFooter::render();
        return $out;
    }

    private function getStatistikDiagram(): string {
        $current_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        $current_month = intval($this->dateUtils()->getCurrentDateInFormat('m'));
        $min_year = $current_year - 19;
        $measurement_repo = $this->entityManager()->getRepository(Measurement::class);

        $series = [
            'num_users' => ['label' => 'Benutzer', 'color' => '#0077cc'],
            'num_members' => ['label' => 'Mitglieder', 'color' => '#008800'],
            'num_member_users' => ['label' => 'Benutzer mit Mitgliedschaft', 'color' => '#cc6600'],
        ];

        // 0 = January of $min_year; the chart covers the last 20 years up to the current month.
        $end_index = ($current_year - $min_year) * 12 + ($current_month - 1);

        // name => [month index => value]
        $values = [];
        foreach (array_keys($series) as $name) {
            $measurements = $measurement_repo->findBy(
                ['name' => $name],
                ['year' => 'ASC', 'month' => 'ASC'],
            );
            foreach ($measurements as $measurement) {
                $year = $measurement->getYear();
                $month = $measurement->getMonth();
                $value = $measurement->getValue();
                if ($year === null || $month === null || $value === null) {
                    continue;
                }
                $index = ($year - $min_year) * 12 + ($month - 1);
                if ($index < 0 || $index > $end_index) {
                    continue;
                }
                $values[$name][$index] = $value;
            }
        }

        $px_per_month = 3;
        $height = 320;
        $pad_left = 60;
        $pad_right = 220;
        $pad_top = 20;
        $pad_bottom = 40;
        $plot_height = $height - $pad_top - $pad_bottom;
        $plot_width = max(1, $end_index) * $px_per_month;
        $width = $pad_left + $plot_width + $pad_right;

        $max_value = 1;
        foreach ($values as $by_month) {
            foreach ($by_month as $value) {
                if ($value > $max_value) {
                    $max_value = $value;
                }
            }
        }
        $step = max(1, intval(ceil($max_value / 4)));
        $max_y = $step * 4;

        $x_of = fn (int $index): float => $pad_left + $index * $px_per_month;
        $y_of = fn (float $value): float => $pad_top + (1 - $value / $max_y) * $plot_height;

        $svg = "<svg class='olz-verein-statistik' width='{$width}' height='{$height}' viewBox='0 0 {$width} {$height}' xmlns='http://www.w3.org/2000/svg' role='img' aria-label='Entwicklung Benutzer und Mitglieder'>";

        // Horizontal grid lines and Y labels.
        for ($i = 0; $i <= 4; $i++) {
            $tick_value = $step * $i;
            $y = round($y_of($tick_value), 1);
            $svg .= "<line x1='{$pad_left}' y1='{$y}' x2='".($pad_left + $plot_width)."' y2='{$y}' stroke='#dddddd' stroke-width='1' />";
            $svg .= "<text x='".($pad_left - 8)."' y='".($y + 4)."' text-anchor='end' font-size='11' fill='#666666'>{$tick_value}</text>";
        }

        // Vertical grid lines at the start (January) of each year.
        for ($year = $min_year; $year <= $current_year; $year++) {
            $index = ($year - $min_year) * 12;
            if ($index > $end_index) {
                continue;
            }
            $x = round($x_of($index), 1);
            $svg .= "<line x1='{$x}' y1='{$pad_top}' x2='{$x}' y2='".($pad_top + $plot_height)."' stroke='#dddddd' stroke-width='1' />";
        }

        // Axes.
        $svg .= "<line x1='{$pad_left}' y1='{$pad_top}' x2='{$pad_left}' y2='".($pad_top + $plot_height)."' stroke='#999999' stroke-width='1' />";
        $svg .= "<line x1='{$pad_left}' y1='".($pad_top + $plot_height)."' x2='".($pad_left + $plot_width)."' y2='".($pad_top + $plot_height)."' stroke='#999999' stroke-width='1' />";

        // X labels at January of every year.
        for ($year = $min_year; $year <= $current_year; $year++) {
            $index = ($year - $min_year) * 12;
            if ($index > $end_index) {
                continue;
            }
            $x = round($x_of($index), 1);
            $svg .= "<text x='{$x}' y='".($pad_top + $plot_height + 18)."' text-anchor='middle' font-size='10' fill='#666666'>{$year}</text>";
        }

        // Series lines, points and legend.
        $legend_x = $pad_left + $plot_width + 20;
        $legend_y = $pad_top + 10;
        foreach ($series as $name => $meta) {
            $color = $meta['color'];
            $label = $meta['label'];
            $points = '';
            foreach (($values[$name] ?? []) as $index => $value) {
                $x = round($x_of($index), 1);
                $y = round($y_of($value), 1);
                $points .= "{$x},{$y} ";
            }
            if ($points !== '') {
                $svg .= "<polyline points='".trim($points)."' fill='none' stroke='{$color}' stroke-width='2' />";
            }
            $svg .= "<rect x='{$legend_x}' y='{$legend_y}' width='14' height='14' fill='{$color}' />";
            $svg .= "<text x='".($legend_x + 20)."' y='".($legend_y + 12)."' font-size='12' fill='#333333'>{$label}</text>";
            $legend_y += 22;
        }

        $svg .= "</svg>";

        return "<div class='content-full'>"
            ."<h2>Entwicklung Benutzer und Mitglieder</h2>"
            ."<p>Anzahl aktiver Benutzer, Mitglieder und Benutzer mit Mitgliedschaft, pro Monat (letzte 20 Jahre).</p>"
            ."<div style='overflow-x: auto;'>".$svg."</div>"
            ."</div>";
    }
}
