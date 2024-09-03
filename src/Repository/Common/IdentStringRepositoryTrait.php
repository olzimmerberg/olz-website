<?php

namespace Olz\Repository\Common;

use Olz\Entity\Common\IdentStringEntityInterface;
use Olz\Utils\WithUtilsTrait;

/**
 * @template T of IdentStringEntityInterface
 *
 * @phpstan-require-implements IdentStringRepositoryInterface<T>
 */
trait IdentStringRepositoryTrait {
    use WithUtilsTrait;

    /**
     * @return ?T
     */
    public function findOneByIdent(string $ident): ?object {
        if (preg_match('/^[0-9]+$/', $ident)) {
            $entity = $this->findOneBy(['id' => intval($ident)]);
            if ($entity) {
                return $entity;
            }
        }
        $truncated_ident = substr($ident, 0, 63);
        $entity = $this->findOneBy(['ident' => $truncated_ident]);
        if ($entity) {
            return $entity;
        }
        $old_entity = $this->findOneBy(['old_ident' => $truncated_ident]);
        if ($old_entity) {
            return $old_entity;
        }
        return null;
    }

    /**
     * @param T $entity
     */
    public function setUniqueIdent(object $entity, string $ident): void {
        $truncated_ident = substr($ident, 0, 63);
        // It's OK if an entity has $ident as its old_ident => "override" that entity
        $existing_entity = $this->findOneBy(['ident' => $truncated_ident]);
        if ($existing_entity) {
            throw new DuplicateIdentStringException("Duplicate ident: {$truncated_ident}");
        }
        $entity->updateIdent($truncated_ident);
    }
}
