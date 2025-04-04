<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Entity\TelegramLink;
use Olz\Entity\Users\User;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Utils\TelegramUtils;

class FakeTelegramUtils extends TelegramUtils {
    public bool $isAnonymousChat = false;
    public bool $configurationSent = false;
    /** @var array<array{0: string, 1: array<string, mixed>}> */
    public array $telegramApiCalls = [];

    public function renderMarkdown(string $markdown): string {
        return $markdown;
    }

    public function startAnonymousChat(string $chat_id, string $user_id): TelegramLink {
        return new TelegramLink();
    }

    public function linkChatUsingPin(string $pin, string $chat_id, string $user_id): TelegramLink {
        if ($pin != 'validpin') {
            throw new \Exception('Error linking chat using PIN.');
        }
        $user = FakeUser::defaultUser(true);
        $user->setFirstName('Fakefirst');
        $telegram_link = new TelegramLink();
        $telegram_link->setUser($user);
        return $telegram_link;
    }

    public function getFreshPinForChat(string $chat_id): string {
        return 'freshpin';
    }

    public function getTelegramPinChars(): string {
        return 'abcdefghijklmnopqrstuvwxyz';
    }

    public function getTelegramPinLength(): int {
        return 8;
    }

    public function isAnonymousChat(string $chat_id): bool {
        return $this->isAnonymousChat;
    }

    public function getChatState(string $chat_id): array {
        return [];
    }

    public function sendConfiguration(): void {
        $this->configurationSent = true;
    }

    /** @param array<string, mixed> $args */
    public function callTelegramApi(string $command, array $args): array {
        if ($args['chat_id'] == 'provoke_error') {
            throw new \Exception('provoked telegram error');
        }
        $this->telegramApiCalls[] = [$command, $args];
        return [];
    }

    public function getBotName(): string {
        return 'bot-name';
    }

    public function getFreshPinForUser(User $user): string {
        if ($user->getUsername() == 'admin') {
            return 'correct-pin';
        }
        return 'wrong-pin';
    }
}
