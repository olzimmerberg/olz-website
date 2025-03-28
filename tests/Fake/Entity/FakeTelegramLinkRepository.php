<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\TelegramLink;
use Olz\Entity\Users\User;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\Users\FakeUser;

/**
 * @extends FakeOlzRepository<TelegramLink>
 */
class FakeTelegramLinkRepository extends FakeOlzRepository {
    public string $olzEntityClass = TelegramLink::class;
    public string $fakeOlzEntityClass = FakeTelegramLink::class;

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        $user = $criteria['user'] ?? null;
        if ($user instanceof User) {
            $user = $user->getId();
        }

        if ($user === 1) {
            $redundant_pin_link = new TelegramLink();
            $redundant_pin_link->setId(13);
            return [$redundant_pin_link];
        }
        if ($user === 2) {
            return [];
        }
        if ($user === 3) {
            return [];
        }
        $query_json = json_encode($criteria);
        throw new \Exception("findBy query not mocked: {$query_json}");
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        global $valid_pin, $expired_pin;

        $user = $criteria['user'] ?? null;
        if ($user instanceof User) {
            $user = $user->getId();
        }

        if ($criteria == ['pin' => $valid_pin]) {
            return FakeTelegramLink::validPin();
        }
        if ($criteria == ['pin' => $expired_pin]) {
            return FakeTelegramLink::expiredPin();
        }
        if ($user === 1) {
            return FakeTelegramLink::validPin();
        }
        if ($user === 2) {
            return FakeTelegramLink::expiredPin();
        }
        if ($user === 3) {
            return FakeTelegramLink::null();
        }
        if ($user === 404) {
            return null;
        }
        if ($user === 666) {
            $telegram_link = new TelegramLink();
            $telegram_link->setTelegramChatId('provoke_error');
            return $telegram_link;
        }
        if ($criteria == ['telegram_chat_id' => 1]) {
            return FakeTelegramLink::validPin();
        }
        if ($criteria == ['telegram_chat_id' => 2]) {
            return FakeTelegramLink::expiredPin();
        }
        if ($criteria == ['telegram_chat_id' => 3]) {
            return FakeTelegramLink::null();
        }
        if ($criteria === ['telegram_chat_id' => 17089367]) {
            $telegram_link = new TelegramLink();
            $telegram_link->setUser(FakeUser::defaultUser());
            return $telegram_link;
        }
        return null;
    }

    /** @return array<TelegramLink> */
    public function getActivatedTelegramLinks(): array {
        return [
            FakeTelegramLink::validPin(),
            FakeTelegramLink::expiredPin(),
            FakeTelegramLink::null(),
        ];
    }
}
