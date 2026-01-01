<?php

namespace Olz\Anniversary\Components\OlzAnniversary;

use Doctrine\Common\Collections\Criteria;
use Olz\Anniversary\Components\OlzAnniversaryRocket\OlzAnniversaryRocket;
use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\OlzZielsprint\OlzZielsprint;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Anniversary\RunRecord;
use Olz\Entity\StravaLink;
use Olz\Repository\Snippets\PredefinedSnippet;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzAnniversaryParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzAnniversary extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function getSearchTitle(): string {
        return 'Jubiläumsjahr';
    }

    public function getSearchResultsWhenHasAccess(array $terms): array {
        return [];
    }

    public static string $title = "🎉 20 Jahre OL Zimmerberg 🥳";
    public static string $description = "Alle Aktivitäten und Informationen zum Jubiläumsjahr 2026.";

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzAnniversaryParams::class);

        $out = OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
            'norobots' => true,
        ]);
        $out .= <<<ZZZZZZZZZZ
            <div class='content-full olz-anniversary'>
                <h1>🎉 20 Jahre OL Zimmerberg 🥳</h1>
                <br>
                {$this->getRunsHtml()}
                {$this->getElevationStravaHtml()}
                <br>
                {$this->getZielsprintHtml()}
            </div>
            ZZZZZZZZZZ;

        $out .= OlzFooter::render();
        return $out;
    }

    protected function getRunsHtml(): string {
        $code_href = $this->envUtils()->getCodeHref();
        $user = $this->authUtils()->getCurrentUser();
        $out = '<h2>🏃 Höhenmeter-Challenge ⛰️</h2>';
        if (!$user) {
            $out .= "<p>😕 Du musst <a href='#login-dialog'>eingeloggt</a> sein, um an der Höhenmeter-Challenge teilzunehmen.</p>";
            return $out;
        }

        $out .= OlzEditableText::render(['snippet' => PredefinedSnippet::AnniversaryHoehenmeter]);

        $stats = $this->anniversaryUtils()->getElevationStats();

        $done_wid = \number_format(max(0, $stats['completion'] * 100), 2);
        $diff_wid = log10(abs($stats['diffDays']) + 1) * 25;
        $rocket = OlzAnniversaryRocket::render();
        $out .= <<<ZZZZZZZZZZ
                <div class='elevation-stats'>
                    <div class='done-range'></div>
                    <div class='done-bar' style='width: {$done_wid}%;'></div>
                    <div
                        class='rocket test-flaky'
                        style='left: {$done_wid}%;'
                        ondblclick='olz.handleRocketClick(this, event)'
                        ontouchstart='olz.handleRocketTap(this)'
                    >
                        {$rocket}
                    </div>
                    <div class='diff-range'></div>
                    <div class='diff-bar {$stats['diffKind']}' style='width: {$diff_wid}%;'></div>
                    <div class='marker' style='left: 12.72%;'></div>
                    <div class='marker' style='left: 27.42%;'></div>
                    <div class='marker' style='left: 42.47%;'></div>
                    <div class='marker' style='left: 50%;'></div>
                    <div class='marker' style='left: 57.53%;'></div>
                    <div class='marker' style='left: 72.58%;'></div>
                    <div class='marker' style='left: 87.28%;'></div>
                    <div class='marker-text' style='left: 12.72%;'>-1 Monat</div>
                    <div class='marker-text' style='left: 27.42%;'>-1 Woche</div>
                    <div class='marker-text' style='left: 42.47%;'>-1 Tag</div>
                    <div class='marker-text' style='left: 57.53%;'>+1 Tag</div>
                    <div class='marker-text' style='left: 72.58%;'>+1 Woche</div>
                    <div class='marker-text' style='left: 87.28%;'>+1 Monat</div>
                </div>
            ZZZZZZZZZZ;

        $out .= "<h3>Aktivitäten in den letzten 24 Stunden</h3>";
        $out .= <<<'ZZZZZZZZZZ'
            <div class='activities-table activities-24h'>
                <table>
                    <tr class='header'>
                        <td>Datum</td>
                        <td>Person</td>
                        <td>Quelle</td>
                        <td>Distanz</td>
                        <td>Höhenmeter</td>
                        <td>Steigung</td>
                        <td>Art</td>
                    </tr>
            ZZZZZZZZZZ;
        $runs_repo = $this->entityManager()->getRepository(RunRecord::class);
        $iso_now = $this->dateUtils()->getIsoNow();
        $minus_one_day = \DateInterval::createFromDateString("-24 hours");
        $one_day_ago = (new \DateTime($iso_now))->add($minus_one_day);
        $runs = $runs_repo->matching(Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->gt('run_at', $one_day_ago),
                Criteria::expr()->eq('on_off', 1),
            ))
            ->orderBy(['run_at' => 'DESC'])
            ->setFirstResult(0)
            ->setMaxResults(1000));
        foreach ($runs as $run) {
            $id = $run->getId();
            $json_id = json_encode($id);
            $date = $run->getRunAt()->format('d.m.Y H:i');
            $info = json_decode($run->getInfo(), true) ?? null;
            $is_counting_emoji = $run->getIsCounting() ? '✅' : '🚫';
            $is_counting_title = $run->getIsCounting() ? 'zählt' : 'zählt nicht';
            $name = $run->getRunnerName() ?? "?";
            $sport_type = "?";
            if (is_array($info)) {
                $sport_type = "{$info['type']} / {$info['sport_type']}";
            }
            $source = $this->anniversaryUtils()->getPrettySource($run->getSource() ?? '?');
            $distance_km = number_format($run->getDistanceMeters() / 1000, 2);
            $inclination_percent = $run->getDistanceMeters()
                ? number_format($run->getElevationMeters() * 100 / $run->getDistanceMeters(), 2)
                : 'NaN';
            $out .= <<<ZZZZZZZZZZ
                <tr>
                    <td>{$date}</td>
                    <td>{$name}</td>
                    <td>{$source}</td>
                    <td class='number'>{$distance_km}km</td>
                    <td class='number'><b>{$run->getElevationMeters()}m</b></td>
                    <td class='number'>{$inclination_percent}%</td>
                    <td><span title='{$is_counting_title}'>{$is_counting_emoji} {$sport_type}</span></td>
                </tr>
                ZZZZZZZZZZ;
        }
        $out .= "</table></div>";

        $out .= <<<ZZZZZZZZZZ
            <h3>
                Deine Aktivitäten (ohne Strava)
                <button
                    id='create-run-button'
                    class='btn btn-secondary'
                    onclick='return olz.initOlzEditRunModal()'
                >
                    <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                    Aktivität manuell hinzufügen
                </button>
            </h3>
            ZZZZZZZZZZ;
        $out .= <<<'ZZZZZZZZZZ'
            <div class='activities-table activities-manual'>
                <table>
                    <tr class='header'>
                        <td></td>
                        <td>Datum</td>
                        <td>Quelle</td>
                        <td>Distanz</td>
                        <td>Höhenmeter</td>
                        <td>Steigung</td>
                    </tr>
            ZZZZZZZZZZ;
        $runs_repo = $this->entityManager()->getRepository(RunRecord::class);
        $runs = $runs_repo->findBy(
            ['user' => $user, 'on_off' => 1],
            ['run_at' => 'DESC'],
        );
        foreach ($runs as $run) {
            $id = $run->getId();
            $json_id = json_encode($id);
            $date = $run->getRunAt()->format('d.m.Y H:i:s');
            $edit_button = $run->getSource() === 'manuell' ? <<<ZZZZZZZZZZ
                    <button
                        id='edit-run-{$id}-button'
                        class='btn btn-secondary-outline btn-sm edit-run-list-button'
                        onclick='return olz.olzAnniversaryEditRun({$json_id})'
                    >
                        <img src='{$code_href}assets/icns/edit_16.svg' class='noborder' />
                    </button>
                ZZZZZZZZZZ : '';
            $source = $this->anniversaryUtils()->getPrettySource($run->getSource() ?? '?');
            $distance_km = number_format($run->getDistanceMeters() / 1000, 2);
            $inclination_percent = number_format($run->getElevationMeters() * 100 / $run->getDistanceMeters(), 2);
            $out .= <<<ZZZZZZZZZZ
                <tr>
                    <td>{$edit_button}</td>
                    <td>{$date}</td>
                    <td>{$source}</td>
                    <td class='number'>{$distance_km}km</td>
                    <td class='number'><b>{$run->getElevationMeters()}m</b></td>
                    <td class='number'>{$inclination_percent}%</td>
                </tr>
                ZZZZZZZZZZ;
        }
        $out .= "</table></div>";
        return $out;
    }

    protected function getElevationStravaHtml(): string {
        $user = $this->authUtils()->getCurrentUser();
        if (!$user || !$this->authUtils()->hasPermission('anniversary', $user)) {
            return '';
        }
        $strava_link_repo = $this->entityManager()->getRepository(StravaLink::class);
        $strava_links = $strava_link_repo->findBy(['user' => $user]);
        $num_strava_links = count($strava_links);
        $redirect_url = "{$this->envUtils()->getBaseHref()}{$this->envUtils()->getCodeHref()}2026";
        $strava_url = $this->stravaUtils()->getRegistrationUrl(['read', 'activity:read'], $redirect_url);
        $out = "<div class='admin-only'><div class='admin-only-text'>Nur für Organisatoren sichtbar</div>";
        if ($num_strava_links === 0) {
            $out .= "<p>😕 Kein Strava-Konto verlinkt. <a href='{$strava_url}' class='linkext'>Jetzt mit Strava verbinden!</a></p>";
        } else {
            $out .= "<p>✅ Du bist mit diesen {$num_strava_links} Strava-Konten verbunden:</p><ul>";
            foreach ($strava_links as $strava_link) {
                $athlete_id = $strava_link->getStravaUser();
                $athlete_url = "https://www.strava.com/athletes/{$athlete_id}";
                $date = $strava_link->getLinkedAt()?->format('d.m.Y H:i:s');
                $out .= "<li><a href='{$athlete_url}'>{$athlete_id}</a> (erstellt: {$date})</li>";
            }
            $out .= "</ul>";
            $out .= "<p><a href='{$strava_url}'>Mit Strava verbinden</a></p>";
        }
        $out .= '</div>';
        return $out;
    }

    protected function getZielsprintHtml(): string {
        $out = '<h2>🏁 Zielsprint-Challenge 🏃</h2>';
        $out .= OlzEditableText::render(['snippet' => PredefinedSnippet::AnniversaryZielsprint]);
        $out .= OlzZielsprint::render();
        return $out;
    }
}
