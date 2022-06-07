<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class TelegramLinkRepository extends EntityRepository {
    public function getActivatedTelegramLinks() {
        $dql = "
            SELECT tl
            FROM App\\Entity\\TelegramLink tl
            WHERE (
                tl.user IS NOT NULL
                AND tl.telegram_chat_id IS NOT NULL
            )";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
}
