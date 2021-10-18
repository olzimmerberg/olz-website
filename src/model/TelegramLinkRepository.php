<?php

use Doctrine\ORM\EntityRepository;

require_once __DIR__.'/../config/doctrine.php';

class TelegramLinkRepository extends EntityRepository {
    public function getActivatedTelegramLinks() {
        $dql = "
            SELECT tl
            FROM TelegramLink tl
            WHERE (
                tl.user IS NOT NULL
                AND tl.telegram_chat_id IS NOT NULL
            )";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
}
