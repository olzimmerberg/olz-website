<?php

namespace Olz\Command\SendDailyNotificationsCommand;

use Doctrine\Common\Collections\Criteria;
use Olz\Entity\Blog;
use Olz\Entity\Forum;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\Termine\Termin;

class DailySummaryGetter {
    use \Psr\Log\LoggerAwareTrait;

    public const CUT_OFF_TIME = '16:00:00';

    protected $entityManager;
    protected $dateUtils;
    protected $envUtils;

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function getDailySummaryNotification($args) {
        $today = new \DateTime($this->dateUtils->getIsoToday());
        $minus_one_day = \DateInterval::createFromDateString("-1 days");
        $yesterday = (new \DateTime($this->dateUtils->getIsoToday()))->add($minus_one_day);
        $today_at_cut_off = new \DateTime($today->format('Y-m-d').' '.self::CUT_OFF_TIME);
        $yesterday_at_cut_off = new \DateTime($yesterday->format('Y-m-d').' '.self::CUT_OFF_TIME);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->orX(
                    Criteria::expr()->andX(
                        Criteria::expr()->eq('datum', $today),
                        Criteria::expr()->lte('zeit', new \DateTime(self::CUT_OFF_TIME)),
                    ),
                    Criteria::expr()->andX(
                        Criteria::expr()->eq('datum', $yesterday),
                        Criteria::expr()->gt('zeit', new \DateTime(self::CUT_OFF_TIME)),
                    ),
                ),
                Criteria::expr()->eq('on_off', 1),
            ))
            ->orderBy(['datum' => Criteria::ASC, 'zeit' => Criteria::ASC])
            ->setFirstResult(0)
            ->setMaxResults(1000)
        ;
        $date_only_criteria = Criteria::create()
            ->where(
                Criteria::expr()->andX(
                    Criteria::expr()->eq('datum', $yesterday),
                    Criteria::expr()->eq('on_off', 1),
                ),
            )
            ->orderBy(['datum' => Criteria::ASC])
            ->setFirstResult(0)
            ->setMaxResults(1000)
        ;
        $termine_criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->lte('modified', $today_at_cut_off),
                Criteria::expr()->gt('modified', $yesterday_at_cut_off),
                Criteria::expr()->eq('newsletter', 1),
                Criteria::expr()->eq('on_off', 1),
            ))
            ->orderBy(['datum' => Criteria::ASC, 'zeit' => Criteria::ASC])
            ->setFirstResult(0)
            ->setMaxResults(1000)
        ;

        $notification_text = '';
        $base_href = $this->envUtils->getBaseHref();
        $code_href = $this->envUtils->getCodeHref();

        if ($args['aktuell'] ?? false) {
            $aktuell_url = "{$base_href}{$code_href}aktuell.php";
            $aktuell_text = '';
            $aktuell_repo = $this->entityManager->getRepository(NewsEntry::class);
            $aktuells = $aktuell_repo->matching($criteria);
            foreach ($aktuells as $aktuell) {
                $id = $aktuell->getId();
                $date = $aktuell->getDate();
                $pretty_date = $date->format('d.m.');
                $time = $aktuell->getTime();
                $pretty_time = $time->format('H:i');
                $title = $aktuell->getTitle();
                $aktuell_text .= "- {$pretty_date} {$pretty_time}: [{$title}]({$aktuell_url}?id={$id})\n";
            }
            if (strlen($aktuell_text) > 0) {
                $notification_text .= "\n**Aktuell**\n\n{$aktuell_text}\n";
            }
        }

        if ($args['blog'] ?? false) {
            $blog_url = "{$base_href}{$code_href}blog.php";
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
                $blog_text .= "- {$pretty_date} {$pretty_time}: [{$title}]({$blog_url}#id{$id})\n";
            }
            if (strlen($blog_text) > 0) {
                $notification_text .= "\n**Kaderblog**\n\n{$blog_text}\n";
            }
        }

        if ($args['forum'] ?? false) {
            $forum_url = "{$base_href}{$code_href}forum.php";
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
                if (strlen(trim($title)) > 0) {
                    $forum_text .= "- {$pretty_date} {$pretty_time}: [{$title}]({$forum_url}#id{$id})\n";
                }
            }
            if (strlen($forum_text) > 0) {
                $notification_text .= "\n**Forum**\n\n{$forum_text}\n";
            }
        }

        if ($args['termine'] ?? false) {
            $termine_url = "{$base_href}{$code_href}termine.php";
            $termine_text = '';
            $termin_repo = $this->entityManager->getRepository(Termin::class);
            $termine = $termin_repo->matching($termine_criteria);
            foreach ($termine as $termin) {
                $id = $termin->getId();
                $starts_on = $termin->getStartsOn();
                $ends_on = $termin->getEndsOn();
                $pretty_date = ($ends_on && $ends_on > $starts_on)
                    ? $starts_on->format('d.m.').' - '.$ends_on->format('d.m.')
                    : $starts_on->format('d.m.');
                $title = $termin->getTitle();
                if (strlen(trim($title)) > 0) {
                    $termine_text .= "- {$pretty_date}: [{$title}]({$termine_url}?id={$id})\n";
                }
            }
            if (strlen($termine_text) > 0) {
                $notification_text .= "\n**Aktualisierte Termine**\n\n{$termine_text}\n";
            }
        }

        if (strlen($notification_text) == 0) {
            return null;
        }

        $title = "Tageszusammenfassung";
        $text = "Hallo %%userFirstName%%,\n\nDas lief heute auf [olzimmerberg.ch](https://olzimmerberg.ch):\n\n{$notification_text}";

        return new Notification($title, $text, [
            'notification_type' => NotificationSubscription::TYPE_DAILY_SUMMARY,
        ]);
    }
}
