<?php

namespace Olz\Users\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use Olz\Entity\AccessToken;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\StravaLink;
use Olz\Entity\TelegramLink;
use PhpTypeScriptApi\HttpError;

class DeleteUserEndpoint extends OlzDeleteEntityEndpoint {
    use UserEndpointTrait;

    public static function getIdent(): string {
        return 'DeleteUserEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $entity = $this->getEntityById($input['id']);

        $current_user = $this->authUtils()->getCurrentUser();
        $is_me = (
            $current_user
            && $entity->getUsername() === $current_user->getUsername()
            && $entity->getId() === $current_user->getId()
        );
        $can_update = $this->entityUtils()->canUpdateOlzEntity($entity, null, 'users');
        if (!$is_me && !$can_update) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        // Remove news ownership
        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $news_entries = $news_repo->findBy(['owner_user' => $entity]);
        foreach ($news_entries as $news_entry) {
            $news_entry->setOwnerUser(null);
            $this->entityManager()->remove($news_entry);
        }
        $this->entityManager()->flush();

        // Remove notification subscriptions
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        $subscriptions = $notification_subscription_repo->findBy(['user' => $entity]);
        foreach ($subscriptions as $subscription) {
            $this->entityManager()->remove($subscription);
        }
        $this->entityManager()->flush();

        // Remove telegram links
        $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
        $telegram_links = $telegram_link_repo->findBy(['user' => $entity]);
        foreach ($telegram_links as $telegram_link) {
            $this->entityManager()->remove($telegram_link);
        }
        $this->entityManager()->flush();

        // Remove strava links
        $strava_link_repo = $this->entityManager()->getRepository(StravaLink::class);
        $strava_links = $strava_link_repo->findBy(['user' => $entity]);
        foreach ($strava_links as $strava_link) {
            $this->entityManager()->remove($strava_link);
        }
        $this->entityManager()->flush();

        // Remove access tokens
        $access_token_repo = $this->entityManager()->getRepository(AccessToken::class);
        $access_tokens = $access_token_repo->findBy(['user' => $entity]);
        foreach ($access_tokens as $access_token) {
            $this->entityManager()->remove($access_token);
        }
        $this->entityManager()->flush();

        // Remove avatar
        $data_path = $this->envUtils()->getDataPath();
        $avatar_path = "{$data_path}img/users/{$entity->getId()}";
        $this->generalUtils()->removeRecursive($avatar_path);

        // Log out
        if ($this->session()->get('user') === $entity->getUsername()) {
            $this->session()->delete('auth');
            $this->session()->delete('root');
            $this->session()->delete('user');
            $this->session()->delete('user_id');
            $this->session()->delete('auth_user');
            $this->session()->delete('auth_user_id');
            $this->session()->clear();
        }

        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $entity->setOnOff(0);
        $entity->setEmail('');
        $entity->setPasswordHash('');
        $entity->setPhone('');
        $entity->setGender(null);
        $entity->setBirthdate(null);
        $entity->setStreet(null);
        $entity->setPostalCode(null);
        $entity->setCity(null);
        $entity->setRegion(null);
        $entity->setCountryCode(null);
        $entity->setPermissions('');
        $entity->setRoot(null);
        $entity->setMemberType(null);
        $entity->setMemberLastPaid(null);
        $entity->setWantsPostalMail(false);
        $entity->setPostalTitle(null);
        $entity->setPostalName(null);
        $entity->setJoinedOn(null);
        $entity->setJoinedReason(null);
        $entity->setLeftOn(null);
        $entity->setLeftReason(null);
        $entity->setSolvNumber(null);
        $entity->setSiCardNumber(null);
        $entity->setNotes('');
        $entity->setLastModifiedAt($now_datetime);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}