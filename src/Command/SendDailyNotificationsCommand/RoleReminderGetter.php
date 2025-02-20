<?php

namespace Olz\Command\SendDailyNotificationsCommand;

use Olz\Entity\NotificationSubscription;
use Olz\Entity\Roles\Role;
use Olz\Entity\Users\User;
use Olz\Repository\Roles\PredefinedRole;
use Olz\Utils\WithUtilsTrait;

class RoleReminderGetter implements NotificationGetterInterface {
    use WithUtilsTrait;

    public const EXECUTION_DATE = '****-01-02';

    public function autogenerateSubscriptions(): void {
        $role_reminder_state = $this->getRoleReminderState();

        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        $user_repo = $this->entityManager()->getRepository(User::class);
        foreach ($role_reminder_state as $ident => $state) {
            [$user_id, $role_id] = array_map(fn ($part): int => intval($part), explode('-', $ident));
            $reminder_id = $state['reminder_id'] ?? false;
            $needs_reminder = $state['needs_reminder'] ?? false;
            if ($needs_reminder && !$reminder_id) {
                $user = $user_repo->findOneBy(['id' => $user_id]);
                $this->log()->info("Generating role ({$role_id}) reminder subscription for '{$user}'...");
                $subscription = new NotificationSubscription();
                $subscription->setUser($user);
                $subscription->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
                $subscription->setNotificationType(NotificationSubscription::TYPE_ROLE_REMINDER);
                $subscription->setNotificationTypeArgs(json_encode([
                    'role_id' => $role_id,
                    'cancelled' => false,
                ]) ?: '{}');
                $subscription->setCreatedAt($now_datetime);
                $this->entityManager()->persist($subscription);
            }
            if ($reminder_id && !$needs_reminder) {
                $user = $user_repo->findOneBy(['id' => $user_id]);
                $this->log()->info("Removing role ({$role_id}) reminder subscription ({$reminder_id}) for '{$user}'...");
                $subscription = $notification_subscription_repo->findOneBy(['id' => $reminder_id]);
                $this->entityManager()->remove($subscription);
            }
        }
        $this->entityManager()->flush();
    }

    /** @return array<string, array{reminder_id?: int, needs_reminder?: bool}> */
    protected function getRoleReminderState(): array {
        $role_reminder_state = [];

        // Find role assignees with existing role reminder notification subscriptions.
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        $telegram_notification_subscriptions = $notification_subscription_repo->findBy([
            'notification_type' => NotificationSubscription::TYPE_ROLE_REMINDER,
        ]);
        foreach ($telegram_notification_subscriptions as $subscription) {
            $user_id = $subscription->getUser()->getId();
            $args = json_decode($subscription->getNotificationTypeArgs(), true);
            $role_id = $args['role_id'] ?? null;
            if ($role_id === null) {
                $this->log()->warning("Role reminder notification subscription ({$subscription->getId()}) without role ID");
            }
            $ident = "{$user_id}-{$role_id}";
            $state = $role_reminder_state[$ident] ?? [];
            $state['reminder_id'] = $subscription->getId();
            $role_reminder_state[$ident] = $state;
        }

        // Find role assignees who should have role reminder notification subscriptions.
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $roles = $role_repo->findBy(['on_off' => 1]);
        foreach ($roles as $role) {
            $role_id = $role->getId();
            $assignees = $role->getUsers();
            foreach ($assignees as $assignee) {
                $user_id = $assignee->getId();
                $ident = "{$user_id}-{$role_id}";
                $user_state = $role_reminder_state[$ident] ?? [];
                $user_state['needs_reminder'] = true;
                $role_reminder_state[$ident] = $user_state;
            }
        }

        return $role_reminder_state;
    }

    // ---

    /** @param array<string, mixed> $args */
    public function getNotification(array $args): ?Notification {
        $today = $this->dateUtils()->getIsoToday();
        if (substr($today, 4, 6) != substr($this::EXECUTION_DATE, 4, 6)) {
            return null;
        }

        $role_repo = $this->entityManager()->getRepository(Role::class);
        $role = $role_repo->findOneBy(['id' => $args['role_id']]);
        $role_name = "{$role->getName()} ({$role->getTitle()})";
        $num_assignees = $role->getUsers()->count();
        $num_others = $num_assignees - 1;
        $num_assignees_note = $num_assignees > 1 ? " (zusammen mit {$num_others} Anderen)" : '';

        $parent_role = $role;
        $parent_role_id = $parent_role->getParentRoleId();
        while ($parent_role_id) {
            $parent_role = $role_repo->findOneBy(['id' => $parent_role_id]);
            $parent_role_id = $parent_role?->getParentRoleId();
        }
        $root_role = $parent_role;
        $pretty_root_assignees = implode(' / ', array_map(function (User $user): string {
            return "{$user->getFullName()}, {$user->getEmail()}";
        }, [...$root_role->getUsers()]));

        $base_href = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();
        $role_url = "{$base_href}{$code_href}verein/{$role->getUsername()}";
        $sysadmin_role = $role_repo->getPredefinedRole(PredefinedRole::Sysadmin);
        $host = $this->envUtils()->getEmailForwardingHost();
        $sysadmin_email = "{$sysadmin_role->getUsername()}@{$host}";

        $title = "Ressort-Erinnerung";
        $text = <<<ZZZZZZZZZZ
            Hallo %%userFirstName%%,

            Du bist im [OLZ-Organigramm]({$base_href}{$code_href}verein){$num_assignees_note} unter dem Ressort [**{$role_name}**]({$role_url}) eingetragen, bzw. für dieses Ressort zuständig.
            
            **Vielen Dank, dass du mithilfst, unseren Verein am Laufen zu halten!**

            Um das Organigramm aktuell zu halten, bitten wir dich, die folgenden Punkte durchzugehen.
            
            **Falls etwas unklar ist, kontaktiere bitte den Website-Admin: {$sysadmin_email}!**

            - Bitte schau dir die [Präsenz deines Ressorts auf olzimmerberg.ch]({$role_url}) an, und **kontrolliere, ergänze und verbessere** gegebenenfalls die Angaben. Wenn du eingeloggt bist, kannst du diese direkt bearbeiten.
            - **Falls** du im kommenden Jahr nicht mehr für dieses Ressort zuständig sein kannst oder möchtest, bzw. nicht mehr unter diesem Ressort angezeigt werden solltest, kontaktiere bitte "deinen" Vorstand: {$pretty_root_assignees} (oder den Präsi).
            - **Falls** du noch kein OLZ-Konto hast, erstelle doch eines ([zum Login-Dialog]({$base_href}{$code_href}#login-dialog), dann "Noch kein OLZ-Konto?" wählen). Verwende den Benutzernamen "%%userUsername%%", um automatisch Schreib-Zugriff für dein Ressort zu erhalten.

            Besten Dank für deine Mithilfe,

            Der Vorstand der OL Zimmerberg
            ZZZZZZZZZZ;

        return new Notification($title, $text, [
            'notification_type' => NotificationSubscription::TYPE_ROLE_REMINDER,
        ]);
    }
}
