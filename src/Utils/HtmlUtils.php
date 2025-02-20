<?php

namespace Olz\Utils;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use Olz\Entity\Roles\Role;
use Olz\Roles\Components\OlzRoleInfoModal\OlzRoleInfoModal;

class HtmlUtils {
    use WithUtilsTrait;

    public string $prefix_regex = '<a ([^>]*)href=[\'"]mailto:';
    public string $subject_regex = '\?subject=([^\'"]*)';
    public string $suffix_regex = '[\'"]([^>]*)>([^<@]*)([^<]*)<\/a>';
    public string $olz_email_regex = '';
    public string $email_regex = '([A-Z0-9a-z._%+-]+)@([A-Za-z0-9.-]+\.[A-Za-z]{2,64})';

    public function __construct() {
        $esc_host = preg_quote($this->envUtils()->getEmailForwardingHost());
        $this->olz_email_regex = '([A-Z0-9a-z._%+-]+)@'.$esc_host;
    }

    /** @param array<string, mixed> $override_config */
    public function renderMarkdown(string $markdown, array $override_config = []): string {
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

    public function postprocess(string $html): string {
        return $this->replaceEmailAdresses($html);
    }

    public function replaceEmailAdresses(string $html): string {
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $host = $this->envUtils()->getEmailForwardingHost();

        $html = str_replace(['<p>', '<p ', '</p>'], ['<div>', '<div ', '</div>'], $html);

        preg_match_all(
            "/{$this->prefix_regex}{$this->olz_email_regex}{$this->subject_regex}{$this->suffix_regex}/",
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
                $email = "{$username}@{$host}";
                $subject = $this->getSubject($matches[3][$i]);
                $suffix = $this->getSuffix($matches[4][$i], $matches[5][$i], $matches[6][$i]);
                $html = preg_replace(
                    "/{$prefix}{$email}{$subject}{$suffix}/",
                    $this->escapeDollar(OlzRoleInfoModal::render([
                        'role' => $role,
                        'text' => $matches[5][$i] ?: null,
                    ])),
                    $html
                );
            }
        }

        preg_match_all(
            "/{$this->prefix_regex}{$this->olz_email_regex}{$this->suffix_regex}/",
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
                $email = "{$username}@{$host}";
                $suffix = $this->getSuffix($matches[3][$i], $matches[4][$i], $matches[5][$i]);
                $html = preg_replace(
                    "/{$prefix}{$email}{$suffix}/",
                    $this->escapeDollar(OlzRoleInfoModal::render([
                        'role' => $role,
                        'text' => $matches[4][$i] ?: null,
                    ])),
                    $html
                );
            }
        }

        preg_match_all(
            "/(\\s|^){$this->olz_email_regex}([\\s,\\.!\\?]|$)/",
            $html,
            $matches,
        );
        for ($i = 0; $i < count($matches[0]); $i++) {
            $username = $matches[2][$i];
            // TODO: Only active roles!
            $role = $role_repo->findOneBy(['username' => $username]);
            if ($role) {
                $username = preg_quote($username);
                $email = "{$username}@{$host}";
                $html = preg_replace(
                    "/{$email}/",
                    $this->escapeDollar(OlzRoleInfoModal::render(['role' => $role])),
                    $html
                );
            }
        }

        $html = preg_replace(
            "/{$this->prefix_regex}{$this->email_regex}{$this->subject_regex}{$this->suffix_regex}/",
            "<script>olz.MailTo(\"\$2\", \"\$3\", \"\$6\" + \"\$7\", \"\$4\")</script>",
            $html
        );
        $html = preg_replace(
            "/{$this->prefix_regex}{$this->email_regex}{$this->suffix_regex}/",
            "<script>olz.MailTo(\"\$2\", \"\$3\", \"\$5\" + \"\$6\")</script>",
            $html
        );
        return preg_replace(
            "/(\\s|^){$this->email_regex}([\\s,\\.!\\?]|$)/",
            "\$1<script>olz.MailTo(\"\$2\", \"\$3\", \"E-Mail\")</script>\$4",
            $html
        );
    }

    /** @param array<string, string> $image_hrefs */
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

    protected function getPrefix(string $match): string {
        $esc_match = preg_quote($match);
        return "<a {$esc_match}href=['\"]mailto:";
    }

    protected function getSubject(string $match): string {
        $esc_match = preg_quote($match);
        return "\\?subject={$esc_match}";
    }

    protected function getSuffix(string $match1, string $match2, string $match3): string {
        $esc_match1 = preg_quote($match1);
        $esc_match2 = preg_quote($match2);
        $esc_match3 = preg_quote($match3);
        return "['\"]{$esc_match1}>{$esc_match2}{$esc_match3}<\\/a>";
    }

    protected function escapeDollar(string $replacement): string {
        return str_replace('$', '\$', $replacement);
    }

    public static function fromEnv(): self {
        return new self();
    }
}
