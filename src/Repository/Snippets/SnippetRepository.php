<?php

namespace Olz\Repository\Snippets;

use Olz\Entity\Snippets\Snippet;
use Olz\Repository\Common\OlzRepository;

enum PredefinedSnippet: int {
    // The int value is the id.
    case TrainingTypesInfo = 1;
    case TermineDownloadsLinks = 2;
    case TermineNewsletter = 3;
    case ForumRules = 4;
    case ZielsprintInfo = 9;
    case KartenbestellungInfo = 12;
    case StartseiteBanner = 22;
    case CustomTileContent = 24;
}

class SnippetRepository extends OlzRepository {
    public function getPredefinedSnippet(PredefinedSnippet $predefined_snippet): null|Snippet {
        $snippet = $this->findOneBy(['id' => $predefined_snippet->value]);
        if (!$snippet) {
            $this->log()->warning("Predefined snippet does not exist: {$predefined_snippet->value}");
        }
        return $snippet;
    }
}
