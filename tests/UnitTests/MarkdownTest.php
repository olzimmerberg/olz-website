<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests;

namespace Olz\Tests\UnitTests;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Parser\MarkdownParser;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 * @coversNothing
 */
final class MarkdownTest extends UnitTestCase {
    public function testMarkdown(): void {
        $output = shell_exec('git ls-tree -r HEAD --name-only');
        $git_paths = preg_split('/\s+/', $output);
        $markdown_paths = [];
        foreach ($git_paths as $git_path) {
            if (preg_match('/\.md$/', $git_path)) {
                $markdown_paths[] = $git_path;
            }
        }
        $this->assertGreaterThan(0, count($markdown_paths));
        foreach ($markdown_paths as $markdown_path) {
            $this->checkMarkdownFile($markdown_path);
        }
    }

    protected function checkMarkdownFile($path) {
        $repo_root = __DIR__.'/../../';
        $content = file_get_contents("{$repo_root}{$path}");
        $markdown_dir = dirname($path);

        $config = [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new AttributesExtension());
        $parser = new MarkdownParser($environment);

        $document = $parser->parse($content);
        foreach ($document->iterator() as $node) {
            if ($node instanceof Link) {
                $url = $node->getUrl();
                $is_absolute_url = preg_match('/^https?:/', $url);
                if (!$is_absolute_url) {
                    $url_path = "{$markdown_dir}/{$url}";
                    $this->assertTrue(
                        file_exists("{$repo_root}{$url_path}"),
                        "Broken relative link {$url_path} in {$path}",
                    );
                }
            }
        }
    }
}
