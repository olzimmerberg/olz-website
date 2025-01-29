<?php

namespace Olz\Components\Common\OlzEditableText;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\Snippets\Snippet;

/** @extends OlzComponent<array<string, mixed>> */
class OlzEditableText extends OlzComponent {
    public function getHtml(mixed $args): string {
        $code_href = $this->envUtils()->getCodeHref();

        $snippet_id = intval($args['snippet_id'] ?? 0);
        $esc_id = htmlentities(json_encode($snippet_id));
        $entityManager = $this->dbUtils()->getEntityManager();
        $snippet_repo = $entityManager->getRepository(Snippet::class);
        $snippet = $snippet_repo->findOneBy(['id' => $snippet_id]);

        $snippet_text = $snippet?->getText() ?? '';
        $snippet_html = $this->htmlUtils()->renderMarkdown($snippet_text);
        if ($snippet) {
            $snippet_html = $snippet->replaceImagePaths($snippet_html);
            $snippet_html = $snippet->replaceFilePaths($snippet_html);
        }

        $has_access = $this->authUtils()->hasPermission("snippet_{$snippet_id}");
        if (!$has_access) {
            return <<<ZZZZZZZZZZ
                <div class='olz-editable-text'>
                    {$snippet_html}
                </div>
                ZZZZZZZZZZ;
        }

        return <<<ZZZZZZZZZZ
            <div class='olz-editable-text editable'>
                <button
                    type='button'
                    onclick='olz.olzEditableTextEditSnippet({$esc_id})'
                    class='btn btn-link olz-edit-button'
                >
                    <img src='{$code_href}assets/icns/edit_16.svg' alt='Bearbeiten' class='noborder' />
                </button>
                {$snippet_html}
            </div>
            ZZZZZZZZZZ;
    }
}
