<?php

namespace Olz\Utils;

use PhpTypeScriptApi\Fields\FieldUtils;

trait WithUtilsTrait {
    use \Psr\Log\LoggerAwareTrait;

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
        'idUtils',
        'log',
        'server',
        'session',
        'stravaUtils',
        'symfonyUtils',
        'telegramUtils',
        'uploadUtils',
    ];

    public function authUtils() {
        return $this->getOrCreate('authUtils');
    }

    public function createAuthUtils() {
        return AuthUtils::fromEnv();
    }

    public function setAuthUtils($authUtils) {
        WithUtilsCache::set('authUtils', $authUtils);
    }

    public function dateUtils() {
        return $this->getOrCreate('dateUtils');
    }

    public function createDateUtils() {
        return AbstractDateUtils::fromEnv();
    }

    public function setDateUtils($dateUtils) {
        WithUtilsCache::set('dateUtils', $dateUtils);
    }

    public function dbUtils() {
        return $this->getOrCreate('dbUtils');
    }

    public function createDbUtils() {
        return DbUtils::fromEnv();
    }

    public function setDbUtils($dbUtils) {
        WithUtilsCache::set('dbUtils', $dbUtils);
    }

    public function devDataUtils() {
        return $this->getOrCreate('devDataUtils');
    }

    public function createDevDataUtils() {
        return DevDataUtils::fromEnv();
    }

    public function setDevDataUtils($devDataUtils) {
        WithUtilsCache::set('devDataUtils', $devDataUtils);
    }

    public function emailUtils() {
        return $this->getOrCreate('emailUtils');
    }

    public function createEmailUtils() {
        return EmailUtils::fromEnv();
    }

    public function setEmailUtils($emailUtils) {
        WithUtilsCache::set('emailUtils', $emailUtils);
    }

    public function entityManager() {
        return $this->getOrCreate('entityManager');
    }

    public function createEntityManager() {
        return DbUtils::fromEnv()->getEntityManager();
    }

    public function setEntityManager($entityManager) {
        WithUtilsCache::set('entityManager', $entityManager);
    }

    public function entityUtils() {
        return $this->getOrCreate('entityUtils');
    }

    public function createEntityUtils() {
        return EntityUtils::fromEnv();
    }

    public function setEntityUtils($entityUtils) {
        WithUtilsCache::set('entityUtils', $entityUtils);
    }

    public function envUtils() {
        return $this->getOrCreate('envUtils');
    }

    public function createEnvUtils() {
        return EnvUtils::fromEnv();
    }

    public function setEnvUtils($envUtils) {
        WithUtilsCache::set('envUtils', $envUtils);
    }

    public function fieldUtils() {
        return $this->getOrCreate('fieldUtils');
    }

    public function createFieldUtils() {
        return FieldUtils::create();
    }

    public function setFieldUtils($fieldUtils) {
        WithUtilsCache::set('fieldUtils', $fieldUtils);
    }

    public function generalUtils() {
        return $this->getOrCreate('generalUtils');
    }

    public function createGeneralUtils() {
        return GeneralUtils::fromEnv();
    }

    public function setGeneralUtils($generalUtils) {
        WithUtilsCache::set('generalUtils', $generalUtils);
    }

    public function getParams() {
        return $this->getOrCreate('getParams');
    }

    public function createGetParams() {
        global $_GET;
        return $_GET;
    }

    public function setGetParams($getParams) {
        WithUtilsCache::set('getParams', $getParams);
    }

    public function idUtils() {
        return $this->getOrCreate('idUtils');
    }

    public function createIdUtils() {
        return IdUtils::fromEnv();
    }

    public function setIdUtils($idUtils) {
        WithUtilsCache::set('idUtils', $idUtils);
    }

    public function log() {
        return $this->getOrCreate('log');
    }

    public function createLog() {
        $called_class = get_called_class();
        $logs_utils = LogsUtils::fromEnv();
        return $logs_utils->getLogger(strval($called_class));
    }

    public function setLog($log) {
        $this->setLogger($log);
        WithUtilsCache::set('log', $log);
    }

    public function recaptchaUtils() {
        return $this->getOrCreate('recaptchaUtils');
    }

    public function createRecaptchaUtils() {
        return RecaptchaUtils::fromEnv();
    }

    public function setRecaptchaUtils($recaptchaUtils) {
        WithUtilsCache::set('recaptchaUtils', $recaptchaUtils);
    }

    public function server() {
        return $this->getOrCreate('server');
    }

    public function createServer() {
        global $_SERVER;
        return $_SERVER;
    }

    public function setServer($server) {
        WithUtilsCache::set('server', $server);
    }

    public function session() {
        return $this->getOrCreate('session');
    }

    public function createSession() {
        return new StandardSession();
    }

    public function setSession($session) {
        WithUtilsCache::set('session', $session);
    }

    public function stravaUtils() {
        return $this->getOrCreate('stravaUtils');
    }

    public function createStravaUtils() {
        return StravaUtils::fromEnv();
    }

    public function setStravaUtils($stravaUtils) {
        WithUtilsCache::set('stravaUtils', $stravaUtils);
    }

    public function symfonyUtils() {
        return $this->getOrCreate('symfonyUtils');
    }

    public function createSymfonyUtils() {
        return SymfonyUtils::fromEnv();
    }

    public function setSymfonyUtils($symfonyUtils) {
        WithUtilsCache::set('symfonyUtils', $symfonyUtils);
    }

    public function telegramUtils() {
        return $this->getOrCreate('telegramUtils');
    }

    public function createTelegramUtils() {
        return TelegramUtils::fromEnv();
    }

    public function setTelegramUtils($telegram_utils) {
        WithUtilsCache::set('telegramUtils', $telegram_utils);
    }

    public function uploadUtils() {
        return $this->getOrCreate('uploadUtils');
    }

    public function createUploadUtils() {
        return UploadUtils::fromEnv();
    }

    public function setUploadUtils($uploadUtils) {
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
