<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\TelegramLink;
use Olz\Entity\User;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeTelegramLinkRepository extends FakeOlzRepository {
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        if ($criteria == ['user' => 1]) {
            $redundant_pin_link = new TelegramLink();
            $redundant_pin_link->setId(13);
            return [$redundant_pin_link];
        }
        if ($criteria == ['user' => 2]) {
            return [];
        }
        if ($criteria == ['user' => 3]) {
            return [];
        }
        $query_json = json_encode($criteria);
        throw new \Exception("findBy query not mocked: {$query_json}");
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        global $valid_pin, $expired_pin;

        $valid_pin_link = new TelegramLink();
        $valid_pin_link->setPin($valid_pin);
        $valid_pin_link->setPinExpiresAt(new \DateTime('2020-03-13 19:35:00')); // in 5 minutes
        $valid_pin_link->setUser(new User()); // in 5 minutes

        $expired_pin_link = new TelegramLink();
        $expired_pin_link->setPin($expired_pin);
        $expired_pin_link->setPinExpiresAt(new \DateTime('2020-03-13 19:25:00')); // 5 minutes ago

        $null_pin_link = new TelegramLink();
        $null_pin_link->setPin(null);
        $null_pin_link->setPinExpiresAt(null);

        if ($criteria == ['pin' => $valid_pin]) {
            return $valid_pin_link;
        }
        if ($criteria == ['pin' => $expired_pin]) {
            return $expired_pin_link;
        }
        if ($criteria == ['user' => 1]) {
            return $valid_pin_link;
        }
        if ($criteria == ['user' => 2]) {
            return $expired_pin_link;
        }
        if ($criteria == ['user' => 3]) {
            return $null_pin_link;
        }
        if ($criteria == ['telegram_chat_id' => 1]) {
            return $valid_pin_link;
        }
        if ($criteria == ['telegram_chat_id' => 2]) {
            return $expired_pin_link;
        }
        if ($criteria == ['telegram_chat_id' => 3]) {
            return $null_pin_link;
        }
        if ($criteria === ['telegram_chat_id' => 17089367]) {
            $telegram_link = new TelegramLink();
            $telegram_link->setUser(FakeUser::defaultUser());
            return $telegram_link;
        }
        return null;
    }
}
