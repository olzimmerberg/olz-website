<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit manuell eingegebenem Inhalt an.
// =============================================================================

namespace Olz\Startseite\Components\OlzCustomTile;

use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzCustomTile extends AbstractOlzTile {
    public const OLZ_TEXT_ID = 24;

    public function getRelevance(?User $user): float {
        $olz_text_id = self::OLZ_TEXT_ID;
        $has_access = $this->authUtils()->hasPermission("olz_text_{$olz_text_id}");
        $is_empty = $this->getContent() === null;
        return (!$has_access && $is_empty) ? 0.0 : 0.9;
    }

    public function getHtml($args = []): string {
        $content = $this->getContent();
        return "<h2>Wichtig</h2><div>{$content}</div>";
    }

    protected function getContent(): ?string {
        $content = OlzEditableText::render(['olz_text_id' => self::OLZ_TEXT_ID]);
        if (trim(strip_tags($content)) === '') {
            return null;
        }
        return $content;
    }
}
