<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Common;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;

class FakeLazyCollection extends AbstractLazyCollection {
    private $array;

    public function __construct($collection) {
        $this->array = $collection;
    }

    protected function doInitialize(): void {
        $this->collection = new ArrayCollection($this->array);
    }
}
