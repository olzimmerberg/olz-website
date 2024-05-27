<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel für Einsteiger an.
// =============================================================================

namespace Olz\Startseite\Components\OlzForBeginnersTile;

use Olz\Entity\Roles\Role;
use Olz\Entity\User;
use Olz\Repository\Roles\PredefinedRole;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzForBeginnersTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        return $user ? 0.0 : 1.0;
    }

    public function getHtml(array $args = []): string {
        $code_href = $this->envUtils()->getCodeHref();
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $fan_role = $role_repo->getPredefinedRole(PredefinedRole::FanOlzElite);

        $out = "<h2>Neu hier?</h2>";
        $out .= "<div>Willkommen bei <b>OL Zimmerberg</b>. Wir sind der <b>Orientierungslauf (OL) Sportverein</b> für die Region rund um den Zimmerberg am linken Zürichseeufer und im Sihltal.</div>";
        $out .= "<ul class='links'>";
        $out .= "<li><a href='{$code_href}fuer_einsteiger?von=startseite' class='linkint'>Für Einsteiger</a></li>";
        $out .= "<li><a href='{$code_href}fragen_und_antworten' class='linkint'>Häufige Fragen (FAQ)</a></li>";
        $out .= "<li><a href='{$code_href}verein' class='linkint'>Unser Verein</a></li>";
        $out .= $fan_role ? "<li><a href='{$code_href}verein/{$fan_role->getUsername()}' class='linkint'>Fan OLZ Elite</a></li>" : '';
        $out .= "</ul>";
        return $out;
    }
}
