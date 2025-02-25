<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Fetchers;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AllFetchersCoveredTest extends IntegrationTestCase {
    public function testAllFetchersCovered(): void {
        $fetchers_path = __DIR__.'/../../../src/Fetchers';

        $this->assertTrue(is_dir($fetchers_path));
        $fetchers_files = scandir($fetchers_path) ?: [];
        $fetchers = array_filter(
            $fetchers_files,
            fn ($filename) => (bool) preg_match('/Fetcher\.php$/', $filename),
        );
        $this->assertGreaterThan(0, count($fetchers));
        foreach ($fetchers as $fetcher) {
            $res = preg_match('/^([a-zA-Z0-9]+)Fetcher\.php$/', $fetcher, $matches);
            $fileident = $matches[1] ?? '';
            $expected_test_path = __DIR__."/{$fileident}FetcherTest.php";
            $this->assertSame(1, $res);
            $this->assertTrue(is_file($expected_test_path), "Expected test for {$fetcher}");
        }
    }
}
