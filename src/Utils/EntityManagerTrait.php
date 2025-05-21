<?php

namespace Olz\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait EntityManagerTrait {
    protected function entityManager(): EntityManagerInterface {
        $util = WithUtilsCache::get('entityManager');
        assert($util);
        return $util;
    }

    #[Required]
    public function setEntityManager(EntityManagerInterface $new): void {
        WithUtilsCache::set('entityManager', $new);
    }
}
