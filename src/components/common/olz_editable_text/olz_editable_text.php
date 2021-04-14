<?php

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\HtmlRenderer;

require_once __DIR__.'/../../../config/vendor/autoload.php';

function olz_editable_text($args = []): string {
    global $entityManager, $code_href;
    require_once __DIR__.'/../../../config/doctrine_db.php';
    require_once __DIR__.'/../../../config/paths.php';
    require_once __DIR__.'/../../../model/index.php';
    require_once __DIR__.'/../../../utils/auth/AuthUtils.php';
    require_once __DIR__.'/../../../utils/client/HtmlUtils.php';

    $olz_text_id = intval($args['olz_text_id'] ?? 0);
    if ($olz_text_id > 0) {
        $olz_text_repo = $entityManager->getRepository(OlzText::class);
        $olz_text = $olz_text_repo->findOneBy(['id' => $olz_text_id]);

        $args['permission'] = "olz_text_{$olz_text_id}";
        $args['get_text'] = function () use ($olz_text) {
            return $olz_text ? $olz_text->getText() : '';
        };
        $args['endpoint'] = 'updateOlzText';
        $args['args'] = ['id' => $olz_text_id];
        $args['text_arg'] = 'text';
    }

    $auth_utils = AuthUtils::fromEnv();
    $has_access = $auth_utils->hasPermission($args['permission'] ?? 'any');

    $get_text_fn = $args['get_text'];
    $raw_markdown = $get_text_fn();

    $environment = Environment::createCommonMarkEnvironment();
    $environment->addExtension(new GithubFlavoredMarkdownExtension());
    $environment->setConfig([
        'html_input' => 'allow', // TODO: Do NOT allow!
        'allow_unsafe_links' => false,
        'max_nesting_level' => 100,
    ]);

    $parser = new DocParser($environment);
    $document = $parser->parse($raw_markdown);
    $html_renderer = new HtmlRenderer($environment);
    $rendered_html = $html_renderer->renderBlock($document);
    $html_utils = HtmlUtils::fromEnv();
    $sanitized_html = $html_utils->sanitize($rendered_html);

    if ($has_access) {
        $esc_endpoint = htmlentities(json_encode($args['endpoint']));
        $esc_args = htmlentities(json_encode($args['args']));
        $esc_text_arg = htmlentities(json_encode($args['text_arg']));
        return <<<ZZZZZZZZZZ
        <div class='olz-editable-text'>
            <div class='rendered-html'>
                <button
                    type='button'
                    onclick='olzEditableTextEdit(this)'
                    class='btn btn-link olz-edit-button'
                >
                    <img src='{$code_href}icns/edit_16.svg' alt='Bearbeiten' class='noborder' />
                </button>
                {$sanitized_html}
            </div>
            <div class='edit-markdown'>
                <form class='default-form' onsubmit='return olzEditableTextSubmit({$esc_endpoint}, {$esc_args},{$esc_text_arg}, this)'>
                    <textarea name='text'>{$raw_markdown}</textarea>
                    <div class='error-message alert alert-danger' role='alert'></div>
                    <div>
                        <button
                            type='button'
                            class='btn btn-secondary'
                            onclick='olzEditableTextCancel(this)'
                        >
                            Abbrechen
                        </button>
                        <button
                            type='submit'
                            class='btn btn-primary olz-edit-submit'
                        >
                            Speichern
                        </button>
                    </div>
                </form>
            </div>
        </div>
        ZZZZZZZZZZ;
    }
    return <<<ZZZZZZZZZZ
    <div class='olz-editable-text'>
        {$sanitized_html}
    </div>
    ZZZZZZZZZZ;
}
