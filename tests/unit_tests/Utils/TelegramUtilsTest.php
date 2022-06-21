<?php

declare(strict_types=1);

use Olz\Entity\TelegramLink;
use Olz\Entity\User;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\TelegramUtils;

require_once __DIR__.'/../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../fake/FakeLogger.php';
require_once __DIR__.'/../common/UnitTestCase.php';

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

class FakeTelegramUtilsEntityManager extends FakeEntityManager {
    public function __construct() {
        $this->repositories = [
            TelegramLink::class => new FakeTelegramUtilsTelegramLinkRepository(),
        ];
    }
}

class FakeTelegramUtilsTelegramLinkRepository {
    public function findBy($where) {
        if ($where == ['user' => 1]) {
            $redundant_pin_link = new TelegramLink();
            $redundant_pin_link->setId(13);
            return [$redundant_pin_link];
        }
        if ($where == ['user' => 2]) {
            return [];
        }
        if ($where == ['user' => 3]) {
            return [];
        }
        $query_json = json_encode($where);
        throw new \Exception("findBy query not mocked: {$query_json}");
    }

    public function findOneBy($where) {
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
    public $fetchBlocked = false;
    public $fetchWithError = false;
    public $sentCommands = [];

    public function callTelegramApi($command, $args, $bot_token) {
        $this->sentCommands[] = [$command, $args, $bot_token];
        if ($this->fetchEmpty) {
            return null;
        }
        if ($this->fetchNotOk) {
            return ['ok' => false];
        }
        if ($this->fetchBlocked) {
            return [
                'ok' => false,
                'error_code' => 403,
                'description' => "Forbidden: bot was blocked by the user",
            ];
        }
        if ($this->fetchWithError) {
            throw new \Exception('fake-telegram-fetcher-exception');
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
 * @covers \Olz\Utils\TelegramUtils
 */
final class TelegramUtilsTest extends UnitTestCase {
    public function testGenerateTelegramPin(): void {
        global $iso_now;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new TelegramUtils();
        $telegram_utils->setEnvUtils(new FakeEnvUtils());
        $telegram_utils->setLogger($logger);

        $this->assertSame('fake-bot-name', $telegram_utils->getBotName());
        $this->assertSame(26 + 26 + 10, strlen($telegram_utils->getTelegramPinChars()));
        $this->assertGreaterThanOrEqual(8, $telegram_utils->getTelegramPinLength());
        $this->assertMatchesRegularExpression('/[a-zA-Z0-9]{8}/', $telegram_utils->generateTelegramPin());
        $this->assertSame([], $logger->handler->getPrettyRecords());
    }

    public function testSetNewPinForLink(): void {
        global $iso_now, $generated_pin_1;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setDateUtils($date_utils);
        $telegram_utils->setEntityManager($entity_manager);
        $telegram_utils->setLogger($logger);
        $telegram_link = new TelegramLink();

        $telegram_utils->setNewPinForLink($telegram_link);

        $this->assertSame($generated_pin_1, $telegram_link->getPin());
        $this->assertSame('2020-03-13 19:40:00', $telegram_link->getPinExpiresAt()->format('Y-m-d H:i:s'));
        $this->assertSame([], $logger->handler->getPrettyRecords());
    }

    public function testStartChatForUser(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setDateUtils($date_utils);
        $telegram_utils->setEntityManager($entity_manager);
        $telegram_utils->setLogger($logger);

        $user = new User();
        $user->setId(1);
        $telegram_link = $telegram_utils->startChatForUser($user);

        $this->assertSame($generated_pin_1, $telegram_link->getPin());
        $this->assertSame('2020-03-13 19:40:00', $telegram_link->getPinExpiresAt()->format('Y-m-d H:i:s'));
        $this->assertSame([], $logger->handler->getPrettyRecords());

        $user = new User();
        $user->setId(4);
        $telegram_link = $telegram_utils->startChatForUser($user);

        $this->assertSame($generated_pin_2, $telegram_link->getPin());
        $this->assertSame('2020-03-13 19:40:00', $telegram_link->getPinExpiresAt()->format('Y-m-d H:i:s'));
        $this->assertSame([], $logger->handler->getPrettyRecords());
    }

    public function testStartAnonymousChat(): void {
        global $iso_now, $expired_pin;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setDateUtils($date_utils);
        $telegram_utils->setEntityManager($entity_manager);
        $telegram_utils->setLogger($logger);

        $telegram_link = $telegram_utils->startAnonymousChat(2, 2);

        $this->assertSame($expired_pin, $telegram_link->getPin());
        $this->assertSame('2020-03-13 19:25:00', $telegram_link->getPinExpiresAt()->format('Y-m-d H:i:s'));
        $this->assertSame(null, $telegram_link->getUser());
        $this->assertSame(null, $telegram_link->getTelegramChatId()); // not modified for existing
        $this->assertSame(null, $telegram_link->getTelegramUserId()); // not modified for existing
        $this->assertSame([], $telegram_link->getTelegramChatState());
        $this->assertSame(null, $telegram_link->getCreatedAt());
        $this->assertSame(null, $telegram_link->getLinkedAt());
        $this->assertSame([], $logger->handler->getPrettyRecords());

        $telegram_link = $telegram_utils->startAnonymousChat(4, 4);

        $this->assertSame(null, $telegram_link->getPin());
        $this->assertSame(null, $telegram_link->getPinExpiresAt());
        $this->assertSame(null, $telegram_link->getUser());
        $this->assertSame(4, $telegram_link->getTelegramChatId());
        $this->assertSame(4, $telegram_link->getTelegramUserId());
        $this->assertSame([], $telegram_link->getTelegramChatState());
        $this->assertSame($iso_now, $telegram_link->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertSame(null, $telegram_link->getLinkedAt());
        $this->assertSame([], $logger->handler->getPrettyRecords());
    }

    public function testLinkChatUsingPin(): void {
        global $iso_now, $valid_pin, $expired_pin, $inexistent_pin;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setDateUtils($date_utils);
        $telegram_utils->setEntityManager($entity_manager);
        $telegram_utils->setLogger($logger);

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
        $this->assertSame([], $logger->handler->getPrettyRecords());

        try {
            $telegram_link = $telegram_utils->linkChatUsingPin($expired_pin, 2, 2);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('PIN ist abgelaufen.', $exc->getMessage());
            $this->assertSame([], $logger->handler->getPrettyRecords());
        }

        try {
            $telegram_link = $telegram_utils->linkChatUsingPin($inexistent_pin, 3, 3);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Falscher PIN.', $exc->getMessage());
            $this->assertSame([], $logger->handler->getPrettyRecords());
        }
    }

    public function testLinkUserUsingPin(): void {
        global $iso_now, $valid_pin, $expired_pin, $inexistent_pin;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setDateUtils($date_utils);
        $telegram_utils->setEntityManager($entity_manager);
        $telegram_utils->setLogger($logger);

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
        $this->assertSame([13], array_map(
            function ($telegram_link) {
                return $telegram_link->getId();
            },
            $entity_manager->removed
        ));
        $this->assertSame($entity_manager->flushed_removed, $entity_manager->removed);
        $this->assertSame([], $logger->handler->getPrettyRecords());

        try {
            $user = new User();
            $user->setId(2);
            $telegram_link = $telegram_utils->linkUserUsingPin($expired_pin, $user);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('PIN ist abgelaufen.', $exc->getMessage());
            $this->assertSame([], $logger->handler->getPrettyRecords());
        }

        try {
            $user = new User();
            $user->setId(3);
            $telegram_link = $telegram_utils->linkUserUsingPin($inexistent_pin, $user);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Falscher PIN.', $exc->getMessage());
            $this->assertSame([], $logger->handler->getPrettyRecords());
        }
    }

    public function testGetFreshPinForUser(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setDateUtils($date_utils);
        $telegram_utils->setEntityManager($entity_manager);
        $telegram_utils->setLogger($logger);

        $user = new User();
        $user->setId(4);
        $chat_link = $telegram_utils->getFreshPinForUser($user);

        $this->assertSame($generated_pin_1, $chat_link);
        $this->assertSame([], $logger->handler->getPrettyRecords());

        $user = new User();
        $user->setId(2);
        $chat_link = $telegram_utils->getFreshPinForUser($user);

        $this->assertSame($generated_pin_2, $chat_link);
        $this->assertSame([], $logger->handler->getPrettyRecords());
    }

    public function testGetFreshPinForChat(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2, $generated_pin_3;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setDateUtils($date_utils);
        $telegram_utils->setEntityManager($entity_manager);
        $telegram_utils->setLogger($logger);

        $telegram_chat_id = 1;
        $chat_link = $telegram_utils->getFreshPinForChat($telegram_chat_id);

        $this->assertSame($generated_pin_1, $chat_link);
        $this->assertSame([], $logger->handler->getPrettyRecords());

        $telegram_chat_id = 2;
        $chat_link = $telegram_utils->getFreshPinForChat($telegram_chat_id);

        $this->assertSame($generated_pin_2, $chat_link);
        $this->assertSame([], $logger->handler->getPrettyRecords());

        $telegram_chat_id = 3;
        $chat_link = $telegram_utils->getFreshPinForChat($telegram_chat_id);

        $this->assertSame($generated_pin_3, $chat_link);
        $this->assertSame([], $logger->handler->getPrettyRecords());

        try {
            $telegram_chat_id = 4;
            $chat_link = $telegram_utils->getFreshPinForChat($telegram_chat_id);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Unbekannter Chat.', $exc->getMessage());
            $this->assertSame([], $logger->handler->getPrettyRecords());
        }
    }

    public function testIsAnonymousChat(): void {
        global $iso_now;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setEntityManager($entity_manager);
        $telegram_utils->setLogger($logger);

        $this->assertSame(false, $telegram_utils->isAnonymousChat(1));
        $this->assertSame(true, $telegram_utils->isAnonymousChat(2));
        $this->assertSame(true, $telegram_utils->isAnonymousChat(3));
        $this->assertSame(true, $telegram_utils->isAnonymousChat(4));
        $this->assertSame([], $logger->handler->getPrettyRecords());
    }

    public function testGetChatState(): void {
        global $iso_now;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setEntityManager($entity_manager);
        $telegram_utils->setLogger($logger);

        $this->assertSame([], $telegram_utils->getChatState(1));
        $this->assertSame([], $telegram_utils->getChatState(2));
        $this->assertSame([], $telegram_utils->getChatState(3));
        $this->assertSame(null, $telegram_utils->getChatState(4));
        $this->assertSame([], $logger->handler->getPrettyRecords());
    }

    public function testSetChatState(): void {
        global $iso_now;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setEntityManager($entity_manager);
        $telegram_utils->setLogger($logger);

        $telegram_utils->setChatState(1, ['test' => true]);
        $telegram_utils->setChatState(2, ['test' => 2]);
        $telegram_utils->setChatState(3, []);
        try {
            $telegram_utils->setChatState(4, ['test' => 4]);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Unbekannter Chat.', $exc->getMessage());
            $this->assertSame([], $logger->handler->getPrettyRecords());
        }
    }

    public function testSendConfiguration(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2, $generated_pin_3;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setEnvUtils(new FakeEnvUtils());
        $telegram_utils->setTelegramFetcher($telegram_fetcher);
        $telegram_utils->setLogger($logger);

        $telegram_utils->sendConfiguration();

        $this->assertSame([
            ['setMyCommands', [
                'commands' => '[{"command":"\/ich","description":"Wer bin ich?"}]',
                'scope' => '{"type":"all_private_chats"}',
            ], 'fake-bot-token'],
        ], $telegram_fetcher->sentCommands);
        $this->assertSame([
            "INFO Telegram API call successful",
        ], $logger->handler->getPrettyRecords());
    }

    public function testSendConfigurationError(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2, $generated_pin_3;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setEnvUtils(new FakeEnvUtils());
        $telegram_utils->setTelegramFetcher($telegram_fetcher);
        $telegram_utils->setLogger($logger);

        $telegram_fetcher->fetchNotOk = true;
        $telegram_utils->sendConfiguration();

        $this->assertSame([
            ['setMyCommands', [
                'commands' => '[{"command":"\/ich","description":"Wer bin ich?"}]',
                'scope' => '{"type":"all_private_chats"}',
            ], 'fake-bot-token'],
        ], $telegram_fetcher->sentCommands);
        $this->assertSame([
            "NOTICE Telegram API response was not OK: {\"ok\":false}",
            "ERROR Telegram API: Could not 'setMyCommands'",
        ], $logger->handler->getPrettyRecords());
    }

    public function testCallTelegramApi(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2, $generated_pin_3;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setEnvUtils(new FakeEnvUtils());
        $telegram_utils->setTelegramFetcher($telegram_fetcher);
        $telegram_utils->setLogger($logger);

        $result = $telegram_utils->callTelegramApi('fakeCommand', ['fakeArg' => 'fakeValue']);

        $this->assertSame(['result' => 'interesting'], $result);
        $this->assertSame([
            "INFO Telegram API call successful",
        ], $logger->handler->getPrettyRecords());
    }

    public function testCallTelegramApiEmpty(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2, $generated_pin_3;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setEnvUtils(new FakeEnvUtils());
        $telegram_utils->setTelegramFetcher($telegram_fetcher);
        $telegram_utils->setLogger($logger);

        try {
            $telegram_fetcher->fetchEmpty = true;
            $result = $telegram_utils->callTelegramApi('fakeCommand', ['fakeArg' => 'fakeValue']);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('{"ok":false}', $exc->getMessage());
            $this->assertSame([
                "WARNING Telegram API response was empty",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testCallTelegramApiNotOk(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2, $generated_pin_3;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setEnvUtils(new FakeEnvUtils());
        $telegram_utils->setTelegramFetcher($telegram_fetcher);
        $telegram_utils->setLogger($logger);

        try {
            $telegram_fetcher->fetchNotOk = true;
            $result = $telegram_utils->callTelegramApi('fakeCommand', ['fakeArg' => 'fakeValue']);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('{"ok":false}', $exc->getMessage());
            $this->assertSame([
                "NOTICE Telegram API response was not OK: {\"ok\":false}",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testCallTelegramApiBlocked(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2, $generated_pin_3;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setEntityManager($entity_manager);
        $telegram_utils->setEnvUtils(new FakeEnvUtils());
        $telegram_utils->setTelegramFetcher($telegram_fetcher);
        $telegram_utils->setLogger($logger);

        try {
            $telegram_fetcher->fetchBlocked = true;
            $result = $telegram_utils->callTelegramApi('fakeCommand', ['chat_id' => 13]);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('{"ok":false,"error_code":403,"description":"Forbidden: bot was blocked by the user"}', $exc->getMessage());
            $this->assertSame([
                "NOTICE We're blocked. Remove telegram link!",
                "NOTICE Telegram API response was not OK: {\"ok\":false,\"error_code\":403,\"description\":\"Forbidden: bot was blocked by the user\"}",
            ], $logger->handler->getPrettyRecords());
            $this->assertSame(
                $entity_manager->removed,
                $entity_manager->flushed_removed
            );
            $this->assertSame(1, count($entity_manager->removed));
        }
    }

    public function testCallTelegramApiWithError(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2, $generated_pin_3;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setEnvUtils(new FakeEnvUtils());
        $telegram_utils->setTelegramFetcher($telegram_fetcher);
        $telegram_utils->setLogger($logger);

        try {
            $telegram_fetcher->fetchWithError = true;
            $result = $telegram_utils->callTelegramApi('fakeCommand', ['fakeArg' => 'fakeValue']);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('fake-telegram-fetcher-exception', $exc->getMessage());
            $this->assertSame([], $logger->handler->getPrettyRecords());
        }
    }

    public function testRenderMarkdown(): void {
        global $iso_now, $generated_pin_1, $generated_pin_2, $generated_pin_3;
        $telegram_fetcher = new FakeTelegramUtilsTelegramFetcher();
        $entity_manager = new FakeTelegramUtilsEntityManager();
        $date_utils = new FixedDateUtils($iso_now);
        $logger = FakeLogger::create();
        $telegram_utils = new DeterministicTelegramUtils();
        $telegram_utils->setLogger($logger);

        // Ignore HTML
        $html = $telegram_utils->renderMarkdown("Normal<h1>H1</h1><script>alert('not good!');</script>");
        $this->assertSame("NormalH1alert('not good!');\n", $html);

        // Headings
        $html = $telegram_utils->renderMarkdown("Normal\n# H1\n## H2\n### H3\nNormal");
        $this->assertSame("Normal\n# H1\n## H2\n### H3\nNormal\n", $html);

        // Font style
        $html = $telegram_utils->renderMarkdown("Normal **fe\\*\\*tt** __fe\\_\\_tt__");
        $this->assertSame("Normal <strong>fe**tt</strong> <strong>fe__tt</strong>\n", $html);
        $html = $telegram_utils->renderMarkdown("Normal *kur\\*siv* _kur\\_siv_");
        $this->assertSame("Normal <em>kur*siv</em> <em>kur_siv</em>\n", $html);
        $html = $telegram_utils->renderMarkdown("Normal ~~durch\\~\\~gestrichen~~");
        $this->assertSame("Normal <del>durch~~gestrichen</del>\n", $html);

        // Quotes
        $html = $telegram_utils->renderMarkdown("Normal\n> quote\nstill quote\n\nnot anymore");
        $this->assertSame("Normal\n&gt; quote\nstill quote\n\nnot anymore\n", $html);

        // Ordered lists
        $html = $telegram_utils->renderMarkdown("Normal\n1. one\n2. two\n3. three\nstill three\n\nnot anymore");
        $this->assertSame("Normal\n1. one\n2. two\n3. three\nstill three\n\nnot anymore\n", $html);

        // Unordered lists
        $html = $telegram_utils->renderMarkdown("Normal\n- one\n- two\n- three\nstill three\n\nnot anymore");
        $this->assertSame("Normal\n- one\n- two\n- three\nstill three\n\nnot anymore\n", $html);

        // Code
        $html = $telegram_utils->renderMarkdown("Normal `co\\`de`");
        $this->assertSame("Normal <code>co\\</code>de`\n", $html);
        $html = $telegram_utils->renderMarkdown("Normal ```co`de```");
        $this->assertSame("Normal <code>co`de</code>\n", $html);
        $html = $telegram_utils->renderMarkdown("Normal\n```python\nco`de\n```");
        $this->assertSame("Normal\n<code>python co`de </code>\n", $html);

        // Horizontal rule
        $html = $telegram_utils->renderMarkdown("something\n\n---\n\ndifferent");
        $this->assertSame("something\n\n---\n\ndifferent\n", $html);

        // Links
        $html = $telegram_utils->renderMarkdown("Normal [link](http://127.0.0.1/)");
        $this->assertSame("Normal <a href=\"http://127.0.0.1/\">link</a>\n", $html);
        $html = $telegram_utils->renderMarkdown("Normal http://127.0.0.1/");
        $this->assertSame("Normal <a href=\"http://127.0.0.1/\">http://127.0.0.1/</a>\n", $html);

        // Image
        $html = $telegram_utils->renderMarkdown("Normal ![bird](img/bird.jpg)");
        $this->assertSame("Normal <img src=\"img/bird.jpg\" alt=\"bird\" />\n", $html);

        // Table
        $html = $telegram_utils->renderMarkdown("Normal\n\n| left | middle | right |\n| --- | --- | --- |\n| 1 | 2 | 3 |\n\nafter");
        $this->assertSame("Normal\n\n| left | middle | right |\n| --- | --- | --- |\n| 1 | 2 | 3 |\n\nafter\n", $html);

        // Footnote
        $html = $telegram_utils->renderMarkdown("This. [^1]\n\n[^1]: explains everything\n");
        // does not work
        $this->assertSame("This. [^1]\n\n[^1]: explains everything\n", $html);

        // Heading ID
        $html = $telegram_utils->renderMarkdown("# So linkable {#anchor}\n");
        // does not work
        $this->assertSame("# So linkable {#anchor}\n", $html);

        // Heading ID
        $html = $telegram_utils->renderMarkdown("- [x] finish\n- [ ] this\n- [ ] list\n");
        $this->assertSame("- [x] finish\n- [ ] this\n- [ ] list\n", $html);

        $this->assertSame([], $logger->handler->getPrettyRecords());
    }
}
