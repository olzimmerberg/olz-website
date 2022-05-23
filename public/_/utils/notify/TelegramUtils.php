<?php

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\MarkdownConverter;

require_once __DIR__.'/../../config/vendor/autoload.php';
require_once __DIR__.'/../../model/TelegramLink.php';
require_once __DIR__.'/../../model/User.php';
require_once __DIR__.'/../WithUtilsTrait.php';

class TelegramUtils {
    use WithUtilsTrait;
    use Psr\Log\LoggerAwareTrait;
    public const UTILS = [
        'dateUtils',
        'entityManager',
        'envUtils',
        'logger',
    ];

    public static function fromEnv() {
        require_once __DIR__.'/../../fetchers/TelegramFetcher.php';
        require_once __DIR__.'/../../model/index.php';

        $telegram_fetcher = new TelegramFetcher();

        $instance = new self();
        $instance->setTelegramFetcher($telegram_fetcher);
        $instance->populateFromEnv(self::UTILS);

        return $instance;
    }

    public function setTelegramFetcher($telegramFetcher) {
        $this->telegramFetcher = $telegramFetcher;
    }

    public function getBotName() {
        return $this->envUtils->getTelegramBotName();
    }

    private function getBotToken() {
        return $this->envUtils->getTelegramBotToken();
    }

    public function getFreshPinForUser(User $user) {
        $telegram_link = $this->startChatForUser($user);
        return $telegram_link->getPin();
    }

    public function startAnonymousChat($chat_id, $user_id): TelegramLink {
        $telegram_link_repo = $this->entityManager->getRepository(TelegramLink::class);
        $existing = $telegram_link_repo->findOneBy(['telegram_chat_id' => $chat_id]);
        if ($existing != null) {
            return $existing;
        }

        $now = new DateTime($this->dateUtils->getIsoNow());

        $telegram_link = new TelegramLink();
        $telegram_link->setPin(null);
        $telegram_link->setPinExpiresAt(null);
        $telegram_link->setUser(null);
        $telegram_link->setTelegramChatId($chat_id);
        $telegram_link->setTelegramUserId($user_id);
        $telegram_link->setTelegramChatState([]);
        $telegram_link->setCreatedAt($now);
        $telegram_link->setLinkedAt(null);

        $this->entityManager->persist($telegram_link);
        $this->entityManager->flush();
        return $telegram_link;
    }

    public function startChatForUser(User $user): TelegramLink {
        $telegram_link_repo = $this->entityManager->getRepository(TelegramLink::class);
        $existing_telegram_link = $telegram_link_repo->findOneBy(['user' => $user->getId()]);

        if ($existing_telegram_link != null) {
            $this->setNewPinForLink($existing_telegram_link);
            return $existing_telegram_link;
        }

        $now = new DateTime($this->dateUtils->getIsoNow());

        $telegram_link = new TelegramLink();
        $telegram_link->setUser($user);
        $telegram_link->setTelegramChatId(null);
        $telegram_link->setTelegramUserId(null);
        $telegram_link->setTelegramChatState([]);
        $telegram_link->setCreatedAt($now);
        $telegram_link->setLinkedAt(null);
        $this->setNewPinForLink($telegram_link);

        $this->entityManager->persist($telegram_link);
        $this->entityManager->flush();
        return $telegram_link;
    }

    public function linkChatUsingPin($pin, $chat_id, $user_id) {
        $now = new DateTime($this->dateUtils->getIsoNow());

        $telegram_link_repo = $this->entityManager->getRepository(TelegramLink::class);
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

        $this->entityManager->flush();
        return $existing_telegram_link;
    }

    public function linkUserUsingPin($pin, $user) {
        $now = new DateTime($this->dateUtils->getIsoNow());

        $telegram_link_repo = $this->entityManager->getRepository(TelegramLink::class);
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
            $this->entityManager->remove($redundant_telegram_link);
        }

