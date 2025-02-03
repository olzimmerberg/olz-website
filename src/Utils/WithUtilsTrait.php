<?php

namespace Olz\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Olz\Fetchers\SolvFetcher;
use Olz\Termine\Utils\TermineUtils;
use PhpTypeScriptApi\Fields\FieldUtils;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait WithUtilsTrait {
    // --- OLZ dependency injection ---

    use \Psr\Log\LoggerAwareTrait;

    // --- Symfony dependency injection ---

    protected ?MailerInterface $mailer = null;

    #[Required]
    public function setMailer(?MailerInterface $mailer): void {
        $this->mailer = $mailer;
    }

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
        'fieldUtils',
        'fileUtils',
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
        return $this->getOrCreate('authUtils');
    }

    public function createAuthUtils(): AuthUtils {
        return AuthUtils::fromEnv();
    }

    public function setAuthUtils(AuthUtils $authUtils): void {
        WithUtilsCache::set('authUtils', $authUtils);
    }

    public function dateUtils(): AbstractDateUtils {
        return $this->getOrCreate('dateUtils');
    }

    public function createDateUtils(): AbstractDateUtils {
        return AbstractDateUtils::fromEnv();
    }

    public function setDateUtils(AbstractDateUtils $dateUtils): void {
        WithUtilsCache::set('dateUtils', $dateUtils);
    }

    public function dbUtils(): DbUtils {
        return $this->getOrCreate('dbUtils');
    }

    public function createDbUtils(): DbUtils {
        return DbUtils::fromEnv();
    }

    public function setDbUtils(DbUtils $dbUtils): void {
        WithUtilsCache::set('dbUtils', $dbUtils);
    }

    public function devDataUtils(): DevDataUtils {
        return $this->getOrCreate('devDataUtils');
    }

    public function createDevDataUtils(): DevDataUtils {
        return DevDataUtils::fromEnv();
    }

    public function setDevDataUtils(DevDataUtils $devDataUtils): void {
        WithUtilsCache::set('devDataUtils', $devDataUtils);
    }

    public function emailUtils(): EmailUtils {
        return $this->getOrCreate('emailUtils');
    }

    public function createEmailUtils(): EmailUtils {
        $emailUtils = EmailUtils::fromEnv();
        $emailUtils->setMailer($this->mailer);
        return $emailUtils;
    }

    public function setEmailUtils(EmailUtils $emailUtils): void {
        WithUtilsCache::set('emailUtils', $emailUtils);
    }

    public function entityManager(): EntityManagerInterface {
        return $this->getOrCreate('entityManager');
    }

    public function createEntityManager(): EntityManagerInterface {
        return DbUtils::fromEnv()->getEntityManager();
    }

    public function setEntityManager(EntityManagerInterface $entityManager): void {
        WithUtilsCache::set('entityManager', $entityManager);
    }

    public function entityUtils(): EntityUtils {
        return $this->getOrCreate('entityUtils');
    }

    public function createEntityUtils(): EntityUtils {
        return EntityUtils::fromEnv();
    }

    public function setEntityUtils(EntityUtils $entityUtils): void {
        WithUtilsCache::set('entityUtils', $entityUtils);
    }

    public function envUtils(): EnvUtils {
        return $this->getOrCreate('envUtils');
    }

    public function createEnvUtils(): EnvUtils {
        return EnvUtils::fromEnv();
    }

    public function setEnvUtils(EnvUtils $envUtils): void {
        WithUtilsCache::set('envUtils', $envUtils);
    }

    public function fieldUtils(): FieldUtils {
        return $this->getOrCreate('fieldUtils');
    }

    public function createFieldUtils(): FieldUtils {
        return FieldUtils::create();
    }

    public function setFieldUtils(FieldUtils $fieldUtils): void {
        WithUtilsCache::set('fieldUtils', $fieldUtils);
    }

    public function fileUtils(): FileUtils {
        return $this->getOrCreate('fileUtils');
    }

    public function createFileUtils(): FileUtils {
        return FileUtils::fromEnv();
    }

    public function setFileUtils(FileUtils $fileUtils): void {
        WithUtilsCache::set('fileUtils', $fileUtils);
    }

    public function generalUtils(): GeneralUtils {
        return $this->getOrCreate('generalUtils');
    }

    public function createGeneralUtils(): GeneralUtils {
        return GeneralUtils::fromEnv();
    }

    public function setGeneralUtils(GeneralUtils $generalUtils): void {
        WithUtilsCache::set('generalUtils', $generalUtils);
    }

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

    public function htmlUtils(): HtmlUtils {
        return $this->getOrCreate('htmlUtils');
    }

    public function createHtmlUtils(): HtmlUtils {
        return HtmlUtils::fromEnv();
    }

    public function setHtmlUtils(HtmlUtils $htmlUtils): void {
        WithUtilsCache::set('htmlUtils', $htmlUtils);
    }

    public function httpUtils(): HttpUtils {
        return $this->getOrCreate('httpUtils');
    }

    public function createHttpUtils(): HttpUtils {
        return HttpUtils::fromEnv();
    }

    public function setHttpUtils(HttpUtils $httpUtils): void {
        WithUtilsCache::set('httpUtils', $httpUtils);
    }

    public function idUtils(): IdUtils {
        return $this->getOrCreate('idUtils');
    }

    public function createIdUtils(): IdUtils {
        return IdUtils::fromEnv();
    }

    public function setIdUtils(IdUtils $idUtils): void {
        WithUtilsCache::set('idUtils', $idUtils);
    }

    public function imageUtils(): ImageUtils {
        return $this->getOrCreate('imageUtils');
    }

    public function createImageUtils(): ImageUtils {
        return ImageUtils::fromEnv();
    }

    public function setImageUtils(ImageUtils $imageUtils): void {
        WithUtilsCache::set('imageUtils', $imageUtils);
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

    public function mapUtils(): MapUtils {
        return $this->getOrCreate('mapUtils');
    }

    public function createMapUtils(): MapUtils {
        return MapUtils::fromEnv();
    }

    public function setMapUtils(MapUtils $mapUtils): void {
        WithUtilsCache::set('mapUtils', $mapUtils);
    }

    public function recaptchaUtils(): RecaptchaUtils {
        return $this->getOrCreate('recaptchaUtils');
    }

    public function createRecaptchaUtils(): RecaptchaUtils {
        return RecaptchaUtils::fromEnv();
    }

    public function setRecaptchaUtils(RecaptchaUtils $recaptchaUtils): void {
        WithUtilsCache::set('recaptchaUtils', $recaptchaUtils);
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
        return $this->getOrCreate('solvFetcher');
    }

    public function createSolvFetcher(): SolvFetcher {
        return new SolvFetcher();
    }

    public function setSolvFetcher(SolvFetcher $solvFetcher): void {
        WithUtilsCache::set('solvFetcher', $solvFetcher);
    }

    public function stravaUtils(): StravaUtils {
        return $this->getOrCreate('stravaUtils');
    }

    public function createStravaUtils(): StravaUtils {
        return StravaUtils::fromEnv();
    }

    public function setStravaUtils(StravaUtils $stravaUtils): void {
        WithUtilsCache::set('stravaUtils', $stravaUtils);
    }

    public function symfonyUtils(): SymfonyUtils {
        return $this->getOrCreate('symfonyUtils');
    }

    public function createSymfonyUtils(): SymfonyUtils {
        return SymfonyUtils::fromEnv();
    }

    public function setSymfonyUtils(SymfonyUtils $symfonyUtils): void {
        WithUtilsCache::set('symfonyUtils', $symfonyUtils);
    }

    public function telegramUtils(): TelegramUtils {
        return $this->getOrCreate('telegramUtils');
    }

    public function createTelegramUtils(): TelegramUtils {
        return TelegramUtils::fromEnv();
    }

    public function setTelegramUtils(TelegramUtils $telegramUtils): void {
        WithUtilsCache::set('telegramUtils', $telegramUtils);
    }

    public function termineUtils(): TermineUtils {
        return $this->getOrCreate('termineUtils');
    }

    public function createTermineUtils(): TermineUtils {
        return TermineUtils::fromEnv();
    }

    public function setTermineUtils(TermineUtils $termineUtils): void {
        WithUtilsCache::set('termineUtils', $termineUtils);
    }

    public function uploadUtils(): UploadUtils {
        return $this->getOrCreate('uploadUtils');
    }

    public function createUploadUtils(): UploadUtils {
        return UploadUtils::fromEnv();
    }

    public function setUploadUtils(UploadUtils $uploadUtils): void {
        WithUtilsCache::set('uploadUtils', $uploadUtils);
    }

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
