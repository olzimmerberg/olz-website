<?php

use Doctrine\Common\Collections\Criteria;

require_once __DIR__.'/Notification.php';
require_once __DIR__.'/../../model/Aktuell.php';
require_once __DIR__.'/../../model/Blog.php';
require_once __DIR__.'/../../model/Galerie.php';
require_once __DIR__.'/../../model/Forum.php';
require_once __DIR__.'/../../model/NotificationSubscription.php';

class WeeklySummaryGetter {
    use Psr\Log\LoggerAwareTrait;

    const CUT_OFF_TIME = '01:00:00';

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function getWeeklySummaryNotification($args) {
        $current_weekday = intval($this->dateUtils->getCurrentDateInFormat('N'));
        $monday = 1;
        if ($current_weekday != $monday) {
            return null;
        }

        $today = new DateTime($this->dateUtils->getIsoToday());
        $minus_one_week = DateInterval::createFromDateString("-7 days");
        $last_week = (new DateTime($this->dateUtils->getIsoToday()))->add($minus_one_week);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->orX(
                Criteria::expr()->andX(
                    Criteria::expr()->eq('datum', $today),
                    Criteria::expr()->lte('zeit', new DateTime(self::CUT_OFF_TIME)),
                ),
                Criteria::expr()->andX(
                    Criteria::expr()->lt('datum', $today),
                    Criteria::expr()->gt('datum', $last_week),
                ),
                Criteria::expr()->andX(
                    Criteria::expr()->eq('datum', $last_week),
                    Criteria::expr()->gt('zeit', new DateTime(self::CUT_OFF_TIME)),
                ),
            ))
            ->orderBy(['datum' => Criteria::ASC, 'zeit' => Criteria::ASC])
            ->setFirstResult(0)
            ->setMaxResults(1000)
        ;
        $date_only_criteria = Criteria::create()
            ->where(
                Criteria::expr()->andX(
                    Criteria::expr()->lt('datum', $today),
                    Criteria::expr()->gte('datum', $last_week),
                )
            )
            ->orderBy(['datum' => Criteria::ASC])
            ->setFirstResult(0)
            ->setMaxResults(1000)
    ;

        $notification_text = '';
        if ($args['aktuell'] ?? false) {
            $aktuell_text = '';
            $aktuell_repo = $this->entityManager->getRepository(Aktuell::class);
            $aktuells = $aktuell_repo->matching($criteria);
            foreach ($aktuells as $aktuell) {
                $id = $aktuell->getId();
                $date = $aktuell->getDate();
                $pretty_date = $date->format('d.m.');
                $time = $aktuell->getTime();
                $pretty_time = $time->format('H:i');
                $title = $aktuell->getTitle();
                $aktuell_text .= "- {$pretty_date} {$pretty_time}: {$title}\n";
            }
            if (strlen($aktuell_text) > 0) {
                $notification_text .= "\n**Aktuell**\n\n{$aktuell_text}\n";
            }
        }

        if ($args['blog'] ?? false) {
            $blog_text = '';
            $blog_repo = $this->entityManager->getRepository(Blog::class);
            $blogs = $blog_repo->matching($criteria);
            foreach ($blogs as $blog) {
                $id = $blog->getId();
                $date = $blog->getDate();
                $pretty_date = $date->format('d.m.');
                $time = $blog->getTime();
                $pretty_time = $time->format('H:i');
                $title = $blog->getTitle();
                $blog_text .= "- {$pretty_date} {$pretty_time}: {$title}\n";
            }
            if (strlen($blog_text) > 0) {
                $notification_text .= "\n**Kaderblog**\n\n{$blog_text}\n";
            }
        }

        if ($args['galerie'] ?? false) {
            $galerie_text = '';
            $galerie_repo = $this->entityManager->getRepository(Galerie::class);
            $galeries = $galerie_repo->matching($date_only_criteria);
            foreach ($galeries as $galerie) {
                $id = $galerie->getId();
                $date = $galerie->getDate();
                $pretty_date = $date->format('d.m.');
                $title = $galerie->getTitle();
                $galerie_text .= "- {$pretty_date}: {$title}\n";
            }
            if (strlen($galerie_text) > 0) {
                $notification_text .= "\n**Galerien**\n\n{$galerie_text}\n";
            }
        }

        if ($args['forum'] ?? false) {
            $forum_text = '';
            $forum_repo = $this->entityManager->getRepository(Forum::class);
            $forums = $forum_repo->matching($criteria);
            foreach ($forums as $forum) {
                $id = $forum->getId();
                $date = $forum->getDate();
                $pretty_date = $date->format('d.m.');
                $time = $forum->getTime();
                $pretty_time = $time->format('H:i');
                $title = $forum->getTitle();
                $forum_text .= "- {$pretty_date} {$pretty_time}: {$title}\n";
            }
            if (strlen($forum_text) > 0) {
                $notification_text .= "\n**Forum**\n\n{$forum_text}\n";
            }
        }

        if (strlen($notification_text) == 0) {
            return null;
        }

        $title = "Wochenzusammenfassung";
        $text = "Hallo %%userFirstName%%,\n\nDas lief diese Woche auf [olzimmerberg.ch](https://olzimmerberg.ch):\n\n{$notification_text}";

        return new Notification($title, $text, [
            'notification_type' => NotificationSubscription::TYPE_WEEKLY_SUMMARY,
        ]);
    }
}
