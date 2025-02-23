<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\OnTelegramEndpoint;
use Olz\Entity\TelegramLink;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\HttpFoundation\Request;

function getFakeTelegramMessage(mixed $from_key, mixed $chat_key, string $text): string {
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
    $json = json_encode($message);
    assert($json);
    return $json;
}

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\OnTelegramEndpoint
 */
final class OnTelegramEndpointTest extends UnitTestCase {
    public function testOnTelegramEndpointParseInput(): void {
        $get_params = ['authenticityCode' => 'some-token'];
        $content_json = json_encode(['json' => 'input']) ?: null;
        $request = new Request($get_params, [], [], [], [], [], $content_json);
        $endpoint = new OnTelegramEndpoint();
        $endpoint->runtimeSetup();
        $parsed_input = $endpoint->parseInput($request);
        $this->assertSame([
            'authenticityCode' => 'some-token',
            'telegramEvent' => '{"json":"input"}',
        ], $parsed_input);
    }

    public function testOnTelegramEndpointWrongAuthenticityCode(): void {
        $endpoint = new OnTelegramEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'authenticityCode' => 'wrong-token',
                'telegramEvent' => getFakeTelegramMessage('test', 'test', 'test'),
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                'INFO Valid user request',
                'WARNING HTTP error 403',
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
            $this->assertSame([], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
        }
    }

    public function testOnTelegramEndpointStartWithCorrectPin(): void {
        $endpoint = new OnTelegramEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
            'telegramEvent' => getFakeTelegramMessage('test', 'test', '/start validpin'),
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([], $result);
        $this->assertSame([
            ['sendChatAction', ['chat_id' => 17089367, 'action' => 'typing']],
            ['sendMessage', ['chat_id' => 17089367, 'text' => 'Hallo, Fakefirst!']],
        ], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
    }

    public function testOnTelegramEndpointStartWithInvalidPinFormat(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        // $telegram_link_repo = new FakeOnTelegramEndpointTelegramLinkRepository();
        // $entity_manager->repositories[TelegramLink::class] = $telegram_link_repo;
        $endpoint = new OnTelegramEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
            'telegramEvent' => getFakeTelegramMessage('test', 'test', '/start invalidpinformat'),
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([], $result);
        $this->assertSame([
            ['sendChatAction', ['chat_id' => 17089367, 'action' => 'typing']],
            ['sendMessage', ['chat_id' => 17089367, 'text' => 'Hä?']],
        ], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
    }

    public function testOnTelegramEndpointStartWithErrorLinkingPin(): void {
        $endpoint = new OnTelegramEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
            'telegramEvent' => getFakeTelegramMessage('test', 'test', '/start aaaaaaaa'),
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([], $result);
        $this->assertSame([
            ['sendChatAction', ['chat_id' => 17089367, 'action' => 'typing']],
            ['sendMessage', ['chat_id' => 17089367, 'text' => 'Error linking chat using PIN.']],
        ], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
    }

    public function testOnTelegramEndpointStartAnonymousChat(): void {
        $endpoint = new OnTelegramEndpoint();
        $endpoint->runtimeSetup();

        WithUtilsCache::get('telegramUtils')->isAnonymousChat = true;
        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
            'telegramEvent' => getFakeTelegramMessage('test', 'test', '/start'),
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([], $result);
        $this->assertSame([
            ['sendChatAction', ['chat_id' => 17089367, 'action' => 'typing']],
            ['sendMessage', [
                'chat_id' => 17089367,
                'parse_mode' => 'HTML',
                'text' => "<b>Willkommen bei der OL Zimmerberg!</b>\n\nDamit dieser Chat zu irgendwas zu gebrauchen ist, musst du <a href=\"https://olzimmerberg.ch/konto_telegram?pin=freshpin\">hier dein OLZ-Konto verlinken</a>.\n\nDieser Link wird nach 10 Minuten ungültig; wähle /start, um einen neuen Link zu erhalten.",
                'disable_web_page_preview' => true,
            ]],
        ], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
    }

    public function testOnTelegramEndpointNoChatId(): void {
        $endpoint = new OnTelegramEndpoint();
        $endpoint->runtimeSetup();

        WithUtilsCache::get('telegramUtils')->isAnonymousChat = true;
        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
            'telegramEvent' => getFakeTelegramMessage('test', [], '/start'),
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'NOTICE Telegram message without chat_id',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([], $result);
        $this->assertSame([], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
    }

    public function testOnTelegramEndpointIchCommand(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        // $telegram_link_repo = new FakeOnTelegramEndpointTelegramLinkRepository();
        // $entity_manager->repositories[TelegramLink::class] = $telegram_link_repo;
        $endpoint = new OnTelegramEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
            'telegramEvent' => getFakeTelegramMessage('test', 'test', '/ich'),
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([], $result);
        $this->assertSame([
            ['sendChatAction', ['chat_id' => 17089367, 'action' => 'typing']],
            ['sendMessage', [
                'chat_id' => 17089367,
                'parse_mode' => 'HTML',
                'text' => "<b>Du bist angemeldet als:</b>\n<b>Name:</b> Default User\n<b>Benutzername:</b> default\n<b>E-Mail:</b> default-user@staging.olzimmerberg.ch",
            ]],
        ], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
    }
}
