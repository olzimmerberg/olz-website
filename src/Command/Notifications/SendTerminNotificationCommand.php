<?php

namespace Olz\Command\Notifications;

use Doctrine\ORM\Query\ResultSetMapping;
use Olz\Command\Notifications\Common\BaseSendNotificationsCommand;
use Olz\Command\Notifications\Common\Notification;
use Olz\Constants\NotificationDeliveryType;
use Olz\Constants\NotificationType;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\Roles\Role;
use Olz\Entity\TelegramLink;
use Olz\Entity\Throttling;
use Olz\Entity\Users\User;
use Olz\Utils\WithUtilsTrait;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'olz:send-termin-notifications')]
class SendTerminNotificationCommand extends BaseSendNotificationsCommand {
    use WithUtilsTrait;

    protected \DateTime $today;
    protected \DateTime $yesterday;

    public function getNotificationSubscriptionType(): NotificationType {
        return NotificationType::TERMIN_NOTIFICATION;
    }

    public function autogenerateSubscriptions(): void {
        $this->log()->info("Generating termin notification subscriptions...");
        $termin_notifications_state = $this->getTerminNotificationState();

        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        $user_repo = $this->entityManager()->getRepository(User::class);
        foreach ($termin_notifications_state as $user_id => $user_state) {
            foreach ($user_state as $delivery_type => $state) {
                $subscription_id = $state['subscription_id'] ?? false;
                $needs_subscription = $state['needs_subscription'] ?? false;
                $user = $user_repo->findOneBy(['id' => $user_id]);
                if (!$user) {
                    $this->log()->warning("No user (ID:{$user_id}) for termin notification");
                }
                if ($needs_subscription && !$subscription_id && $user) {
                    $this->log()->info("Generating termin notification subscription for '{$user}' via '{$delivery_type}'...");
                    $subscription = new NotificationSubscription();
                    $subscription->setUser($user);
                    $subscription->setDeliveryType(NotificationDeliveryType::from($delivery_type));
                    $subscription->setNotificationType(NotificationType::TERMIN_NOTIFICATION);
                    $subscription->setNotificationTypeArgs(json_encode(['cancelled' => false]) ?: '{}');
                    $subscription->setCreatedAt($now_datetime);
                    $this->entityManager()->persist($subscription);
                }
                if ($subscription_id && !$needs_subscription) {
                    $this->log()->info("Removing termin notification subscription ({$subscription_id}) for '{$user}' via '{$delivery_type}'...");
                    $subscription = $notification_subscription_repo->findOneBy(['id' => $subscription_id]);
                    if ($subscription) {
                        $this->entityManager()->remove($subscription);
                    }
                }
            }
        }
        $this->entityManager()->flush();
    }

    /**
     * @return array<int, array<NotificationDeliveryType, array{
     *   subscription_id?: int,
     *   needs_subscription?: bool,
     * }>> */
    protected function getTerminNotificationState(): array {
        $termin_notifications_state = [];

        // Find users with existing termin notification subscriptions.
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        $termin_notification_subscriptions = $notification_subscription_repo->findBy([
            'notification_type' => NotificationType::TERMIN_NOTIFICATION,
        ]);
        foreach ($termin_notification_subscriptions as $subscription) {
            $user_id = $subscription->getUser()->getId() ?: 0;
            $delivery_type = $subscription->getDeliveryType()->value;
            $user_state = $termin_notifications_state[$user_id][$delivery_type] ?? [];
            $subscription_id = $subscription->getId();
            $this->generalUtils()->checkNotNull($subscription_id, "No subscription ID");
            $user_state['subscription_id'] = $subscription_id;
            $termin_notifications_state[$user_id][$delivery_type] = $user_state;
        }

        // Find users who should have termin notification subscriptions.
        $user_repo = $this->entityManager()->getRepository(User::class);
        $users_with_email = $user_repo->getUsersWithLogin();
        foreach ($users_with_email as $user_with_email) {
            $user_id = $user_with_email->getId();
            if (!$user_id) {
                continue;
            }
            $delivery_type = NotificationDeliveryType::EMAIL->value;
            $user_state = $termin_notifications_state[$user_id][$delivery_type] ?? [];
            $user_state['needs_subscription'] = true;
            $termin_notifications_state[$user_id][$delivery_type] = $user_state;
        }
        $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
        $telegram_links = $telegram_link_repo->getActivatedTelegramLinks();
        foreach ($telegram_links as $telegram_link) {
            $user_id = $telegram_link->getUser()?->getId();
            if (!$user_id) {
                continue;
            }
            $delivery_type = NotificationDeliveryType::TELEGRAM->value;
            $user_state = $termin_notifications_state[$user_id][$delivery_type] ?? [];
            $user_state['needs_subscription'] = true;
            $termin_notifications_state[$user_id][$delivery_type] = $user_state;
        }

        return $termin_notifications_state;
    }

    // ---

