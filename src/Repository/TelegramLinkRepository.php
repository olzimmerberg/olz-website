<?php

namespace Olz\Repository;

use Olz\Entity\TelegramLink;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<TelegramLink>
 */
class TelegramLinkRepository extends OlzRepository {
    protected string $entityClass = TelegramLink::class;

    /** @return array<TelegramLink> */
    public function getActivatedTelegramLinks(): array {
        $dql = "
            SELECT tl
            FROM {$this->entityClass} tl
            WHERE (
                tl.user IS NOT NULL
                AND tl.telegram_chat_id IS NOT NULL
            )";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
}
