<?php

namespace Olz\Repository\Common;

use Olz\Entity\Common\IdentStringEntityInterface;

/**
 * @template T of IdentStringEntityInterface
 */
interface IdentStringRepositoryInterface {
    /**
     * @return ?T
     */
    public function findOneByIdent(string $ident): ?object;

    /**
     * @param T $entity
     */
    public function setUniqueIdent(object $entity, string $ident): void;

    /**
     * @param array<string, mixed>       $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return ?T
     */
    public function findOneBy(array $criteria, ?array $orderBy = null);
}
