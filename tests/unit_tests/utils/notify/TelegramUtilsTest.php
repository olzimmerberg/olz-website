<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/model/TelegramLink.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../../src/utils/notify/TelegramUtils.php';

$iso_now = '2020-03-13 19:30:00';

$valid_pin = '00000000';
$expired_pin = '00000001';
$inexistent_pin = '00000002';
// As this PIN is expired, PIN generation returns this PIN.
$generated_pin_1 = '00000001';
// The $generated_pin_1 expiration has been updated, now the first available PIN is this.
$generated_pin_2 = '00000002';
// The $generated_pin_2 expiration has been updated, now the first available PIN is this.
$generated_pin_3 = '00000003';

class FakeTelegramUtilsEntityManager {
    public $persisted = [];
    public $flushed = [];
    private $repositories = [];

    public function __construct() {
        $this->repositories = [
            'TelegramLink' => new FakeTelegramUtilsTelegramLinkRepository(),
            // 'User' => new FakeTelegramUtilsUserRepository(),
        ];
    }

    public function getRepository($class) {
        return $this->repositories[$class] ?? null;
    }

    public function persist($object) {
        $this->persisted[] = $object;
    }

    public function flush() {
        $this->flushed = $this->persisted;
    }
}

class FakeTelegramUtilsTelegramLinkRepository {
    public function findOneBy($where) {
        global $valid_pin, $expired_pin;

        $valid_pin_link = new TelegramLink();
        $valid_pin_link->setPin($valid_pin);
        $valid_pin_link->setPinExpiresAt(new DateTime('2020-03-13 19:35:00')); // in 5 minutes
        $valid_pin_link->setUser(new User()); // in 5 minutes

        $expired_pin_link = new TelegramLink();
        $expired_pin_link->setPin($expired_pin);
        $expired_pin_link->setPinExpiresAt(new DateTime('2020-03-13 19:25:00')); // 5 minutes ago

        $null_pin_link = new TelegramLink();
        $null_pin_link->setPin(null);
        $null_pin_link->setPinExpiresAt(null);

        if ($where == ['pin' => $valid_pin]) {
            return $valid_pin_link;
        }
        if ($where == ['pin' => $expired_pin]) {
            return $expired_pin_link;
        }
        if ($where == ['user' => 1]) {
            return $valid_pin_link;
        }
        if ($where == ['user' => 2]) {
            return $expired_pin_link;
        }
        if ($where == ['user' => 3]) {
            return $null_pin_link;
        }
        if ($where == ['telegram_chat_id' => 1]) {
            return $valid_pin_link;
        }
        if ($where == ['telegram_chat_id' => 2]) {
            return $expired_pin_link;
        }
        if ($where == ['telegram_chat_id' => 3]) {
            return $null_pin_link;
        }
        return null;
    }
}

class FakeTelegramUtilsTelegramFetcher {
    public $fetchEmpty = false;
    public $fetchNotOk = false;
    public $fetchWithError = false;

    public function callTelegramApi($command, $args, $bot_token) {
        if ($this->fetchEmpty) {
            return null;
        }
        if ($this->fetchNotOk) {
            return ['ok' => false];
        }
        if ($this->fetchWithError) {
            throw new Exception('fake-telegram-fetcher-exception');
        }
        return ['result' => 'interesting'];
    }
}

class DeterministicTelegramUtils extends TelegramUtils {
    private $generateTelegramPinCallCount = 0;

    public function generateTelegramPin() {
        $pin = str_pad(strval($this->generateTelegramPinCallCount), 8, '0', STR_PAD_LEFT);
        $this->generateTelegramPinCallCount++;
        return $pin;
    }
}

/**
 * @internal
 * @covers \TelegramUtils
 */
