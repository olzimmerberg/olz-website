<?php

namespace Olz\Anniversary\Components\OlzAnniversary;

use Doctrine\Common\Collections\Criteria;
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
        ]);
        $out .= <<<ZZZZZZZZZZ
            <div class='content-full olz-anniversary'>
                <h1>🎉 20 Jahre OL Zimmerberg 🥳</h1>
                {$this->getRunsHtml()}
                {$this->getElevationStravaHtml()}
                {$this->getZielsprintHtml()}
            </div>
            ZZZZZZZZZZ;

        $out .= OlzFooter::render();
        return $out;
    }

    protected function getRunsHtml(): string {
        $code_href = $this->envUtils()->getCodeHref();
        $user = $this->authUtils()->getCurrentUser();
        $out = '<h2>Höhenmeter-Challenge</h2>';
        if (!$user) {
            $out .= "<p>😕 Du musst <a href='#login-dialog'>eingeloggt</a> sein, um an der Höhenmeter-Challenge teilzunehmen.</p>";
            return $out;
        }

        $out .= OlzEditableText::render(['snippet' => PredefinedSnippet::AnniversaryHoehenmeter]);

        $out .= "<h3>Aktivitäten in den letzten 24 Stunden</h3>";
        $out .= <<<'ZZZZZZZZZZ'
            <table class='activities-table'>
                <tr class='header'>
                    <td></td>
                    <td>Datum</td>
                    <td>Quelle</td>
                    <td>Distanz</td>
                    <td>Höhenmeter</td>
                </tr>
            ZZZZZZZZZZ;
        $runs_repo = $this->entityManager()->getRepository(RunRecord::class);
        $runs = $runs_repo->matching(Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->gt('run_at', new \DateTime('-14 hours')),
                Criteria::expr()->eq('on_off', 1),
            ))
            ->orderBy(['run_at' => 'DESC'])
            ->setFirstResult(0)
            ->setMaxResults(1));
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
            $distance_km = number_format($run->getDistanceMeters() / 1000, 2);
            $out .= <<<ZZZZZZZZZZ
                <tr>
                    <td>{$edit_button}</td>
                    <td>{$date}</td>
                    <td>{$run->getSource()}</td>
                    <td>{$distance_km}km</td>
                    <td>{$run->getElevationMeters()}m</td>
                </tr>
                ZZZZZZZZZZ;
        }
        $out .= "</table>";

        $out .= <<<ZZZZZZZZZZ
            <h3>
                Deine Aktivitäten
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
            <table class='activities-table'>
                <tr class='header'>
                    <td></td>
                    <td>Datum</td>
                    <td>Quelle</td>
                    <td>Distanz</td>
                    <td>Höhenmeter</td>
                </tr>
            ZZZZZZZZZZ;
        $runs_repo = $this->entityManager()->getRepository(RunRecord::class);
        $runs = $runs_repo->findBy(['user' => $user], ['run_at' => 'DESC']);
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
            $distance_km = number_format($run->getDistanceMeters() / 1000, 2);
            $out .= <<<ZZZZZZZZZZ
                <tr>
                    <td>{$edit_button}</td>
                    <td>{$date}</td>
                    <td>{$run->getSource()}</td>
                    <td>{$distance_km}km</td>
                    <td>{$run->getElevationMeters()}m</td>
                </tr>
                ZZZZZZZZZZ;
        }
        $out .= "</table>";
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
        $out = '<h2>Zielsprint-Challenge</h2>';
        $out .= OlzEditableText::render(['snippet' => PredefinedSnippet::AnniversaryZielsprint]);
        $out .= OlzZielsprint::render();
        return $out;
    }
}
