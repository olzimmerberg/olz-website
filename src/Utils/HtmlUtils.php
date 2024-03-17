<?php

namespace Olz\Utils;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class HtmlUtils {
    use WithUtilsTrait;
    public const UTILS = [];

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
        $rendered = $converter->convert($markdown);
        $postprocessed = $this->postprocess(strval($rendered));
        return "<div class='rendered-markdown'>{$postprocessed}</div>";
    }

    public function postprocess($html) {
        return $this->replaceEmailAdresses($html);
    }

    public function replaceEmailAdresses($html) {
        $html = preg_replace(
            '/<a ([^>]*)href=[\'"]mailto:'.self::EMAIL_REGEX.'\?subject=([^\'"]*)[\'"]([^>]*)>([^<@]*)([^<]*)<\/a>/',
            "<script>olz.MailTo(\"$2\", \"$3\", \"$6\" + \"$7\", \"$4\")</script>",
            $html
        );
        $html = preg_replace(
            '/<a ([^>]*)href=[\'"]mailto:'.self::EMAIL_REGEX.'[\'"]([^>]*)>([^<@]*)([^<]*)<\/a>/',
            "<script>olz.MailTo(\"$2\", \"$3\", \"$5\" + \"$6\")</script>",
            $html
        );
        return preg_replace(
            '/(\s|^)'.self::EMAIL_REGEX.'([\s,\.!\?]|$)/',
            "$1<script>olz.MailTo(\"$2\", \"$3\", \"E-Mail\")</script>$4",
            $html
        );
    }

    public function getImageSrcHtml(array $image_hrefs): string {
        $keys = array_keys($image_hrefs);
        if (count($keys) < 1) {
            return '';
        }
        $default_src = $image_hrefs['1x'] ?? $image_hrefs[$keys[0]];
        if (count($keys) < 2) {
            return <<<ZZZZZZZZZZ
            src='{$default_src}'
            ZZZZZZZZZZ;
        }
        $srcset = implode(",\n    ", array_map(function ($key) use ($image_hrefs) {
            $value = $image_hrefs[$key];
            return "{$value} {$key}";
        }, $keys));
        return <<<ZZZZZZZZZZ
        srcset='
            {$srcset}
        '
        src='{$default_src}'
        ZZZZZZZZZZ;
    }
}