final class TelegramUtilsTest extends TestCase {
    public function testGenerateTelegramPin(): void {
        global $iso_now;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $telegram_utils = new TelegramUtils('fake-bot-name', 'fake-bot-token', $telegram_fetcher, $entity_manager, $date_utils);

        $this->assertSame('fake-bot-name', $telegram_utils->getBotName());
        $this->assertSame(26 + 26 + 10, strlen($telegram_utils->getTelegramPinChars()));
        $this->assertGreaterThanOrEqual(8, $telegram_utils->getTelegramPinLength());
        $this->assertMatchesRegularExpression('/[a-zA-Z0-9]{8}/', $telegram_utils->generateTelegramPin());
    }

    public function testSetNewPinForLink(): void {
        global $iso_now, $generated_pin_1;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $telegram_utils = new DeterministicTelegramUtils('fake-bot-name', 'fake-bot-token', $telegram_fetcher, $entity_manager, $date_utils);
        $telegram_link = new TelegramLink();

        $telegram_utils->setNewPinForLink($telegram_link);

        $this->assertSame($generated_pin_1, $telegram_link->getPin());
        $this->assertSame('2020-03-13 19:40:00', $telegram_link->getPinExpiresAt()->format('Y-m-d H:i:s'));
    }

    public function testStartChatForUser(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $telegram_utils = new DeterministicTelegramUtils('fake-bot-name', 'fake-bot-token', $telegram_fetcher, $entity_manager, $date_utils);

        $user = new User();
        $user->setId(1);
        $telegram_link = $telegram_utils->startChatForUser($user);

        $this->assertSame($generated_pin_1, $telegram_link->getPin());
        $this->assertSame('2020-03-13 19:40:00', $telegram_link->getPinExpiresAt()->format('Y-m-d H:i:s'));

        $user = new User();
        $user->setId(4);
        $telegram_link = $telegram_utils->startChatForUser($user);

        $this->assertSame($generated_pin_2, $telegram_link->getPin());
        $this->assertSame('2020-03-13 19:40:00', $telegram_link->getPinExpiresAt()->format('Y-m-d H:i:s'));
    }

