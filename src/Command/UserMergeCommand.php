<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Entity\AccessToken;
use Olz\Entity\Anmelden\Booking;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\Quiz\SkillLevel;
use Olz\Entity\StravaLink;
use Olz\Entity\TelegramLink;
use Olz\Entity\Termine\TerminNotification;
use Olz\Entity\Termine\TerminNotificationTemplate;
use Olz\Entity\Users\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:user-merge')]
class UserMergeCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function configure(): void {
        $this->addArgument('target', InputArgument::REQUIRED, 'Target user ID');
        $this->addArgument('source', InputArgument::REQUIRED, 'Source user ID');
        $this->addOption(
            'dry',
            null,
            InputOption::VALUE_NONE,
            'Do not actually make the changes',
        );
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $target_id = intval($input->getArgument('target'));
        $source_id = intval($input->getArgument('source'));
        $user_repo = $this->entityManager()->getRepository(User::class);
        $target_user = $user_repo->findOneBy(['id' => $target_id]);
        $source_user = $user_repo->findOneBy(['id' => $source_id]);
        $this->generalUtils()->checkNotNull($target_user, "Target user not found");
        $this->generalUtils()->checkNotNull($source_user, "Source user not found");
        array_map(
            fn ($line) => $this->log()->info($line),
            explode("\n", <<<ZZZZZZZZZZ

                Target
                ------
                {$this->getUserOverview($target_user)}

                Source
                ------
                {$this->getUserOverview($source_user)}

                ZZZZZZZZZZ),
        );
        $dry = $input->getOption('dry');
        if (!$dry) {
            $this->makeChanges($target_user, $source_user);
            $this->entityManager()->flush();
            array_map(
                fn ($line) => $this->log()->info($line),
                explode("\n", <<<ZZZZZZZZZZ

                    Result
                    ------
                    {$this->getUserOverview($target_user)}

                    ZZZZZZZZZZ),
            );
        }
        return Command::SUCCESS;
    }

    protected function getUserOverview(User $user): string {
        $overview = str_replace("\n", "\n    ", $user->pretty());

        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $news_entries = $news_repo->findBy(['owner_user' => $user]);
        $num_news_entries = count($news_entries);

        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        $subscriptions = $notification_subscription_repo->findBy(['user' => $user]);
        $num_subscriptions = count($subscriptions);

        $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
        $telegram_links = $telegram_link_repo->findBy(['user' => $user]);
        $num_telegram_links = count($telegram_links);

        $strava_link_repo = $this->entityManager()->getRepository(StravaLink::class);
        $strava_links = $strava_link_repo->findBy(['user' => $user]);
        $num_strava_links = count($strava_links);

        $access_token_repo = $this->entityManager()->getRepository(AccessToken::class);
        $access_tokens = $access_token_repo->findBy(['user' => $user]);
        $num_access_tokens = count($access_tokens);

        return <<<ZZZZZZZZZZ
                {$overview}
                News ({$num_news_entries})
                Notification subscriptions ({$num_subscriptions})
                Telegram links ({$num_telegram_links})
                Strava links ({$num_strava_links})
                Access tokens ({$num_access_tokens})

            ZZZZZZZZZZ;
    }

    protected function makeChanges(User $target_user, User $source_user): void {
        if ($target_user->getFirstName() !== $source_user->getFirstName()) {
            $this->log()->notice("FirstName {$target_user->getFirstName()} vs. {$source_user->getFirstName()}");
        }
        if ($target_user->getLastName() !== $source_user->getLastName()) {
            $this->log()->notice("LastName {$target_user->getLastName()} vs. {$source_user->getLastName()}");
        }
        $this->log()->info("OldUsername {$target_user->getOldUsername()} => {$source_user->getUsername()}");
        $target_user->setOldUsername($source_user->getUsername());
        if (!$target_user->getEmail() && $source_user->getEmail()) {
            $this->log()->info("Email merge");
            $target_user->setEmail($source_user->getEmail());
        }
        if (!$target_user->getPasswordHash() && $source_user->getPasswordHash()) {
            $this->log()->info("PasswordHash merge");
            $target_user->setPasswordHash($source_user->getPasswordHash());
        }
        if ($target_user->getParentUserId() !== $source_user->getParentUserId()) {
            $this->log()->notice("ParentUserId {$target_user->getParentUserId()} vs. {$source_user->getParentUserId()}");
        }
        foreach ($source_user->getPermissionMap() as $key => $value) {
            $this->log()->info("addPermission {$key}");
            $target_user->addPermission($key);
        }
        if ($target_user->getRoot() !== $source_user->getRoot()) {
            $this->log()->notice("root {$target_user->getRoot()} vs. {$source_user->getRoot()}");
        }
        if (!$target_user->getGender() && $source_user->getGender()) {
            $this->log()->info("Gender merge");
            $target_user->setGender($source_user->getGender());
        }
        if (!$target_user->getStreet() && $source_user->getStreet()) {
            $this->log()->info("Street merge");
            $target_user->setStreet($source_user->getStreet());
        }
        if (!$target_user->getPostalCode() && $source_user->getPostalCode()) {
            $this->log()->info("PostalCode merge");
            $target_user->setPostalCode($source_user->getPostalCode());
        }
        if (!$target_user->getCity() && $source_user->getCity()) {
            $this->log()->info("City merge");
            $target_user->setCity($source_user->getCity());
        }
        if (!$target_user->getRegion() && $source_user->getRegion()) {
            $this->log()->info("Region merge");
            $target_user->setRegion($source_user->getRegion());
        }
        if (!$target_user->getCountryCode() && $source_user->getCountryCode()) {
            $this->log()->info("CountryCode merge");
            $target_user->setCountryCode($source_user->getCountryCode());
        }
        if (!$target_user->getBirthdate() && $source_user->getBirthdate()) {
            $this->log()->info("Birthdate merge");
            $target_user->setBirthdate($source_user->getBirthdate());
        }
        if (!$target_user->getPhone() && $source_user->getPhone()) {
            $this->log()->info("Phone merge");
            $target_user->setPhone($source_user->getPhone());
        }
        if (!$target_user->getSolvNumber() && $source_user->getSolvNumber()) {
            $this->log()->info("SolvNumber merge");
            $target_user->setSolvNumber($source_user->getSolvNumber());
        }
        if (!$target_user->getSiCardNumber() && $source_user->getSiCardNumber()) {
            $this->log()->info("SiCardNumber merge");
            $target_user->setSiCardNumber($source_user->getSiCardNumber());
        }

        // Roles
        foreach ($source_user->getRoles() as $role) {
            $this->log()->info("addRole {$role->getUsername()}");
            $target_user->addRole($role);
        }

        // Avatar
        $data_path = $this->envUtils()->getDataPath();
        $target_path = "{$data_path}img/users/{$target_user->getId()}";
        $source_path = "{$data_path}img/users/{$source_user->getId()}";
        if (!$target_user->getAvatarImageId() && $source_user->getAvatarImageId()) {
            $this->log()->info("AvatarImageId merge");
            $was_successful = rename($source_path, $target_path);
            if ($was_successful) {
                $target_user->setAvatarImageId($source_user->getAvatarImageId());
                $this->log()->info("Avatar successfully moved");
            } else {
                $this->log()->info("Failed moving avatar");
            }
        }
        $this->generalUtils()->removeRecursive($source_path);

        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $news_entries = $news_repo->findBy(['author_user' => $source_user]);
        foreach ($news_entries as $news_entry) {
            $this->log()->info("NewsEntry::setAuthorUser {$news_entry->getId()}");
            $news_entry->setAuthorUser($target_user);
        }

        $termin_notification_repo = $this->entityManager()->getRepository(TerminNotification::class);
        $termin_notifications = $termin_notification_repo->findBy(['recipient_user' => $source_user]);
        foreach ($termin_notifications as $termin_notification) {
            $this->log()->info("TerminNotification::setRecipientUser {$termin_notification->getId()}");
            $termin_notification->setRecipientUser($target_user);
        }

        $termin_notification_template_repo = $this->entityManager()->getRepository(TerminNotificationTemplate::class);
        $termin_notification_templates = $termin_notification_template_repo->findBy(['recipient_user' => $source_user]);
        foreach ($termin_notification_templates as $termin_notification_template) {
            $this->log()->info("TerminNotificationTemplate::setRecipientUser {$termin_notification_template->getId()}");
            $termin_notification_template->setRecipientUser($target_user);
        }

        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        $subscriptions = $notification_subscription_repo->findBy(['user' => $source_user]);
        foreach ($subscriptions as $subscription) {
            $this->log()->info("NotificationSubscription::setUser {$subscription->getId()}");
            $subscription->setUser($target_user);
        }

        $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
        $telegram_links = $telegram_link_repo->findBy(['user' => $source_user]);
        foreach ($telegram_links as $telegram_link) {
            $this->log()->info("TelegramLink::setUser {$telegram_link->getId()}");
            $telegram_link->setUser($target_user);
        }

        $strava_link_repo = $this->entityManager()->getRepository(StravaLink::class);
        $strava_links = $strava_link_repo->findBy(['user' => $source_user]);
        foreach ($strava_links as $strava_link) {
            $this->log()->info("StravaLink::setUser {$strava_link->getId()}");
            $strava_link->setUser($target_user);
        }

        $access_token_repo = $this->entityManager()->getRepository(AccessToken::class);
        $access_tokens = $access_token_repo->findBy(['user' => $source_user]);
        foreach ($access_tokens as $access_token) {
            $this->log()->info("AccessToken::setUser {$access_token->getId()}");
            $access_token->setUser($target_user);
        }

        $booking_repo = $this->entityManager()->getRepository(Booking::class);
        $bookings = $booking_repo->findBy(['user' => $source_user]);
        foreach ($bookings as $booking) {
            $this->log()->info("Booking::setUser {$booking->getId()}");
            $booking->setUser($target_user);
        }

        $skill_level_repo = $this->entityManager()->getRepository(SkillLevel::class);
        $skill_levels = $skill_level_repo->findBy(['user' => $source_user]);
        foreach ($skill_levels as $skill_level) {
            $this->log()->info("SkillLevel remove {$skill_level->getId()}");
            $this->entityManager()->remove($skill_level);
        }

        foreach ($this->entityUtils()->olzEntityClasses() as $class) {
            $olz_entity_repo = $this->entityManager()->getRepository($class);
            $olz_entities = $olz_entity_repo->findBy(['owner_user' => $source_user]);
            foreach ($olz_entities as $olz_entity) {
                $this->log()->info("{$class}::setOwnerUser (created at {$olz_entity->getCreatedAt()->format('Y-m-d H:i:s')})");
                $olz_entity->setOwnerUser($target_user);
            }
            $olz_entities = $olz_entity_repo->findBy(['created_by_user' => $source_user]);
            foreach ($olz_entities as $olz_entity) {
                $this->log()->info("{$class}::setCreatedByUser (created at {$olz_entity->getCreatedAt()->format('Y-m-d H:i:s')})");
                $olz_entity->setCreatedByUser($target_user);
            }
            $olz_entities = $olz_entity_repo->findBy(['last_modified_by_user' => $source_user]);
            foreach ($olz_entities as $olz_entity) {
                $this->log()->info("{$class}::setLastModifiedByUser (created at {$olz_entity->getCreatedAt()->format('Y-m-d H:i:s')})");
                $olz_entity->setLastModifiedByUser($target_user);
            }
        }

        $source_user->softDelete();
    }
}
