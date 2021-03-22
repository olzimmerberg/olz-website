<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../fake/fake_user.php';
require_once __DIR__.'/../../../../src/api/endpoints/OnTelegramEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/TelegramLink.php';
require_once __DIR__.'/../../../../src/model/User.php';

class FakeOnTelegramEndpointTelegramUtils {
    public $isAnonymousChat = false;
    public $telegramApiCalls = [];

    public function startAnonymousChat($chat_id, $user_id) {
    }

    public function linkChatUsingPin($pin, $chat_id, $user_id) {
        if ($pin != 'validpin') {
            throw new Exception('Error linking chat using PIN.');
        }
        $user = get_fake_user();
        $user->setFirstName('Fakefirst');
        $telegram_link = new TelegramLink();
        $telegram_link->setUser($user);
        return $telegram_link;
    }

    public function getFreshPinForChat($chat_id) {
        return 'freshpin';
    }

    public function getTelegramPinChars() {
        return 'abcdefghijklmnopqrstuvwxyz';
    }

    public function getTelegramPinLength() {
        return 8;
    }

    public function isAnonymousChat($chat_id) {
        return $this->isAnonymousChat;
    }

    public function getChatState($chat_id) {
        return [];
    }

    public function callTelegramApi($command, $args) {
        $this->telegramApiCalls[] = [$command, $args];
        return [];
    }
}

class FakeOnTelegramEndpointEnvUtils {
    public function getTelegramAuthenticityCode() {
        return 'some-token';
    }
}

function getFakeTelegramMessage($from_key, $chat_key, $text) {
    $users = [
        'test' => [
            'id' => 17089367,
            'is_bot' => false,
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testuser',
            'language_code' => 'en',
        ],
    ];
    $chats = [
        'test' => [
            'id' => 17089367,
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testuser',
            'type' => 'private',
        ],
    ];
    $from = is_string($from_key) ? $users[$from_key] : $from_key;
    $chat = is_string($chat_key) ? $chats[$chat_key] : $chat_key;
    $message = [
        'update_id' => 15880244,
        'message' => [
            'message_id' => 548,
            'from' => $from,
            'chat' => $chat,
            'date' => 1610644522,
            'text' => $text,
        ],
    ];
    return json_encode($message);
}

/**
 * @internal
 * @covers \OnTelegramEndpoint
 */
final class OnTelegramEndpointTest extends TestCase {
    public function testOnTelegramEndpointIdent(): void {
        $endpoint = new OnTelegramEndpoint();
        $this->assertSame('OnTelegramEndpoint', $endpoint->getIdent());
    }

    public function testOnTelegramEndpointParseInput(): void {
        global $_GET;
        $_GET = ['authenticityCode' => 'some-token'];
        $endpoint = new OnTelegramEndpoint();
        $parsed_input = $endpoint->parseInput();
        $this->assertSame([
            'authenticityCode' => 'some-token',
            'telegramEvent' => 'null',
        ], $parsed_input);
    }

    public function testOnTelegramEndpointWrongAuthenticityCode(): void {
        $telegram_utils = new FakeOnTelegramEndpointTelegramUtils();
        $server_config = new FakeOnTelegramEndpointEnvUtils();
        $logger = new Logger('OnTelegramEndpointTest');
        $endpoint = new OnTelegramEndpoint();
        $endpoint->setTelegramUtils($telegram_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        try {
            $endpoint->call([
                'authenticityCode' => 'wrong-token',
                'telegramEvent' => getFakeTelegramMessage('test', 'test', 'test'),
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(403, $err->getCode());
            $this->assertSame([], $telegram_utils->telegramApiCalls);
        }
    }

    public function testOnTelegramEndpointStartWithCorrectPin(): void {
        $telegram_utils = new FakeOnTelegramEndpointTelegramUtils();
        $server_config = new FakeOnTelegramEndpointEnvUtils();
        $logger = new Logger('OnTelegramEndpointTest');
        $endpoint = new OnTelegramEndpoint();
        $endpoint->setTelegramUtils($telegram_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
            'telegramEvent' => getFakeTelegramMessage('test', 'test', '/start validpin'),
        ]);

        $this->assertSame([], $result);
        $this->assertSame([
            ['sendChatAction', ['chat_id' => 17089367, 'action' => 'typing']],
            ['sendMessage', ['chat_id' => 17089367, 'text' => 'Hallo, Fakefirst!']],
        ], $telegram_utils->telegramApiCalls);
    }

    public function testOnTelegramEndpointStartWithInvalidPinFormat(): void {
        $telegram_utils = new FakeOnTelegramEndpointTelegramUtils();
        $server_config = new FakeOnTelegramEndpointEnvUtils();
        $logger = new Logger('OnTelegramEndpointTest');
        $endpoint = new OnTelegramEndpoint();
        $endpoint->setTelegramUtils($telegram_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
            'telegramEvent' => getFakeTelegramMessage('test', 'test', '/start invalidpinformat'),
        ]);

        $this->assertSame([], $result);
        $this->assertSame([
            ['sendChatAction', ['chat_id' => 17089367, 'action' => 'typing']],
            ['sendMessage', ['chat_id' => 17089367, 'text' => 'Hä?']],
        ], $telegram_utils->telegramApiCalls);
    }

    public function testOnTelegramEndpointStartWithErrorLinkingPin(): void {
        $telegram_utils = new FakeOnTelegramEndpointTelegramUtils();
        $server_config = new FakeOnTelegramEndpointEnvUtils();
        $logger = new Logger('OnTelegramEndpointTest');
        $endpoint = new OnTelegramEndpoint();
        $endpoint->setTelegramUtils($telegram_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
            'telegramEvent' => getFakeTelegramMessage('test', 'test', '/start aaaaaaaa'),
        ]);

        $this->assertSame([], $result);
        $this->assertSame([
            ['sendChatAction', ['chat_id' => 17089367, 'action' => 'typing']],
            ['sendMessage', ['chat_id' => 17089367, 'text' => 'Error linking chat using PIN.']],
        ], $telegram_utils->telegramApiCalls);
    }

    public function testOnTelegramEndpointStartAnonymousChat(): void {
        $telegram_utils = new FakeOnTelegramEndpointTelegramUtils();
        $server_config = new FakeOnTelegramEndpointEnvUtils();
        $logger = new Logger('OnTelegramEndpointTest');
        $endpoint = new OnTelegramEndpoint();
        $endpoint->setTelegramUtils($telegram_utils);
        $endpoint->setEnvUtils($server_config);
        $endpoint->setLogger($logger);

        $telegram_utils->isAnonymousChat = true;
        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
            'telegramEvent' => getFakeTelegramMessage('test', 'test', '/start'),
        ]);

        $this->assertSame([], $result);
        $this->assertSame([
            ['sendChatAction', ['chat_id' => 17089367, 'action' => 'typing']],
            ['sendMessage', [
                'chat_id' => 17089367,
                'parse_mode' => 'HTML',
                'text' => "<b>Willkommen bei der OL Zimmerberg!</b>\n\nDamit dieser Chat zu irgendwas zu gebrauchen ist, musst du <a href=\"https://olzimmerberg.ch/_/konto_telegram.php?pin=freshpin\">hier dein OLZ-Konto verlinken</a>.\n\nDieser Link wird nach 10 Minuten ungültig; klicke auf /start, um einen neuen Link zu erhalten.",
                'disable_web_page_preview' => true,
            ]],
        ], $telegram_utils->telegramApiCalls);
    }
}
