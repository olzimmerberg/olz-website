<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit manuell eingegebenem Inhalt an.
// =============================================================================

namespace Olz\Startseite\Components\OlzAnniversaryTile;

use Olz\Anniversary\Components\OlzAnniversaryRocket\OlzAnniversaryRocket;
use Olz\Entity\Users\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzAnniversaryTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        if (!$this->authUtils()->hasPermission('anniversary', $user)) {
            return 0.0;
        }
        return ((bool) $user) ? 0.91 : 0.01;
    }

    public function getHtml(mixed $args): string {
        $code_href = $this->envUtils()->getCodeHref();

        $value = 0.33; // TODO: Read actual value
        $done_hei = intval($value * 160);
        $rocket_hei = intval($value * 160) - 30;
        $rocket = OlzAnniversaryRocket::render();
        return <<<ZZZZZZZZZZ
            <a href='{$code_href}2026' class='anniversary-container'>
                <h3 class='anniversary-h3'>🥳 20 Jahre OL Zimmerberg</h3>
                <div class='all-bar'></div>
                <div class='done-bar' style='height: {$done_hei}px;'></div>
                <div class='rocket test-flaky' style='bottom: {$rocket_hei}px;'>{$rocket}</div>
            </a>
            ZZZZZZZZZZ;
    }
}
