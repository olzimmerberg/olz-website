<?php

require_once __DIR__.'/Notification.php';
require_once __DIR__.'/../../model/Termin.php';

class WeeklySummaryGetter {
    use Psr\Log\LoggerAwareTrait;

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function getWeeklySummaryNotification($args) {
        // TODO: implement
        return null;
    }
}