    public function testStartAnonymousChat(): void {
        global $iso_now, $expired_pin;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $telegram_utils = new DeterministicTelegramUtils('fake-bot-name', 'fake-bot-token', $telegram_fetcher, $entity_manager, $date_utils);

        $telegram_link = $telegram_utils->startAnonymousChat(2, 2);

        $this->assertSame($expired_pin, $telegram_link->getPin());
        $this->assertSame('2020-03-13 19:25:00', $telegram_link->getPinExpiresAt()->format('Y-m-d H:i:s'));
        $this->assertSame(null, $telegram_link->getUser());
        $this->assertSame(null, $telegram_link->getTelegramChatId()); // not modified for existing
        $this->assertSame(null, $telegram_link->getTelegramUserId()); // not modified for existing
        $this->assertSame([], $telegram_link->getTelegramChatState());
        $this->assertSame(null, $telegram_link->getCreatedAt());
        $this->assertSame(null, $telegram_link->getLinkedAt());

        $telegram_link = $telegram_utils->startAnonymousChat(4, 4);

        $this->assertSame(null, $telegram_link->getPin());
        $this->assertSame(null, $telegram_link->getPinExpiresAt());
        $this->assertSame(null, $telegram_link->getUser());
        $this->assertSame(4, $telegram_link->getTelegramChatId());
        $this->assertSame(4, $telegram_link->getTelegramUserId());
        $this->assertSame([], $telegram_link->getTelegramChatState());
        $this->assertSame($iso_now, $telegram_link->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertSame(null, $telegram_link->getLinkedAt());
    }

    public function testLinkChatUsingPin(): void {
        global $iso_now, $valid_pin, $expired_pin, $inexistent_pin;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $telegram_utils = new DeterministicTelegramUtils('fake-bot-name', 'fake-bot-token', $telegram_fetcher, $entity_manager, $date_utils);

        $telegram_chat_id = 1;
        $telegram_user_id = 10;
        $telegram_link = $telegram_utils->linkChatUsingPin($valid_pin, $telegram_chat_id, $telegram_user_id);

        $this->assertSame($valid_pin, $telegram_link->getPin());
        $this->assertSame('2020-03-13 19:35:00', $telegram_link->getPinExpiresAt()->format('Y-m-d H:i:s'));
        $this->assertSame(null, $telegram_link->getUser()->getId());
        $this->assertSame($telegram_chat_id, $telegram_link->getTelegramChatId());
        $this->assertSame($telegram_user_id, $telegram_link->getTelegramUserId());
        $this->assertSame([], $telegram_link->getTelegramChatState());
        $this->assertSame(null, $telegram_link->getCreatedAt());
        $this->assertSame($iso_now, $telegram_link->getLinkedAt()->format('Y-m-d H:i:s'));

        try {
            $telegram_link = $telegram_utils->linkChatUsingPin($expired_pin, 2, 2);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('PIN ist abgelaufen.', $exc->getMessage());
        }

        try {
            $telegram_link = $telegram_utils->linkChatUsingPin($inexistent_pin, 3, 3);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Falscher PIN.', $exc->getMessage());
        }
    }

    public function testLinkUserUsingPin(): void {
        global $iso_now, $valid_pin, $expired_pin, $inexistent_pin;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $telegram_utils = new DeterministicTelegramUtils('fake-bot-name', 'fake-bot-token', $telegram_fetcher, $entity_manager, $date_utils);

        $user = new User();
        $user->setId(1);
        $telegram_link = $telegram_utils->linkUserUsingPin($valid_pin, $user);

        $this->assertSame($valid_pin, $telegram_link->getPin());
        $this->assertSame('2020-03-13 19:35:00', $telegram_link->getPinExpiresAt()->format('Y-m-d H:i:s'));
        $this->assertSame($user, $telegram_link->getUser());
        $this->assertSame(null, $telegram_link->getTelegramChatId());
        $this->assertSame(null, $telegram_link->getTelegramUserId());
        $this->assertSame([], $telegram_link->getTelegramChatState());
        $this->assertSame(null, $telegram_link->getCreatedAt());
        $this->assertSame($iso_now, $telegram_link->getLinkedAt()->format('Y-m-d H:i:s'));

        try {
            $user = new User();
            $user->setId(2);
            $telegram_link = $telegram_utils->linkUserUsingPin($expired_pin, $user);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('PIN ist abgelaufen.', $exc->getMessage());
        }

        try {
            $user = new User();
            $user->setId(3);
            $telegram_link = $telegram_utils->linkUserUsingPin($inexistent_pin, $user);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Falscher PIN.', $exc->getMessage());
        }
    }

    public function testGetFreshChatLinkForUser(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $telegram_utils = new DeterministicTelegramUtils('fake-bot-name', 'fake-bot-token', $telegram_fetcher, $entity_manager, $date_utils);

        $user = new User();
        $user->setId(4);
        $chat_link = $telegram_utils->getFreshChatLinkForUser($user);

        $this->assertSame("https://t.me/fake-bot-name?start={$generated_pin_1}", $chat_link);

        $user = new User();
        $user->setId(2);
        $chat_link = $telegram_utils->getFreshChatLinkForUser($user);

        $this->assertSame("https://t.me/fake-bot-name?start={$generated_pin_2}", $chat_link);
    }

    public function testGetFreshPinForChat(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2, $generated_pin_3;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $telegram_utils = new DeterministicTelegramUtils('fake-bot-name', 'fake-bot-token', $telegram_fetcher, $entity_manager, $date_utils);

        $telegram_chat_id = 1;
        $chat_link = $telegram_utils->getFreshPinForChat($telegram_chat_id);

        $this->assertSame($generated_pin_1, $chat_link);

        $telegram_chat_id = 2;
        $chat_link = $telegram_utils->getFreshPinForChat($telegram_chat_id);

        $this->assertSame($generated_pin_2, $chat_link);

        $telegram_chat_id = 3;
        $chat_link = $telegram_utils->getFreshPinForChat($telegram_chat_id);

        $this->assertSame($generated_pin_3, $chat_link);

        try {
            $telegram_chat_id = 4;
            $chat_link = $telegram_utils->getFreshPinForChat($telegram_chat_id);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Unbekannter Chat.', $exc->getMessage());
        }
    }

    public function testIsAnonymousChat(): void {
        global $iso_now;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $telegram_utils = new DeterministicTelegramUtils('fake-bot-name', 'fake-bot-token', $telegram_fetcher, $entity_manager, $date_utils);

        $this->assertSame(false, $telegram_utils->isAnonymousChat(1));
        $this->assertSame(true, $telegram_utils->isAnonymousChat(2));
        $this->assertSame(true, $telegram_utils->isAnonymousChat(3));
        $this->assertSame(true, $telegram_utils->isAnonymousChat(4));
    }

    public function testGetChatState(): void {
        global $iso_now;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $telegram_utils = new DeterministicTelegramUtils('fake-bot-name', 'fake-bot-token', $telegram_fetcher, $entity_manager, $date_utils);

        $this->assertSame([], $telegram_utils->getChatState(1));
        $this->assertSame([], $telegram_utils->getChatState(2));
        $this->assertSame([], $telegram_utils->getChatState(3));
        $this->assertSame(null, $telegram_utils->getChatState(4));
    }

    public function testSetChatState(): void {
        global $iso_now;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $telegram_utils = new DeterministicTelegramUtils('fake-bot-name', 'fake-bot-token', $telegram_fetcher, $entity_manager, $date_utils);

        $telegram_utils->setChatState(1, ['test' => true]);
        $telegram_utils->setChatState(2, ['test' => 2]);
        $telegram_utils->setChatState(3, []);
        try {
            $telegram_utils->setChatState(4, ['test' => 4]);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Unbekannter Chat.', $exc->getMessage());
        }
    }

    public function testCallTelegramApi(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2, $generated_pin_3;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $telegram_utils = new DeterministicTelegramUtils('fake-bot-name', 'fake-bot-token', $telegram_fetcher, $entity_manager, $date_utils);

        $result = $telegram_utils->callTelegramApi('fakeCommand', ['fakeArg' => 'fakeValue']);

        $this->assertSame(['result' => 'interesting'], $result);
    }

    public function testCallTelegramApiEmpty(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2, $generated_pin_3;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $telegram_utils = new DeterministicTelegramUtils('fake-bot-name', 'fake-bot-token', $telegram_fetcher, $entity_manager, $date_utils);

        try {
            $telegram_fetcher->fetchEmpty = true;
            $result = $telegram_utils->callTelegramApi('fakeCommand', ['fakeArg' => 'fakeValue']);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('{"ok":false}', $exc->getMessage());
        }
    }

    public function testCallTelegramApiNotOk(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2, $generated_pin_3;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $telegram_utils = new DeterministicTelegramUtils('fake-bot-name', 'fake-bot-token', $telegram_fetcher, $entity_manager, $date_utils);

        try {
            $telegram_fetcher->fetchNotOk = true;
            $result = $telegram_utils->callTelegramApi('fakeCommand', ['fakeArg' => 'fakeValue']);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('{"ok":false}', $exc->getMessage());
        }
    }

    public function testCallTelegramApiWithError(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2, $generated_pin_3;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $telegram_utils = new DeterministicTelegramUtils('fake-bot-name', 'fake-bot-token', $telegram_fetcher, $entity_manager, $date_utils);

        try {
            $telegram_fetcher->fetchWithError = true;
            $result = $telegram_utils->callTelegramApi('fakeCommand', ['fakeArg' => 'fakeValue']);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('fake-telegram-fetcher-exception', $exc->getMessage());
        }
    }
}
