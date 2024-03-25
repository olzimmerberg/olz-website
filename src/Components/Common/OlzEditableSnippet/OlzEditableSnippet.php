<?php

namespace Olz\Components\Common\OlzEditableSnippet;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\Snippets\Snippet;
use Olz\Repository\Snippets\PredefinedSnippet;

class OlzEditableSnippet extends OlzComponent {
    public function getHtml($args = []): string {
        $code_href = $this->envUtils()->getCodeHref();

        $id = $args['id'] ?? 0;
        if ($args['id'] instanceof PredefinedSnippet) {
            $id = $id->value;
        } else {
            $id = intval($id);
        }
        $esc_id = htmlentities(json_encode($id));
        $entityManager = $this->dbUtils()->getEntityManager();
        $snippet_repo = $entityManager->getRepository(Snippet::class);
        $snippet = $snippet_repo->findOneBy(['id' => $id]);

        $snippet_text = $snippet?->getText() ?? '';
        $snippet_html = $this->htmlUtils()->renderMarkdown($snippet_text);
        if ($snippet) {
            $snippet_html = $snippet->replaceImagePaths($snippet_html);
            $snippet_html = $snippet->replaceFilePaths($snippet_html);
        }

        $has_access = $this->authUtils()->hasPermission("snippet_{$id}");
        if (!$has_access) {
            return <<<ZZZZZZZZZZ
            <div class='olz-editable-snippet'>
                {$snippet_html}
            </div>
            ZZZZZZZZZZ;
        }

        return <<<ZZZZZZZZZZ
        <div class='olz-editable-snippet editable'>
            <button
                type='button'
                onclick='olz.olzEditableSnippetEditSnippet({$esc_id})'
                class='btn btn-link olz-edit-button'
            >
                <img src='{$code_href}assets/icns/edit_16.svg' alt='Bearbeiten' class='noborder' />
            </button>
            {$snippet_html}
        </div>
        ZZZZZZZZZZ;
    }
}
