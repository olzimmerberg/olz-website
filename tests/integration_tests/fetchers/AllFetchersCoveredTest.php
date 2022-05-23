<?php

declare(strict_types=1);

require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @coversNothing
 */
final class AllFetchersCoveredTest extends IntegrationTestCase {
    public function testAllFetchersCovered(): void {
        $fetchers_path = __DIR__.'/../../../public/_/fetchers';

        $this->assertTrue(is_dir($fetchers_path));
        $fetchers_files = scandir($fetchers_path);
        $fetchers = array_filter(
            $fetchers_files,
            function ($filename) {
                return preg_match('/Fetcher\.php$/', $filename);
            },
        );
        $this->assertGreaterThan(0, count($fetchers));
        foreach ($fetchers as $fetcher) {
            $res = preg_match('/^([a-zA-Z0-9]+)Fetcher\.php$/', $fetcher, $matches);
            $expected_test_path = __DIR__."/{$matches[1]}FetcherTest.php";
            $this->assertSame(1, $res);
            $this->assertTrue(is_file($expected_test_path), "Expected test for {$fetcher}");
        }
    }
}
