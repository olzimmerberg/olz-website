<?php

namespace Olz\Repository;

use Olz\Entity\TelegramLink;
use Olz\Repository\Common\OlzRepository;

class TelegramLinkRepository extends OlzRepository {
    /** @return array<TelegramLink> */
    public function getActivatedTelegramLinks(): array {
        $dql = "
            SELECT tl
            FROM Olz:TelegramLink tl
            WHERE (
                tl.user IS NOT NULL
                AND tl.telegram_chat_id IS NOT NULL
            )";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
}
