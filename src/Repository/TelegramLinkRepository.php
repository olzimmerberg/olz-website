<?php

namespace Olz\Repository;

use Olz\Entity\TelegramLink;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<TelegramLink>
 */
class TelegramLinkRepository extends OlzRepository {
    protected string $telegram_link_class = TelegramLink::class;

    /** @return array<TelegramLink> */
    public function getActivatedTelegramLinks(): array {
        $dql = "
            SELECT tl
            FROM {$this->telegram_link_class} tl
            WHERE (
                tl.user IS NOT NULL
                AND tl.telegram_chat_id IS NOT NULL
            )";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
}