    public function getNotifications(array $args): array {
        if ($args['cancelled'] ?? false) {
            return [];
        }

        $ident = 'send-termin-notifications';
        $throttling_repo = $this->entityManager()->getRepository(Throttling::class);
        $last_occurrence = $throttling_repo->getLastOccurrenceOf($ident) ?? new \DateTime();
        $now = new \DateTime($this->dateUtils()->getIsoNow());

        // TODO: Add volunteers and participants
        $sql = <<<'ZZZZZZZZZZ'
            WITH
                computed AS (
                    SELECT
                        ADDTIME(
                            ADDDATE(t.start_date, INTERVAL -tn.fires_earlier_seconds SECOND),
                            COALESCE(t.start_time, '12:00:00')
                        ) AS fires_at,
                        tn.title AS notification_title,
                        tn.content AS notification_content,
                        tn.recipient_user_id AS recipient_user_id,
                        tn.recipient_role_id AS recipient_role_id,
                        tn.recipient_termin_owner_user AS recipient_termin_owner_user,
                        tn.recipient_termin_owner_role AS recipient_termin_owner_role,
                        tn.recipient_termin_organizer AS recipient_termin_organizer,
                        tn.recipient_termin_volunteers AS recipient_termin_volunteers,
                        tn.recipient_termin_participants AS recipient_termin_participants,
                        t.title AS termin_title,
                        t.start_date AS termin_date,
                        t.id AS termin_id,
                        t.owner_user_id AS termin_owner_user_id,
                        t.owner_role_id AS termin_owner_role_id,
                        t.organizer_user_id AS termin_organizer_user_id
                    FROM
                        termin_notifications tn
                        JOIN termine t ON (tn.termin_id = t.id)
                )
            SELECT *
            FROM computed
            WHERE fires_at < :now AND fires_at >= :last_occurrence
            ZZZZZZZZZZ;

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('fires_at', 'firesAt');
        $rsm->addScalarResult('notification_title', 'notificationTitle');
        $rsm->addScalarResult('notification_content', 'notificationContent');
        $rsm->addScalarResult('recipient_user_id', 'recipientUserId');
        $rsm->addScalarResult('recipient_role_id', 'recipientRoleId');
        $rsm->addScalarResult('recipient_termin_owner_user', 'recipientTerminOwnerUser');
        $rsm->addScalarResult('recipient_termin_owner_role', 'recipientTerminOwnerRole');
        $rsm->addScalarResult('recipient_termin_organizer', 'recipientTerminOrganizer');
        $rsm->addScalarResult('recipient_termin_volunteers', 'recipientTerminVolunteers');
        $rsm->addScalarResult('recipient_termin_participants', 'recipientTerminParticipants');
        $rsm->addScalarResult('termin_title', 'terminTitle');
        $rsm->addScalarResult('termin_date', 'terminDate');
        $rsm->addScalarResult('termin_id', 'terminId');
        $rsm->addScalarResult('termin_owner_user_id', 'terminOwnerUserId');
        $rsm->addScalarResult('termin_owner_role_id', 'terminOwnerRoleId');
        $rsm->addScalarResult('termin_organizer_user_id', 'terminOrganizerUserId');

        $role_repo = $this->entityManager()->getRepository(Role::class);
        $query = $this->entityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('now', $now, 'datetime');
        $query->setParameter('last_occurrence', $last_occurrence, 'datetime');
        $result = $query->execute();

        $base_href = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();
        $termine_url = "{$base_href}{$code_href}termine";

        $notifications = [];
        foreach ($result as $row) {
            $date = $this->dateUtils()->compactDate($row['terminDate']);
            $title = "Termin-Erinnerung: {$row['notificationTitle']}";
            $text = <<<ZZZZZZZZZZ
                Hallo %%userFirstName%%,

                Dies ist eine Termin-Erinnerung für den Termin [{$row['terminTitle']}]({$termine_url}/{$row['terminId']}) vom {$date}:

                {$row['notificationContent']}

                ZZZZZZZZZZ;
            $recipient_user_ids = [];
            if ($row['recipientUserId'] ?? false) {
                $recipient_user_ids[$row['recipientUserId']] = true;
            }
            if ($row['recipientRoleId'] ?? false) {
                $role = $role_repo->findOneBy(['id' => $row['recipientRoleId']]);
                foreach (($role?->getUsers() ?? []) as $user) {
                    $recipient_user_ids[$user->getId()] = true;
                }
            }
            if ($row['recipientTerminOwnerUser'] ?? false) {
                $recipient_user_ids[$row['terminOwnerUserId']] = true;
            }
            if ($row['recipientTerminOwnerRole'] ?? false) {
                $role = $role_repo->findOneBy(['id' => $row['terminOwnerRoleId']]);
                foreach (($role?->getUsers() ?? []) as $user) {
                    $recipient_user_ids[$user->getId()] = true;
                }
            }
            if ($row['recipientTerminOrganizer'] ?? false) {
                $recipient_user_ids[$row['terminOrganizerUserId']] = true;
            }
            if ($row['recipientTerminVolunteers'] ?? false) {
                // TODO
                $this->log()->notice("Termin notification to recipientTerminVolunteers: not implemented");
            }
            if ($row['recipientTerminParticipants'] ?? false) {
                // TODO
                $this->log()->notice("Termin notification to recipientTerminParticipants: not implemented");
            }
            $notifications[] = new Notification($title, $text, [
                'notification_type' => NotificationType::TERMIN_NOTIFICATION,
                'recipient_user_ids' => $recipient_user_ids,
            ]);
        }

        $throttling_repo->recordOccurrenceOf($ident, $now);

        return $notifications;
    }
}
