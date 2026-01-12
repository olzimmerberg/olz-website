<?php

namespace Olz\Utils;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\MarkdownConverter;
use Olz\Entity\TelegramLink;
use Olz\Entity\Users\User;

class TelegramUtils {
    use WithUtilsTrait;

    public function getBotName(): string {
        return $this->envUtils()->getTelegramBotName();
    }

    private function getBotToken(): string {
        return $this->envUtils()->getTelegramBotToken();
    }

    public function getFreshPinForUser(User $user): string {
        $telegram_link = $this->startChatForUser($user);
        $pin = $telegram_link->getPin();
        $this->generalUtils()->checkNotNull($pin, "Telegram link pin was null");
        return $pin;
    }

    public function startAnonymousChat(string $chat_id, string $user_id): TelegramLink {
        $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
        $existing = $telegram_link_repo->findOneBy(['telegram_chat_id' => $chat_id]);
        if ($existing != null) {
            return $existing;
        }

        $now = new \DateTime($this->dateUtils()->getIsoNow());

        $telegram_link = new TelegramLink();
        $telegram_link->setPin(null);
        $telegram_link->setPinExpiresAt(null);
        $telegram_link->setUser(null);
        $telegram_link->setTelegramChatId($chat_id);
        $telegram_link->setTelegramUserId($user_id);
        $telegram_link->setTelegramChatState([]);
        $telegram_link->setCreatedAt($now);
        $telegram_link->setLinkedAt(null);

        $this->entityManager()->persist($telegram_link);
        $this->entityManager()->flush();
        return $telegram_link;
    }

    public function startChatForUser(User $user): TelegramLink {
        $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
        $existing_telegram_link = $telegram_link_repo->findOneBy(['user' => $user->getId()]);

        if ($existing_telegram_link != null) {
            $this->setNewPinForLink($existing_telegram_link);
            return $existing_telegram_link;
        }

        $now = new \DateTime($this->dateUtils()->getIsoNow());

        $telegram_link = new TelegramLink();
        $telegram_link->setUser($user);
        $telegram_link->setTelegramChatId(null);
        $telegram_link->setTelegramUserId(null);
        $telegram_link->setTelegramChatState([]);
        $telegram_link->setCreatedAt($now);
        $telegram_link->setLinkedAt(null);
        $this->setNewPinForLink($telegram_link);

        $this->entityManager()->persist($telegram_link);
        $this->entityManager()->flush();
        return $telegram_link;
    }

    public function linkChatUsingPin(string $pin, string $chat_id, string $user_id): TelegramLink {
        $now = new \DateTime($this->dateUtils()->getIsoNow());

        $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
        $existing_telegram_link = $telegram_link_repo->findOneBy(['pin' => $pin]);

        if ($existing_telegram_link == null) {
            throw new \Exception('Falscher PIN.');
        }
        if ($now > $existing_telegram_link->getPinExpiresAt()) {
            throw new \Exception('PIN ist abgelaufen.');
        }
        $existing_telegram_link->setTelegramChatId($chat_id);
        $existing_telegram_link->setTelegramUserId($user_id);
        $existing_telegram_link->setLinkedAt($now);

        $this->entityManager()->flush();
        return $existing_telegram_link;
    }

    public function linkUserUsingPin(string $pin, User $user): TelegramLink {
        $now = new \DateTime($this->dateUtils()->getIsoNow());

        $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
        $existing_telegram_link = $telegram_link_repo->findOneBy(['pin' => $pin]);
        $redundant_telegram_links = $telegram_link_repo->findBy(['user' => $user->getId()]);

        if ($existing_telegram_link == null) {
            throw new \Exception('Falscher PIN.');
        }
        if ($now > $existing_telegram_link->getPinExpiresAt()) {
            throw new \Exception('PIN ist abgelaufen.');
        }
        $existing_telegram_link->setUser($user);
        $existing_telegram_link->setLinkedAt($now);

        foreach ($redundant_telegram_links as $redundant_telegram_link) {
            $this->entityManager()->remove($redundant_telegram_link);
        }

        $this->entityManager()->flush();
        return $existing_telegram_link;
    }

    public function getTelegramExpirationInterval(): \DateInterval {
        return \DateInterval::createFromDateString("+10 min");
    }

    public function getFreshPinForChat(string $chat_id): string {
        $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
        $existing = $telegram_link_repo->findOneBy(['telegram_chat_id' => $chat_id]);
        if ($existing == null) {
            throw new \Exception('Unbekannter Chat.');
        }
        $telegram_link = $this->setNewPinForLink($existing);
        $pin = $telegram_link->getPin();
        $this->generalUtils()->checkNotNull($pin, "Telegram link pin was null");
        return $pin;
    }

    public function setNewPinForLink(TelegramLink $telegram_link): TelegramLink {
        $pin = $this->generateUniqueTelegramPin();
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $pin_expires_at = $now->add($this->getTelegramExpirationInterval());

        $telegram_link->setPin($pin);
        $telegram_link->setPinExpiresAt($pin_expires_at);

        $this->entityManager()->flush();

        return $telegram_link;
    }

