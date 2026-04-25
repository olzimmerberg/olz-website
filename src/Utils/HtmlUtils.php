<?php

namespace Olz\Utils;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use Olz\Captcha\Components\OlzEmailModal\OlzEmailModal;
use Olz\Entity\Roles\Role;
use Olz\Roles\Components\OlzRoleInfoModal\OlzRoleInfoModal;

class HtmlUtils {
    use WithUtilsTrait;

    public string $subject_regex = '\?subject=([^\'"]*)';
    public string $olz_email_regex = '';
    public string $email_regex = '([A-Z0-9a-z._%+-]+)@([A-Za-z0-9.-]+\.[A-Za-z]{2,64})';

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
        $host = $this->envUtils()->getEmailForwardingHost();
        $esc_host = preg_quote($host);
        $this->olz_email_regex = '([A-Z0-9a-z._%+-]+)@'.$esc_host;

        $html = preg_replace(
            "/(\\s|^){$this->email_regex}([\\s,\\.!\\?]|$)/",
            "\$1<a href='mailto:\$2@\$3'></a>\$4",
            $html
        );
        $this->generalUtils()->checkNotNull($html, "String replacement failed");

        $doc = \DOM\HTMLDocument::createFromString(
            "<div id='root'>{$html}</div>",
            LIBXML_NOERROR
        );
        $links = [...$doc->getElementsByTagName('a')];
        foreach ($links as $link) {
            $this->replaceMailtoLink($link);
        }
        $root = $doc->getElementById('root');
        $this->generalUtils()->checkNotNull($root, 'Root not found anymore');
        return $this->getDOMNodeInnerHtml($root);
    }

    protected function replaceMailtoLink(\DOM\Element $link): void {
        $href_attr = $link->attributes->getNamedItem('href');
        if (!$href_attr) {
            return;
        }
        $olz_email_pattern = "/^mailto:{$this->olz_email_regex}(?:{$this->subject_regex})?\$/";
        $is_olz_email = preg_match($olz_email_pattern, $href_attr->value, $matches);
        if ($is_olz_email) {
            $username = $matches[1];
            // Note: Subject remains unused
            $role_repo = $this->entityManager()->getRepository(Role::class);
            $role = $role_repo->findOneBy(['username' => $username, 'on_off' => 1]);
            if (!$role) {
                $role = $role_repo->findOneBy(['old_username' => $username, 'on_off' => 1]);
                if ($role) {
                    $this->log()->notice("Old username {$role->getOldUsername()} of Role {$role->getId()} is still used. Use {$role->getUsername()} instead!");
                }
            }
            if ($role) {
                $text = $this->getDOMNodeInnerHtml($link) ?: null;
                if (preg_match("/{$this->email_regex}/", $text ?? '')) {
                    $text = null;
                }
                $this->replaceNodeWithHtml($link, OlzRoleInfoModal::render([
                    'role' => $role,
                    'text' => $text,
                ]));
                return;
            }
        }
        $email_pattern = "/^mailto:{$this->email_regex}(?:{$this->subject_regex})?\$/";
        $is_email = preg_match($email_pattern, $href_attr->value, $matches);
        if ($is_email) {
            $username = $matches[1];
            $domain = $matches[2];
            $subject = $matches[3] ?? null;
            $text = $this->getDOMNodeInnerHtml($link) ?: null;
            if (preg_match("/{$this->email_regex}/", $text ?? '')) {
                $text = null;
            }
            $this->replaceNodeWithHtml($link, OlzEmailModal::render([
                'email' => "{$username}@{$domain}",
                'text' => $text,
                'subject' => $subject ?: null,
            ]));
            return;
        }
    }

    protected function replaceNodeWithHtml(\DOM\Element $old, string $new): void {
        $replacement_nodes = $this->getDOMNode($new)->childNodes ?? [];
        $doc = $old->ownerDocument;
        $this->generalUtils()->checkNotNull($doc, 'No owner doc');
        foreach ($replacement_nodes as $replacement_node) {
            $imported_node = $doc->importNode($replacement_node, true);
            $old->parentNode?->insertBefore($imported_node, $old);
        }
        $old->parentNode?->removeChild($old);
    }

    protected function getDOMNodeInnerHtml(\DOM\Element $node): string {
        $innerHTML = "";
        $doc = $node->ownerDocument;
        if (!$doc instanceof \DOM\HTMLDocument) {
            throw new \Exception('No owner HTML doc');
        }
        $children = $node->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $doc->saveHTML($child) ?: '';
        }
        return $innerHTML;
    }

    protected function getDOMNode(string $html): ?\DOM\Node {
        $tmp_doc = \DOM\HTMLDocument::createFromString(
            "<div id='root'>{$html}</div>",
            LIBXML_NOERROR
        );
        return $tmp_doc->getElementById('root');
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
}
