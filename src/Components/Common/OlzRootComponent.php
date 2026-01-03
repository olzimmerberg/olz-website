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
    abstract public function hasAccess(): bool;

    /** @return non-empty-string */
    abstract public function getSearchTitle(): string;

    /** @param array<string> $terms */
    public function searchSql(array $terms): ?string {
        if (!$this->hasAccess()) {
            return null;
        }
        return $this->searchSqlWhenHasAccess($terms);
    }

    /** @param array<string> $terms */
    public function searchSqlWhenHasAccess(array $terms): ?string {
        $called_class = get_called_class();
        throw new \Exception("{$called_class}::searchSqlWhenHasAccess is not implemented");
    }

    /** @param T $args */
    public function getHtml(mixed $args): string {
        if (!$this->hasAccess()) {
            $this->httpUtils()->dieWithHttpError(403);
        }
        return $this->getHtmlWhenHasAccess($args);
    }

    /** @param T $args */
    public function getHtmlWhenHasAccess(mixed $args): string {
        $called_class = get_called_class();
        throw new \Exception("{$called_class}::getHtmlWhenHasAccess is not implemented");
    }
}
