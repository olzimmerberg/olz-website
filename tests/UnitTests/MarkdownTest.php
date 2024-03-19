<?php

declare(strict_types=1);

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
 *
 * @coversNothing
 */
final class MarkdownTest extends UnitTestCase {
    public function testMarkdown(): void {
        // Just in order to test that this *would* work.
        file_put_contents(__DIR__.'/dummy.md', '# Dummy file');

        $output = implode("\n", [
            shell_exec('git diff --name-only main'), // only tracked files
            shell_exec('git ls-files --others --exclude-standard'), // added files
        ]);
        $git_paths = preg_split('/\s+/', $output);
        $markdown_paths = [];
        foreach ($git_paths as $git_path) {
            $repo_root = __DIR__.'/../../';
            $path = "{$repo_root}{$git_path}";
            if (preg_match('/\.md$/', $git_path) && is_file($path)) {
                $markdown_paths[] = $git_path;
            }
        }

        // This works because of the dummy.md
        $this->assertGreaterThan(0, count($markdown_paths));

        foreach ($markdown_paths as $markdown_path) {
            $this->checkMarkdownFile($markdown_path);
        }

        // Clean up.
        unlink(__DIR__.'/dummy.md');
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