    public function generateUniqueTelegramPin(): string {
        while (true) {
            $pin = $this->generateTelegramPin();
            $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
            $existing = $telegram_link_repo->findOneBy(['pin' => $pin]);
            $now = new \DateTime($this->dateUtils()->getIsoNow());
            if ($existing == null || $now > $existing->getPinExpiresAt()) {
                return $pin;
            }
        }
        // @codeCoverageIgnoreStart
        // Reason: Unreachable.
    }

    // @codeCoverageIgnoreEnd

    public function generateTelegramPin(): string {
        $pin_chars = $this->getTelegramPinChars();
        $pin_length = $this->getTelegramPinLength();
        $pin = '';
        for ($i = 0; $i < $pin_length; $i++) {
            $char_index = random_int(0, strlen($pin_chars) - 1);
            $pin .= substr($pin_chars, $char_index, 1);
        }
        return $pin;
    }

    /** @return non-empty-string */
    public function getTelegramPinChars(): string {
        return '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    }

    public function getTelegramPinLength(): int {
        return 8;
    }

    public function isAnonymousChat(string $chat_id): bool {
        $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
        $existing = $telegram_link_repo->findOneBy(['telegram_chat_id' => $chat_id]);
        if ($existing == null) {
            return true;
        }
        return $existing->getUser() == null;
    }

    /** @return array<string, mixed> */
    public function getChatState(string $chat_id): ?array {
        $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
        $existing = $telegram_link_repo->findOneBy(['telegram_chat_id' => $chat_id]);
        if ($existing == null) {
            return null;
        }
        return $existing->getTelegramChatState();
    }

    /** @param array<string, mixed> $chat_state */
    public function setChatState(string $chat_id, array $chat_state): void {
        $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
        $existing = $telegram_link_repo->findOneBy(['telegram_chat_id' => $chat_id]);
        if ($existing == null) {
            throw new \Exception('Unbekannter Chat.');
        }
        $existing->setTelegramChatState($chat_state);
    }

    public function sendConfiguration(): void {
        try {
            $response = $this->callTelegramApi('setMyCommands', [
                'commands' => json_encode([
                    [
                        'command' => '/ich',
                        'description' => 'Wer bin ich?',
                    ],
                ]),
                'scope' => json_encode(['type' => 'all_private_chats']),
            ]);
            $response_json = json_encode($response);
        } catch (\Throwable $th) {
            $this->log()->error("Telegram API: Could not 'setMyCommands'");
        }

        $base_url = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();
        $authenticity_code = $this->envUtils()->getTelegramAuthenticityCode();
        $query = "authenticityCode={$authenticity_code}";
        $url = "{$base_url}{$code_href}api/onTelegram?{$query}";
        try {
            $response = $this->callTelegramApi('setWebhook', [
                'url' => $url,
            ]);
            $response_json = json_encode($response);
        } catch (\Throwable $th) {
            $this->log()->error("Telegram API: Could not 'setWebhook'");
        }
    }

    /**
     * @param array<string, mixed> $args
     *
     * @return array<string, mixed>
     */
    public function callTelegramApi(string $command, array $args): array {
        $response = $this->fetchTelegramApi($command, $args, $this->getBotToken());
        if (!$response) {
            $this->log()->warning("Telegram API response was empty");
            $json = json_encode(['ok' => false]);
            throw new \Exception("{$json}");
        }
        if (isset($response['ok']) && !$response['ok']) {
            $error_code = $response['error_code'] ?? null;
            $description = $response['description'] ?? null;
            $response_json = json_encode($response);
            if (
                $error_code == 403
                && (
                    $description == 'Forbidden: bot was blocked by the user'
                    || $description == 'Forbidden: user is deactivated'
                )
                && isset($args['chat_id'])
            ) {
                $this->log()->notice("Telegram API response was not OK: {$response_json}");
                $this->log()->notice("Permanently forbidden. Remove telegram link!");
                $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
                $telegram_link = $telegram_link_repo->findOneBy([
                    'telegram_chat_id' => $args['chat_id'],
                ]);
                if ($telegram_link) {
                    $this->entityManager()->remove($telegram_link);
                    $this->entityManager()->flush();
                    $this->log()->notice("Telegram link removed!");
                }
                throw new \Exception("{$response_json}");
            }
            $this->log()->error("Telegram API response was not OK: {$response_json}");
            throw new \Exception("{$response_json}");
        }
        $this->log()->info("Telegram API {$command} call successful", [$args, $response]);
        return $response;
    }

    /**
     * @param array<string, mixed> $args
     *
     * @return ?array<string, mixed>
     */
    public function fetchTelegramApi(string $command, array $args, string $bot_token): ?array {
        $telegram_url = "https://api.telegram.org/bot{$bot_token}/{$command}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $telegram_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $telegram_result = curl_exec($ch);
        return json_decode(!is_bool($telegram_result) ? $telegram_result : '', true);
    }

    public function renderMarkdown(string $markdown): string {
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
        ]);
        $environment->addExtension(new InlinesOnlyExtension());
        $environment->addExtension(new StrikethroughExtension());
        $environment->addExtension(new AutolinkExtension());
        $converter = new MarkdownConverter($environment);
        $rendered = $converter->convert($markdown);
        return strval($rendered);
    }
}
