<?php

use Doctrine\Common\Collections\Criteria;

require_once __DIR__.'/Notification.php';
require_once __DIR__.'/../../model/NotificationSubscription.php';
require_once __DIR__.'/../../model/Termin.php';

class DeadlineWarningGetter {
    use Psr\Log\LoggerAwareTrait;

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function getDeadlineWarningNotification($args) {
        $days_arg = intval($args['days'] ?? '');
        if ($days_arg <= 0 || $days_arg > 7) {
            return null;
        }
        $given_days = DateInterval::createFromDateString("+{$days_arg} days");
        $in_given_days = (new DateTime($this->dateUtils->getIsoToday()))->add($given_days);

        $termin_repo = $this->entityManager->getRepository(Termin::class);
        $solv_event_repo = $this->entityManager->getRepository(SolvEvent::class);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('deadline', new DateTime($in_given_days->format('Y-m-d'))))
            ->orderBy(['date' => Criteria::ASC])
            ->setFirstResult(0)
            ->setMaxResults(1000)
        ;
        $deadlines = $solv_event_repo->matching($criteria);

        $base_href = $this->envUtils->getBaseHref();
        $code_href = $this->envUtils->getCodeHref();

        $termine_url = "{$base_href}{$code_href}termine.php";
        $deadlines_text = '';
        foreach ($deadlines as $deadline) {
            $solv_uid = $deadline->getSolvUid();
            $termin = $termin_repo->findOneBy([
                'solv_uid' => $solv_uid,
                'on_off' => 1,
            ]);
            if (!$termin) {
                continue;
            }
            $deadline_date = $deadline->getDeadline();
            $date = $deadline_date->format('d.m.');
            $id = $termin->getId();
            $title = $termin->getTitle();
            $deadlines_text .= "- {$date}: Meldeschluss für '[{$title}]({$termine_url}#id{$id})'\n";
        }

        if (strlen($deadlines_text) == 0) {
            return null;
        }

        $title = "Meldeschlusswarnung";
        $text = "Hallo %%userFirstName%%,\n\nFolgende Meldeschlüsse stehen bevor:\n\n{$deadlines_text}";

        return new Notification($title, $text, [
            'notification_type' => NotificationSubscription::TYPE_DEADLINE_WARNING,
        ]);
    }
}
