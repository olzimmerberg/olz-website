<?php

namespace Olz\Repository;

use Doctrine\ORM\EntityRepository;

class TelegramLinkRepository extends EntityRepository {
    public function getActivatedTelegramLinks() {
        $dql = "
            SELECT tl
            FROM Olz\\Entity\\TelegramLink tl
            WHERE (
                tl.user IS NOT NULL
                AND tl.telegram_chat_id IS NOT NULL
            )";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
}
