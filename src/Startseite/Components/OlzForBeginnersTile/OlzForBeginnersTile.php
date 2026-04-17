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
            <div>
                Wir sind euer <b>Orientierungslauf (OL) Sportverein</b> in 
                {$this->getSektionLink('sektion-thalwil', 'Thalwil')},
                {$this->getSektionLink('sektion-horgen', 'Horgen')},
                {$this->getSektionLink('sektion-waedenswil', 'Wädenswil')},
                {$this->getSektionLink('sektion-richterswil', 'Richterswil')},
                {$this->getSektionLink('sektion-adliswil', 'Adliswil')},
                {$this->getSektionLink('sektion-langnau', 'Langnau am Albis')},
                {$this->getSektionLink('sektion-kilchberg', 'Kilchberg')},
                {$this->getSektionLink('sektion-rueschlikon', 'Rüschlikon')},
                {$this->getSektionLink('sektion-oberrieden', 'Oberrieden')} und 
                {$this->getSektionLink('sektion-zuerich', 'Zürich-Süd')}.
            </div>
            <ul class='links two-columns'>
                <li><a href='{$code_href}fuer_einsteiger?von=startseite' class='linkint'><b>Für Einsteiger</b></a></li>
                <li><a href='{$code_href}fragen_und_antworten?von=startseite' class='linkint'>Häufige Fragen</a></li>
                <li><a href='{$code_href}verein?von=startseite' class='linkint'>Unser Verein</a></li>
                {$fan_olz_elite}
            </ul>
            <h4>Angebot</h4>
            <div class='filters'>
                <div class='filter'><a href='{$code_href}angebot/anfaenger?von=startseite'>
                    für Anfänger
                </a></div>
                <div class='filter'><a href='{$code_href}angebot/schulen?von=startseite'>
                    für Schulen
                </a></div>
                <div class='filter'><a href='{$code_href}angebot/mitglieder?von=startseite'>
                    für Mitglieder
                </a></div>
            </div>
            ZZZZZZZZZZ;
    }

    protected function getSektionLink(string $username, string $name): string {
        $code_href = $this->envUtils()->getCodeHref();
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $role = $role_repo->findOneBy(['username' => $username]);
        if ($role === null) {
            return $name;
        }
        return <<<ZZZZZZZZZZ
            <a href='{$code_href}verein/{$role->getUsername()}'>
                {$name}
            </a>
            ZZZZZZZZZZ;
    }
}
