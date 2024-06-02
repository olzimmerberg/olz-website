<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Common;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @template T
 *
 * @extends AbstractLazyCollection<int, T>
 */
class FakeLazyCollection extends AbstractLazyCollection {
    /** @var array<T> */
    private array $array;

    /** @param array<T> $collection */
    public function __construct($collection) {
        $this->array = $collection;
    }

    protected function doInitialize(): void {
        $this->collection = new ArrayCollection($this->array);
    }
}
