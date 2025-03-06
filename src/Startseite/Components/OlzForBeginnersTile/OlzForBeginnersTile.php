<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel für Einsteiger an.
// =============================================================================

namespace Olz\Startseite\Components\OlzForBeginnersTile;

use Olz\Entity\Roles\Role;
use Olz\Entity\Users\User;
use Olz\Repository\Roles\PredefinedRole;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzForBeginnersTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        return $user ? 0.0 : 1.0;
    }

    public function getHtml(mixed $args): string {
        $code_href = $this->envUtils()->getCodeHref();
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $fan_role = $role_repo->getPredefinedRole(PredefinedRole::FanOlzElite);
        $fan_olz_elite = $fan_role ? "<li><a href='{$code_href}verein/{$fan_role->getUsername()}?von=startseite' class='linkint'>Fan OLZ Elite</a></li>" : '';

        return <<<ZZZZZZZZZZ
            <h3>Neu hier?</h3>
            <h1 class='welcome'>Willkommen bei <b>OL Zimmerberg</b>!</h1>
            <div>Wir sind euer <b>Orientierungslauf (OL) Sportverein</b> in Thalwil, Horgen, Wädenswil, Richterswil, Adliswil, Langnau am Albis, Kilchberg, Rüschlikon,  Oberrieden und Zürich-Süd.</div>
            <ul class='links'>
                <li><a href='{$code_href}fuer_einsteiger?von=startseite' class='linkint'>Für Einsteiger</a></li>
                <li><a href='{$code_href}fragen_und_antworten?von=startseite' class='linkint'>Häufige Fragen (FAQ)</a></li>
                <li><a href='{$code_href}verein?von=startseite' class='linkint'>Unser Verein</a></li>
                {$fan_olz_elite}
            </ul>
            ZZZZZZZZZZ;
    }
}
