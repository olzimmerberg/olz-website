<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit manuell eingegebenem Inhalt an.
// =============================================================================

namespace Olz\Startseite\Components\OlzCustomTile;

use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Entity\Users\User;
use Olz\Repository\Snippets\PredefinedSnippet;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzCustomTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        $is_empty = $this->getContent() === null;
        return ($is_empty) ? 0.0 : 0.9;
    }

    public function getHtml(mixed $args): string {
        $content = $this->getContent();
        return "<h3>Wichtig</h3><div>{$content}</div>";
    }

    protected function getContent(): ?string {
        $snippet = PredefinedSnippet::StartseiteCustomTile;
        $has_access = $this->authUtils()->hasPermission("snippet_{$snippet->value}");
        $content = OlzEditableText::render(['snippet' => $snippet]);
        if (trim(strip_tags($content)) === '' && !$has_access) {
            return null;
        }
        return $content;
    }
}
