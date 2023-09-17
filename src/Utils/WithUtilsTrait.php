<?php

namespace Olz\Utils;

use Doctrine\ORM\EntityManager;
use PhpTypeScriptApi\Fields\FieldUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait WithUtilsTrait {
    // --- OLZ dependency injection ---

    use \Psr\Log\LoggerAwareTrait;

    // --- Symfony dependency injection ---

    protected MailerInterface $mailer;

    #[Required]
    public function setMailer(MailerInterface $mailer): void {
        $this->mailer = $mailer;
    }

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
        'generalUtils',
        'getParams',
        'htmlUtils',
        'httpUtils',
        'idUtils',
        'log',
        'server',
        'session',
        'stravaUtils',
        'symfonyUtils',
        'telegramUtils',
        'uploadUtils',
    ];

    public function authUtils(): AuthUtils {
        return $this->getOrCreate('authUtils');
    }

    public function createAuthUtils() {
        return AuthUtils::fromEnv();
    }

    public function setAuthUtils(AuthUtils $authUtils) {
        WithUtilsCache::set('authUtils', $authUtils);
    }

    public function dateUtils(): AbstractDateUtils {
        return $this->getOrCreate('dateUtils');
    }

    public function createDateUtils() {
        return AbstractDateUtils::fromEnv();
    }

    public function setDateUtils(AbstractDateUtils $dateUtils) {
        WithUtilsCache::set('dateUtils', $dateUtils);
    }

    public function dbUtils(): DbUtils {
        return $this->getOrCreate('dbUtils');
    }

    public function createDbUtils() {
        return DbUtils::fromEnv();
    }

    public function setDbUtils(DbUtils $dbUtils) {
        WithUtilsCache::set('dbUtils', $dbUtils);
    }

    public function devDataUtils(): DevDataUtils {
        return $this->getOrCreate('devDataUtils');
    }

    public function createDevDataUtils() {
        return DevDataUtils::fromEnv();
    }

    public function setDevDataUtils(DevDataUtils $devDataUtils) {
        WithUtilsCache::set('devDataUtils', $devDataUtils);
    }

    public function emailUtils(): EmailUtils {
        return $this->getOrCreate('emailUtils');
    }

    public function createEmailUtils() {
        $emailUtils = EmailUtils::fromEnv();
        $emailUtils->setMailer($this->mailer);
        return $emailUtils;
    }

    public function setEmailUtils(EmailUtils $emailUtils) {
        WithUtilsCache::set('emailUtils', $emailUtils);
    }

    public function entityManager(): EntityManager {
        return $this->getOrCreate('entityManager');
    }

    public function createEntityManager() {
        return DbUtils::fromEnv()->getEntityManager();
    }

    public function setEntityManager(EntityManager $entityManager) {
        WithUtilsCache::set('entityManager', $entityManager);
    }

    public function entityUtils(): EntityUtils {
        return $this->getOrCreate('entityUtils');
    }

    public function createEntityUtils() {
        return EntityUtils::fromEnv();
    }

    public function setEntityUtils(EntityUtils $entityUtils) {
        WithUtilsCache::set('entityUtils', $entityUtils);
    }

    public function envUtils(): EnvUtils {
        return $this->getOrCreate('envUtils');
    }

    public function createEnvUtils() {
        return EnvUtils::fromEnv();
    }

    public function setEnvUtils(EnvUtils $envUtils) {
        WithUtilsCache::set('envUtils', $envUtils);
    }

    public function fieldUtils(): FieldUtils {
        return $this->getOrCreate('fieldUtils');
    }

    public function createFieldUtils() {
        return FieldUtils::create();
    }

    public function setFieldUtils(FieldUtils $fieldUtils) {
        WithUtilsCache::set('fieldUtils', $fieldUtils);
    }

    public function generalUtils(): GeneralUtils {
        return $this->getOrCreate('generalUtils');
    }

    public function createGeneralUtils() {
        return GeneralUtils::fromEnv();
    }

    public function setGeneralUtils(GeneralUtils $generalUtils) {
        WithUtilsCache::set('generalUtils', $generalUtils);
    }

    public function getParams(): array {
        return $this->getOrCreate('getParams');
    }

    public function createGetParams() {
        global $_GET;
        return $_GET;
    }

    public function setGetParams(array $getParams) {
        WithUtilsCache::set('getParams', $getParams);
    }

    public function htmlUtils(): HtmlUtils {
        return $this->getOrCreate('htmlUtils');
    }

    public function createHtmlUtils() {
        return HtmlUtils::fromEnv();
    }

    public function setHtmlUtils(HtmlUtils $htmlUtils) {
        WithUtilsCache::set('htmlUtils', $htmlUtils);
    }

    public function httpUtils(): HttpUtils {
        return $this->getOrCreate('httpUtils');
    }

    public function createHttpUtils() {
        return HttpUtils::fromEnv();
    }

    public function setHttpUtils(HttpUtils $httpUtils) {
        WithUtilsCache::set('httpUtils', $httpUtils);
    }

    public function idUtils(): IdUtils {
        return $this->getOrCreate('idUtils');
    }

    public function createIdUtils() {
        return IdUtils::fromEnv();
    }

    public function setIdUtils(IdUtils $idUtils) {
        WithUtilsCache::set('idUtils', $idUtils);
    }

    public function log(): LoggerInterface {
        return $this->getOrCreate('log');
    }

    public function createLog() {
        $called_class = get_called_class();
        $logs_utils = LogsUtils::fromEnv();
        return $logs_utils->getLogger(strval($called_class));
    }

    public function setLog(LoggerInterface $log) {
        $this->setLogger($log);
        WithUtilsCache::set('log', $log);
    }

    public function recaptchaUtils(): RecaptchaUtils {
        return $this->getOrCreate('recaptchaUtils');
    }

    public function createRecaptchaUtils() {
        return RecaptchaUtils::fromEnv();
    }

    public function setRecaptchaUtils(RecaptchaUtils $recaptchaUtils) {
        WithUtilsCache::set('recaptchaUtils', $recaptchaUtils);
    }

    public function server(): array {
        return $this->getOrCreate('server');
    }

    public function createServer() {
        global $_SERVER;
        return $_SERVER;
    }

    public function setServer(array $server) {
        WithUtilsCache::set('server', $server);
    }

    public function session(): AbstractSession {
        return $this->getOrCreate('session');
    }

    public function createSession() {
        return new StandardSession();
    }

    public function setSession(AbstractSession $session) {
        WithUtilsCache::set('session', $session);
    }

    public function stravaUtils(): StravaUtils {
        return $this->getOrCreate('stravaUtils');
    }

    public function createStravaUtils() {
        return StravaUtils::fromEnv();
    }

    public function setStravaUtils(StravaUtils $stravaUtils) {
        WithUtilsCache::set('stravaUtils', $stravaUtils);
    }

    public function symfonyUtils(): SymfonyUtils {
        return $this->getOrCreate('symfonyUtils');
    }

    public function createSymfonyUtils() {
        return SymfonyUtils::fromEnv();
    }

    public function setSymfonyUtils(SymfonyUtils $symfonyUtils) {
        WithUtilsCache::set('symfonyUtils', $symfonyUtils);
    }

    public function telegramUtils(): TelegramUtils {
        return $this->getOrCreate('telegramUtils');
    }

    public function createTelegramUtils() {
        return TelegramUtils::fromEnv();
    }

    public function setTelegramUtils(TelegramUtils $telegram_utils) {
        WithUtilsCache::set('telegramUtils', $telegram_utils);
    }

    public function uploadUtils(): UploadUtils {
        return $this->getOrCreate('uploadUtils');
    }

    public function createUploadUtils() {
        return UploadUtils::fromEnv();
    }

    public function setUploadUtils(UploadUtils $uploadUtils) {
        WithUtilsCache::set('uploadUtils', $uploadUtils);
    }

    public function getAllUtils() {
        return WithUtilsCache::getAll();
    }

    public function setAllUtils($all_utils) {
        WithUtilsCache::setAll($all_utils);
    }

    protected function getOrCreate($util_name) {
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

    public static function fromEnv() {
        return new self();
    }
}
