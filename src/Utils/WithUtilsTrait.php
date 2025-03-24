<?php

namespace Olz\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Olz\Fetchers\SolvFetcher;
use Olz\Termine\Utils\TermineUtils;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait WithUtilsTrait {
    use LoggerAwareTrait;

    // --- Symfony dependency injection ---

    #[Required]
    public function setLogger(LoggerInterface $logger): void {
        $this->logger = $logger;
    }

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

    /**
     * @var array<string>
     */
    public static $ALL_UTILS = [
        'authUtils',
        'dateUtils',
        'dbUtils',
        'devDataUtils',
        'emailUtils',
        'entityManager',
        'entityUtils',
        'envUtils',
        'generalUtils',
        'getParams',
        'htmlUtils',
        'httpUtils',
        'idUtils',
        'imageUtils',
        'log',
        'mapUtils',
        'server',
        'session',
        'solvFetcher',
        'stravaUtils',
        'symfonyUtils',
        'telegramUtils',
        'uploadUtils',
    ];

    public function authUtils(): AuthUtils {
        $util = WithUtilsCache::get('authUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setAuthUtils(AuthUtils $authUtils): void {
        WithUtilsCache::set('authUtils', $authUtils);
    }

    public function dateUtils(): DateUtils {
        $util = WithUtilsCache::get('dateUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setDateUtils(DateUtils $dateUtils): void {
        WithUtilsCache::set('dateUtils', $dateUtils);
    }

    public function dbUtils(): DbUtils {
        $util = WithUtilsCache::get('dbUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setDbUtils(DbUtils $dbUtils): void {
        WithUtilsCache::set('dbUtils', $dbUtils);
    }

    public function devDataUtils(): DevDataUtils {
        $util = WithUtilsCache::get('devDataUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setDevDataUtils(DevDataUtils $devDataUtils): void {
        WithUtilsCache::set('devDataUtils', $devDataUtils);
    }

    public function emailUtils(): EmailUtils {
        $util = WithUtilsCache::get('emailUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setEmailUtils(EmailUtils $emailUtils): void {
        WithUtilsCache::set('emailUtils', $emailUtils);
    }

    public function entityManager(): EntityManagerInterface {
        $util = WithUtilsCache::get('entityManager');
        assert($util);
        return $util;
    }

    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager): void {
        WithUtilsCache::set('entityManager', $entityManager);
    }

    public function entityUtils(): EntityUtils {
        $util = WithUtilsCache::get('entityUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setEntityUtils(EntityUtils $entityUtils): void {
        WithUtilsCache::set('entityUtils', $entityUtils);
    }

    public function envUtils(): EnvUtils {
        $util = WithUtilsCache::get('envUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setEnvUtils(EnvUtils $envUtils): void {
        WithUtilsCache::set('envUtils', $envUtils);
    }

    public function generalUtils(): GeneralUtils {
        $util = WithUtilsCache::get('generalUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setGeneralUtils(GeneralUtils $generalUtils): void {
        WithUtilsCache::set('generalUtils', $generalUtils);
    }

    public function htmlUtils(): HtmlUtils {
        $util = WithUtilsCache::get('htmlUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setHtmlUtils(HtmlUtils $htmlUtils): void {
        WithUtilsCache::set('htmlUtils', $htmlUtils);
    }

    public function httpUtils(): HttpUtils {
        $util = WithUtilsCache::get('httpUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setHttpUtils(HttpUtils $httpUtils): void {
        WithUtilsCache::set('httpUtils', $httpUtils);
    }

    public function idUtils(): IdUtils {
        $util = WithUtilsCache::get('idUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setIdUtils(IdUtils $idUtils): void {
        WithUtilsCache::set('idUtils', $idUtils);
    }

    public function imageUtils(): ImageUtils {
        $util = WithUtilsCache::get('imageUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setImageUtils(ImageUtils $imageUtils): void {
        WithUtilsCache::set('imageUtils', $imageUtils);
    }

    public function mapUtils(): MapUtils {
        $util = WithUtilsCache::get('mapUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setMapUtils(MapUtils $mapUtils): void {
        WithUtilsCache::set('mapUtils', $mapUtils);
    }

    public function recaptchaUtils(): RecaptchaUtils {
        $util = WithUtilsCache::get('recaptchaUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setRecaptchaUtils(RecaptchaUtils $recaptchaUtils): void {
        WithUtilsCache::set('recaptchaUtils', $recaptchaUtils);
    }

    public function session(): AbstractSession {
        return $this->getOrCreate('session');
    }

    public function createSession(): AbstractSession {
        return new StandardSession();
    }

    public function setSession(AbstractSession $session): void {
        WithUtilsCache::set('session', $session);
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

    public function stravaUtils(): StravaUtils {
        $util = WithUtilsCache::get('stravaUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setStravaUtils(StravaUtils $stravaUtils): void {
        WithUtilsCache::set('stravaUtils', $stravaUtils);
    }

    public function symfonyUtils(): SymfonyUtils {
        $util = WithUtilsCache::get('symfonyUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setSymfonyUtils(SymfonyUtils $symfonyUtils): void {
        WithUtilsCache::set('symfonyUtils', $symfonyUtils);
    }

    public function telegramUtils(): TelegramUtils {
        $util = WithUtilsCache::get('telegramUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setTelegramUtils(TelegramUtils $telegramUtils): void {
        WithUtilsCache::set('telegramUtils', $telegramUtils);
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

    public function uploadUtils(): UploadUtils {
        $util = WithUtilsCache::get('uploadUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setUploadUtils(UploadUtils $uploadUtils): void {
        WithUtilsCache::set('uploadUtils', $uploadUtils);
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

    public function log(): Logger {
        return $this->getOrCreate('log');
    }

    public function createLog(): Logger {
        $logs_utils = LogsUtils::fromEnv();
        return $logs_utils->getLogger('');
    }

    public function setLog(Logger $log): void {
        $this->setLogger($log);
        WithUtilsCache::set('log', $log);
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
