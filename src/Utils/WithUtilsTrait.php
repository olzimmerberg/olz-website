<?php

namespace Olz\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Olz\Captcha\Utils\CaptchaUtils;
use Olz\Fetchers\SolvFetcher;
use Olz\Termine\Utils\TermineUtils;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait WithUtilsTrait {
    use AuthUtilsTrait;
    use DateUtilsTrait;
    use DbUtilsTrait;
    use DevDataUtilsTrait;
    use EmailUtilsTrait;
    use EntityUtilsTrait;
    use EnvUtilsTrait;
    use GeneralUtilsTrait;
    use HtmlUtilsTrait;
    use HttpUtilsTrait;
    use IdUtilsTrait;
    use ImageUtilsTrait;
    use MapUtilsTrait;
    use SessionTrait;
    use SymfonyUtilsTrait;
    use TelegramUtilsTrait;
    use UploadUtilsTrait;
    use LoggerAwareTrait;

    // --- Symfony dependency injection ---

    protected MailerInterface $mailer;

    #[Required]
    public function setMailer(MailerInterface $mailer): void {
        $this->mailer = $mailer;
    }

    protected MessageBusInterface $messageBus;

    #[Required]
    public function setMessageBus(MessageBusInterface $messageBus): void {
        $this->messageBus = $messageBus;
    }

    // --- OLZ dependency injection ---

    public function entityManager(): EntityManagerInterface {
        $util = WithUtilsCache::get('entityManager');
        assert($util);
        return $util;
    }

    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager): void {
        WithUtilsCache::set('entityManager', $entityManager);
    }

    public function log(): LoggerInterface {
        $util = WithUtilsCache::get('log');
        assert($util);
        return $util;
    }

    #[Required]
    public function setLog(LoggerInterface $log): void {
        $this->logger = $log;
        WithUtilsCache::set('log', $log);
    }

    public function captchaUtils(): CaptchaUtils {
        $util = WithUtilsCache::get('captchaUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setCaptchaUtils(CaptchaUtils $captchaUtils): void {
        WithUtilsCache::set('captchaUtils', $captchaUtils);
    }

    public function solvFetcher(): SolvFetcher {
        $util = WithUtilsCache::get('solvFetcher');
        assert($util);
        return $util;
    }

    #[Required]
    public function setSolvFetcher(SolvFetcher $solvFetcher): void {
        WithUtilsCache::set('solvFetcher', $solvFetcher);
    }

    public function termineUtils(): TermineUtils {
        $util = WithUtilsCache::get('termineUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setTermineUtils(TermineUtils $termineUtils): void {
        WithUtilsCache::set('termineUtils', $termineUtils);
    }

    // Legacy implementation
    // TODO: Migrate away!

    /**
     * @return array<string, string>
     */
    public function getParams(): array {
        return $this->getOrCreate('getParams');
    }

    /**
     * @return array<string, string>
     */
    public function createGetParams(): array {
        global $_GET;
        return $_GET;
    }

    /**
     * @param array<string, string> $getParams
     */
    public function setGetParams(array $getParams): void {
        WithUtilsCache::set('getParams', $getParams);
    }

    /**
     * @return array<string, mixed>
     */
    public function server(): array {
        return $this->getOrCreate('server');
    }

    /**
     * @return array<string, mixed>
     */
    public function createServer(): array {
        global $_SERVER;
        return $_SERVER;
    }

    /**
     * @param array<string, mixed> $server
     */
    public function setServer(array $server): void {
        WithUtilsCache::set('server', $server);
    }

    // ---

    /**
     * @return array<string, mixed>
     */
    public function getAllUtils(): array {
        return WithUtilsCache::getAll();
    }

    /**
     * @param array<string, mixed> $all_utils
     */
    public function setAllUtils(array $all_utils): void {
        WithUtilsCache::setAll($all_utils);
    }

    protected function getOrCreate(string $util_name): mixed {
        $util = WithUtilsCache::get($util_name);
        if ($util) {
            return $util;
        }
        $cap_util_name = ucfirst($util_name);
        $creator_name = "create{$cap_util_name}";
        $util = $this->{$creator_name}();
        WithUtilsCache::set($util_name, $util);
        return $util;
    }
}