        $this->entityManager->flush();
        return $existing_telegram_link;
    }

    public function getTelegramExpirationInterval(): DateInterval {
        return DateInterval::createFromDateString("+10 min");
    }

    public function getFreshPinForChat($chat_id) {
        $telegram_link_repo = $this->entityManager->getRepository(TelegramLink::class);
        $existing = $telegram_link_repo->findOneBy(['telegram_chat_id' => $chat_id]);
        if ($existing == null) {
            throw new Exception('Unbekannter Chat.');
        }
        $telegram_link = $this->setNewPinForLink($existing);
        return $telegram_link->getPin();
    }

    public function setNewPinForLink($telegram_link) {
        $pin = $this->generateUniqueTelegramPin();
        $now = new DateTime($this->dateUtils->getIsoNow());
        $pin_expires_at = $now->add($this->getTelegramExpirationInterval());

        $telegram_link->setPin($pin);
        $telegram_link->setPinExpiresAt($pin_expires_at);

        $this->entityManager->flush();

        return $telegram_link;
    }

    public function generateUniqueTelegramPin() {
        while (true) {
            $pin = $this->generateTelegramPin();
            $telegram_link_repo = $this->entityManager->getRepository(TelegramLink::class);
            $existing = $telegram_link_repo->findOneBy(['pin' => $pin]);
            $now = new DateTime($this->dateUtils->getIsoNow());
            if ($existing == null || $now > $existing->getPinExpiresAt()) {
                return $pin;
            }
        }
        // @codeCoverageIgnoreStart
        // Reason: Unreachable.
    }

    // @codeCoverageIgnoreEnd

    public function generateTelegramPin() {
        $pin_chars = $this->getTelegramPinChars();
        $pin_length = $this->getTelegramPinLength();
        $pin = '';
        for ($i = 0; $i < $pin_length; $i++) {
            $char_index = random_int(0, strlen($pin_chars) - 1);
            $pin .= substr($pin_chars, $char_index, 1);
        }
        return $pin;
    }

    public function getTelegramPinChars(): string {
        return '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    }

    public function getTelegramPinLength(): int {
        return 8;
    }

    public function isAnonymousChat($chat_id) {
        $telegram_link_repo = $this->entityManager->getRepository(TelegramLink::class);
        $existing = $telegram_link_repo->findOneBy(['telegram_chat_id' => $chat_id]);
        if ($existing == null) {
            return true;
        }
        return $existing->getUser() == null;
    }

    public function getChatState($chat_id) {
        $telegram_link_repo = $this->entityManager->getRepository(TelegramLink::class);
        $existing = $telegram_link_repo->findOneBy(['telegram_chat_id' => $chat_id]);
        if ($existing == null) {
            return null;
        }
        return $existing->getTelegramChatState();
    }

    public function setChatState($chat_id, $chat_state) {
        $telegram_link_repo = $this->entityManager->getRepository(TelegramLink::class);
        $existing = $telegram_link_repo->findOneBy(['telegram_chat_id' => $chat_id]);
        if ($existing == null) {
            throw new Exception('Unbekannter Chat.');
        }
        return $existing->setTelegramChatState($chat_state);
    }

    public function sendConfiguration() {
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
            $this->logger->error("Telegram API: Could not 'setMyCommands'");
        }
    }

    public function callTelegramApi($command, $args) {
        $response = $this->telegramFetcher->callTelegramApi($command, $args, $this->getBotToken());
        if (!$response) {
            $this->logger->warning("Telegram API response was empty");
            throw new Exception(json_encode(['ok' => false]));
        }
        if (isset($response['ok']) && !$response['ok']) {
            $error_code = $response['error_code'] ?? null;
            $description = $response['description'] ?? null;
            if (
                $error_code == 403
                && $description == 'Forbidden: bot was blocked by the user'
                && isset($args['chat_id'])
            ) {
                $this->logger->notice("We're blocked. Remove telegram link!");
                $telegram_link_repo = $this->entityManager->getRepository(TelegramLink::class);
                $telegram_link = $telegram_link_repo->findOneBy([
                    'telegram_chat_id' => $args['chat_id'],
                ]);
                $this->entityManager->remove($telegram_link);
                $this->entityManager->flush();
            }
            $response_json = json_encode($response);
            $this->logger->notice("Telegram API response was not OK: {$response_json}");
            throw new Exception($response_json);
        }
        $this->logger->info("Telegram API call successful", [$command, $args, $response]);
        return $response;
    }

    public function renderMarkdown($markdown) {
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
        ]);
        $environment->addExtension(new InlinesOnlyExtension());
        $environment->addExtension(new StrikethroughExtension());
        $environment->addExtension(new AutolinkExtension());
        $converter = new MarkdownConverter($environment);
        $rendered = $converter->convertToHtml($markdown);
        return strval($rendered);
    }
}
