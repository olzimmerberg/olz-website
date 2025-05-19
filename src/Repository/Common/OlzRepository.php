<?php

namespace Olz\Repository\Common;

use Doctrine\ORM\EntityRepository;
use Olz\Utils\WithUtilsTrait;

/**
 * @template T of object
 *
 * @extends EntityRepository<T>
 */
class OlzRepository extends EntityRepository {
    use WithUtilsTrait;

    /** @var class-string<T> */
    protected string $entityClass;
}
