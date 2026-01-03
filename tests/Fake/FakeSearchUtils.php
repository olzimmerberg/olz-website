<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Suche\Utils\SearchUtils;

class FakeSearchUtils extends SearchUtils {
    public function getPageSearchResultsNew(string $page_class, array $terms): array {
        return [
            'title' => 'Fake title',
            'bestScore' => null,
            'results' => [],
        ];
    }
}
