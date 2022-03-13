<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../OlzEndpoint.php';

class DeleteUserEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        global $entityManager, $_DATE;
        require_once __DIR__.'/../../config/date.php';
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../../model/index.php';
        require_once __DIR__.'/../../utils/auth/AuthUtils.php';
        require_once __DIR__.'/../../utils/env/EnvUtils.php';
        $auth_utils = AuthUtils::fromEnv();
        $env_utils = EnvUtils::fromEnv();
        $this->setAuthUtils($auth_utils);
        $this->setDateUtils($_DATE);
        $this->setEntityManager($entityManager);
        $this->setEnvUtils($env_utils);
    }

    public function setAuthUtils($new_auth_utils) {
        $this->authUtils = $new_auth_utils;
    }

    public function setDateUtils($new_date_utils) {
        $this->dateUtils = $new_date_utils;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public function setEnvUtils($new_env_utils) {
        $this->envUtils = $new_env_utils;
    }

    public static function getIdent() {
        return 'DeleteUserEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => new FieldTypes\IntegerField([]),
        ]]);
    }

    protected function handle($input) {
        $auth_username = $this->session->get('user');

        $user_repo = $this->entityManager->getRepository(User::class);
        $user = $user_repo->findOneBy(['id' => $input['id']]);
        $user_id = $user->getId();

        if ($user->getUsername() !== $auth_username) {
            return ['status' => 'ERROR'];
        }

        // Remove news ownership
        $news_repo = $this->entityManager->getRepository(NewsEntry::class);
        $news_entries = $news_repo->findBy(['owner_user' => $user]);
        foreach ($news_entries as $news_entry) {
            $news_entry->setOwnerUser(null);
            $this->entityManager->remove($news_entry);
        }
        $this->entityManager->flush();

        // Remove notification subscriptions
        $notification_subscription_repo = $this->entityManager->getRepository(NotificationSubscription::class);
        $subscriptions = $notification_subscription_repo->findBy(['user' => $user]);
        foreach ($subscriptions as $subscription) {
            $this->entityManager->remove($subscription);
        }
        $this->entityManager->flush();

        // Remove telegram links
        $telegram_link_repo = $this->entityManager->getRepository(TelegramLink::class);
        $telegram_links = $telegram_link_repo->findBy(['user' => $user]);
        foreach ($telegram_links as $telegram_link) {
            $this->entityManager->remove($telegram_link);
        }
        $this->entityManager->flush();

        // Remove strava links
        $strava_link_repo = $this->entityManager->getRepository(StravaLink::class);
        $strava_links = $strava_link_repo->findBy(['user' => $user]);
        foreach ($strava_links as $strava_link) {
            $this->entityManager->remove($strava_link);
        }
        $this->entityManager->flush();

        // Remove google links
        $google_link_repo = $this->entityManager->getRepository(GoogleLink::class);
        $google_links = $google_link_repo->findBy(['user' => $user]);
        foreach ($google_links as $google_link) {
            $this->entityManager->remove($google_link);
        }
        $this->entityManager->flush();

        // Remove facebook links
        $facebook_link_repo = $this->entityManager->getRepository(FacebookLink::class);
        $facebook_links = $facebook_link_repo->findBy(['user' => $user]);
        foreach ($facebook_links as $facebook_link) {
            $this->entityManager->remove($facebook_link);
        }
        $this->entityManager->flush();

        // Remove access tokens
        $access_token_repo = $this->entityManager->getRepository(AccessToken::class);
        $access_tokens = $access_token_repo->findBy(['user' => $user]);
        foreach ($access_tokens as $access_token) {
            $this->entityManager->remove($access_token);
        }
        $this->entityManager->flush();

        // Remove avatar
        $data_path = $this->envUtils->getDataPath();
        $avatar_path = "{$data_path}img/users/{$user_id}.jpg";
        if ($this->isFile($avatar_path)) {
            $this->unlink($avatar_path);
        }

        // Log out
        if ($this->session->get('user') === $user->getUsername()) {
            $this->session->delete('auth');
            $this->session->delete('root');
            $this->session->delete('user');
            $this->session->delete('user_id');
            $this->session->clear();
        }

        // Check user roles
        $has_user_roles = count($user->getRoles()) > 0;

        // Check news authorship
        $news_repo = $this->entityManager->getRepository(NewsEntry::class);
        $news_entry = $news_repo->findOneBy(['author_user' => $user]);
        $has_news_authorship = $news_entry !== null;

        $should_keep_basic_info = $has_user_roles || $has_news_authorship;
        if ($should_keep_basic_info) {
            $now_datetime = new DateTime($this->dateUtils->getIsoNow());
            $user->setEmail('');
            $user->setPasswordHash('');
            $user->setPhone('');
            $user->setGender(null);
            $user->setBirthdate(null);
            $user->setStreet(null);
            $user->setPostalCode(null);
            $user->setCity(null);
            $user->setRegion(null);
            $user->setCountryCode(null);
            $user->setZugriff('');
            $user->setRoot(null);
            $user->setLastModifiedAt($now_datetime);
        } else {
            $this->logger->warning("Removing user {$user}.");
            $this->entityManager->remove($user);
        }
        $this->entityManager->flush();

        return [
            'status' => 'OK',
        ];
    }

    protected function isFile($path) {
        return is_file($path);
    }

    protected function unlink($path) {
        return unlink($path);
    }

    protected function rename($source_path, $destination_path) {
        return rename($source_path, $destination_path);
    }
}
