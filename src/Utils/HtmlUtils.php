<?php

namespace Olz\Utils;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use Olz\Components\Users\OlzPopup\OlzPopup;
use Olz\Components\Users\OlzRoleInfoCard\OlzRoleInfoCard;
use Olz\Entity\Roles\Role;

class HtmlUtils {
    use WithUtilsTrait;

    public const PREFIX_REGEX = '<a ([^>]*)href=[\'"]mailto:';
    public const SUBJECT_REGEX = '\?subject=([^\'"]*)';
    public const SUFFIX_REGEX = '[\'"]([^>]*)>([^<@]*)([^<]*)<\/a>';
    public const OLZ_EMAIL_REGEX = '([A-Z0-9a-z._%+-]+)@olzimmerberg\.ch';
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
        $role_repo = $this->entityManager()->getRepository(Role::class);

        $html = str_replace(['<p>', '<p ', '</p>'], ['<div>', '<div ', '</div>'], $html);

        preg_match_all(
            '/'.self::PREFIX_REGEX.self::OLZ_EMAIL_REGEX.self::SUBJECT_REGEX.self::SUFFIX_REGEX.'/',
            $html,
            $matches,
        );
        for ($i = 0; $i < count($matches[0]); $i++) {
            $username = $matches[2][$i];
            // TODO: Only active roles!
            $role = $role_repo->findOneBy(['username' => $username]);
            if ($role) {
                $prefix = $this->getPrefix($matches[1][$i]);
                $username = preg_quote($username);
                $email = "{$username}@olzimmerberg.ch";
                $subject = $this->getSubject($matches[3][$i]);
                $suffix = $this->getSuffix($matches[4][$i], $matches[5][$i], $matches[6][$i]);
                $html = preg_replace(
                    "/{$prefix}{$email}{$subject}{$suffix}/",
                    OlzPopup::render([
                        'trigger' => "<a href='#' class='linkrole'>{$matches[5][$i]}</a>",
                        'popup' => OlzRoleInfoCard::render(['role' => $role]),
                    ]),
                    $html
                );
            }
        }

        preg_match_all(
            '/'.self::PREFIX_REGEX.self::OLZ_EMAIL_REGEX.self::SUFFIX_REGEX.'/',
            $html,
            $matches,
        );
        for ($i = 0; $i < count($matches[0]); $i++) {
            $username = $matches[2][$i];
            // TODO: Only active roles!
            $role = $role_repo->findOneBy(['username' => $username]);
            if ($role) {
                $prefix = $this->getPrefix($matches[1][$i]);
                $username = preg_quote($username);
                $email = "{$username}@olzimmerberg.ch";
                $suffix = $this->getSuffix($matches[3][$i], $matches[4][$i], $matches[5][$i]);
                $html = preg_replace(
                    "/{$prefix}{$email}{$suffix}/",
                    OlzPopup::render([
                        'trigger' => "<a href='#' class='linkrole'>{$matches[4][$i]}</a>",
                        'popup' => OlzRoleInfoCard::render(['role' => $role]),
                    ]),
                    $html
                );
            }
        }

        preg_match_all(
            '/(\s|^)'.self::OLZ_EMAIL_REGEX.'([\s,\.!\?]|$)/',
            $html,
            $matches,
        );
        for ($i = 0; $i < count($matches[0]); $i++) {
            $username = $matches[2][$i];
            // TODO: Only active roles!
            $role = $role_repo->findOneBy(['username' => $username]);
            if ($role) {
                $username = preg_quote($username);
                $email = "{$username}@olzimmerberg.ch";
                $html = preg_replace(
                    "/{$email}/",
                    OlzPopup::render([
                        'trigger' => "<a href='#' class='linkrole'>{$email}</a>",
                        'popup' => OlzRoleInfoCard::render(['role' => $role]),
                    ]),
                    $html
                );
            }
        }

        $html = preg_replace(
            '/'.self::PREFIX_REGEX.self::EMAIL_REGEX.self::SUBJECT_REGEX.self::SUFFIX_REGEX.'/',
            "<script>olz.MailTo(\"$2\", \"$3\", \"$6\" + \"$7\", \"$4\")</script>",
            $html
        );
        $html = preg_replace(
            '/'.self::PREFIX_REGEX.self::EMAIL_REGEX.''.self::SUFFIX_REGEX.'/',
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

    protected function getPrefix($match) {
        $esc_match = preg_quote($match);
        return "<a {$esc_match}href=['\"]mailto:";
    }

    protected function getSubject($match) {
        $esc_match = preg_quote($match);
        return "\\?subject={$esc_match}";
    }

    protected function getSuffix($match1, $match2, $match3) {
        $esc_match1 = preg_quote($match1);
        $esc_match2 = preg_quote($match2);
        $esc_match3 = preg_quote($match3);
        return "['\"]{$esc_match1}>{$esc_match2}{$esc_match3}<\\/a>";
    }

    public static function fromEnv(): self {
        return new self();
    }
}
