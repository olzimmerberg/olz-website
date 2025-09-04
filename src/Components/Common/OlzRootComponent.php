<?php

namespace Olz\Components\Common;

use Olz\Suche\Utils\SearchUtils;

/**
 * @phpstan-import-type SearchResult from SearchUtils
 *
 * @template T
 *
 * @extends OlzComponent<T>
 */
abstract class OlzRootComponent extends OlzComponent {
    /** @return non-empty-string */
    abstract public function getSearchTitle(): string;

    /**
     * @param array<string> $terms
     *
     * @return array<SearchResult>
     */
    abstract public function getSearchResults(array $terms): array;
}
