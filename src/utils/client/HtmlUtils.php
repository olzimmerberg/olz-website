<?php

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

require_once __DIR__.'/../../config/vendor/autoload.php';

class HtmlUtils {
    public const EMAIL_REGEX = '([A-Z0-9a-z._%+-]+)@([A-Za-z0-9.-]+\\.[A-Za-z]{2,64})';

    public function renderMarkdown($markdown, $override_config = []) {
        $default_config = [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
        ];
        $config = array_merge($default_config, $override_config);

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new AttributesExtension());
        $converter = new MarkdownConverter($environment);
        $rendered = $converter->convertToHtml($markdown);
        return $this->sanitize(strval($rendered));
    }

    public function sanitize($html) {
        return $this->replaceEmailAdresses($html);
    }

    public function replaceEmailAdresses($html) {
        $html = preg_replace(
            '/<a ([^>]*)href=[\'"]mailto:'.self::EMAIL_REGEX.'\?subject=([^\'"]*)[\'"]([^>]*)>([^<]*)<\/a>/',
            "<script>MailTo(\"$2\", \"$3\", \"$6\", \"$4\")</script>",
            $html
        );
        $html = preg_replace(
            '/<a ([^>]*)href=[\'"]mailto:'.self::EMAIL_REGEX.'[\'"]([^>]*)>([^<]*)<\/a>/',
            "<script>MailTo(\"$2\", \"$3\", \"$5\")</script>",
            $html
        );
        return preg_replace(
            '/(\s|^)'.self::EMAIL_REGEX.'([\s,\.!\?]|$)/',
            "$1<script>MailTo(\"$2\", \"$3\", \"E-Mail\")</script>$4",
            $html
        );
    }

    public static function fromEnv() {
        return new self();
    }
}
