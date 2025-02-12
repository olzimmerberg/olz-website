<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Common;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\Common\Collections\Selectable;

/**
 * @template T
 *
 * @extends AbstractLazyCollection<int, T>
 *
 * @implements Selectable<int, T>
 */
class FakeLazyCollection extends AbstractLazyCollection implements Selectable {
    /** @var array<T> */
    private array $array;

    /** @param array<T> $collection */
    public function __construct($collection) {
        $this->array = $collection;
    }

    protected function doInitialize(): void {
        $this->collection = new ArrayCollection($this->array);
    }

    public function matching(Criteria $criteria): ReadableCollection&Selectable {
        throw new \Exception('not implemented');
    }
}
