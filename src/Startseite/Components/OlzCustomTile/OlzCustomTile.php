<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit manuell eingegebenem Inhalt an.
// =============================================================================

namespace Olz\Startseite\Components\OlzCustomTile;

use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzCustomTile extends AbstractOlzTile {
    public const SNIPPET_ID = 24;

    public function getRelevance(?User $user): float {
        $is_empty = $this->getContent() === null;
        return ($is_empty) ? 0.0 : 0.9;
    }

    public function getHtml(array $args = []): string {
        $content = $this->getContent();
        return "<h2>Wichtig</h2><div>{$content}</div>";
    }

    protected function getContent(): ?string {
        $snippet_id = self::SNIPPET_ID;
        $has_access = $this->authUtils()->hasPermission("snippet_{$snippet_id}");
        $content = OlzEditableText::render(['snippet_id' => $snippet_id]);
        if (trim(strip_tags($content)) === '' && !$has_access) {
            return null;
        }
        return $content;
    }
}
