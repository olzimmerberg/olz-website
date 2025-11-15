<?php

namespace Olz\Anniversary\Components\OlzAnniversary;

use Olz\Anniversary\Components\OlzAnniversaryRocket\OlzAnniversaryRocket;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Anniversary\RunRecord;
use Olz\Entity\StravaLink;
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
        $rocket = OlzAnniversaryRocket::render();
        $out .= <<<ZZZZZZZZZZ
            <div class='content-full olz-anniversary'>
                <h1>🎉 20 Jahre OL Zimmerberg 🥳</h1>
                {$this->getRunsHtml()}
                {$this->getElevationStravaHtml()}
                {$rocket}
                TODO
            </div>
            ZZZZZZZZZZ;

        $out .= OlzFooter::render();
        return $out;
    }

    protected function getElevationStravaHtml(): string {
        $user = $this->authUtils()->getCurrentUser();
        if (!$user) {
            return "<p>😕 Du musst eingeloggt sein, um an der Höhenmeter-Challenge teilzunehmen.</p>";
        }
        $strava_link_repo = $this->entityManager()->getRepository(StravaLink::class);
        $strava_links = $strava_link_repo->findBy(['user' => $user]);
        $num_strava_links = count($strava_links);
        $redirect_url = "{$this->envUtils()->getBaseHref()}{$this->envUtils()->getCodeHref()}2026";
        $strava_url = $this->stravaUtils()->getRegistrationUrl(['read', 'activity:read'], $redirect_url);
        $out = '';
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
        return $out;
    }

    protected function getRunsHtml(): string {
        $code_href = $this->envUtils()->getCodeHref();
        $user = $this->authUtils()->getCurrentUser();
        if (!$user) {
            return "<p>😕 Du musst eingeloggt sein, um an der Höhenmeter-Challenge teilzunehmen.</p>";
        }
        $out = '';
        $out .= <<<ZZZZZZZZZZ
            <p>
                Deine Aktivitäten:
                <button
                    id='create-run-button'
                    class='btn btn-secondary'
                    onclick='return olz.initOlzEditRunModal()'
                >
                    <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                    Aktivität manuell hinzufügen
                </button>
            </p>
            ZZZZZZZZZZ;
        $out .= "<table class='activities-table'><tr class='header'><td></td><td>Datum</td><td>Quelle</td><td>Höhenmeter</td></tr>";
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
            $out .= <<<ZZZZZZZZZZ
                <tr>
                    <td>{$edit_button}</td>
                    <td>{$date}</td>
                    <td>{$run->getSource()}</td>
                    <td>{$run->getElevationMeters()}m</td>
                </tr>
                ZZZZZZZZZZ;
        }
        $out .= "</table>";
        return $out;
    }
}
