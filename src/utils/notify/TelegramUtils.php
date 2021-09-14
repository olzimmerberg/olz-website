<?php

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\HtmlRenderer;

require_once __DIR__.'/../../config/vendor/autoload.php';
require_once __DIR__.'/../../model/TelegramLink.php';
require_once __DIR__.'/../../model/User.php';

class TelegramUtils {
    private $botName;
    private $botToken;
    private $telegramFetcher;
    private $entityManager;
    private $dateUtils;

    public function __construct($botName, $botToken, $telegramFetcher, $entityManager, $dateUtils) {
        $this->botName = $botName;
        $this->botToken = $botToken;
        $this->telegramFetcher = $telegramFetcher;
        $this->entityManager = $entityManager;
        $this->dateUtils = $dateUtils;
    }

    public function getBotName() {
        return $this->botName;
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

        if ($existing_telegram_link == null) {
            throw new \Exception('Falscher PIN.');
        }
        if ($now > $existing_telegram_link->getPinExpiresAt()) {
            throw new \Exception('PIN ist abgelaufen.');
        }
        $existing_telegram_link->setUser($user);
        $existing_telegram_link->setLinkedAt($now);

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

    public function callTelegramApi($command, $args) {
        $response = $this->telegramFetcher->callTelegramApi($command, $args, $this->botToken);
        if (!$response) {
            // TODO: logging
            throw new Exception(json_encode(['ok' => false]));
        }
        if (isset($response['ok']) && !$response['ok']) {
            // TODO: logging
            throw new Exception(json_encode($response));
        }
        return $response;
    }

    public function renderMarkdown($markdown) {
        $environment = new Environment();
        $environment->addExtension(new InlinesOnlyExtension());
        $environment->addExtension(new StrikethroughExtension());
        $environment->addExtension(new AutolinkExtension());
        $environment->setConfig([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
        ]);

        $parser = new DocParser($environment);
        $document = $parser->parse($markdown);

        $html_renderer = new HtmlRenderer($environment);
        return $html_renderer->renderBlock($document);
    }

    public static function fromEnv() {
        global $_CONFIG, $entityManager;
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../../config/server.php';
        require_once __DIR__.'/../../fetchers/TelegramFetcher.php';
        require_once __DIR__.'/../../model/index.php';
        require_once __DIR__.'/../../utils/date/LiveDateUtils.php';

        $telegram_fetcher = new TelegramFetcher();
        $live_date_utils = new LiveDateUtils();

        return new self(
            $_CONFIG->getTelegramBotName(),
            $_CONFIG->getTelegramBotToken(),
            $telegram_fetcher,
            $entityManager,
            $live_date_utils,
        );
    }
}

function getTelegramUtilsFromEnv() {
    // @codeCoverageIgnoreStart
    // Reason: functions cannot be covered.
    return TelegramUtils::fromEnv();
    // @codeCoverageIgnoreEnd
}
