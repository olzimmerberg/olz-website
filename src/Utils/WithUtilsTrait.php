<?php

namespace Olz\Utils;

use PhpTypeScriptApi\Fields\FieldUtils;

trait WithUtilsTrait {
    use \Psr\Log\LoggerAwareTrait;

    public static $ALL_UTILS = [
        'authUtils',
        'dateUtils',
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
        $this->utilsCache['authUtils'] = $authUtils;
    }

    public function dateUtils() {
        return $this->getOrCreate('dateUtils');
    }

    public function createDateUtils() {
        return AbstractDateUtils::fromEnv();
    }

    public function setDateUtils($dateUtils) {
        $this->utilsCache['dateUtils'] = $dateUtils;
    }

    public function emailUtils() {
        return $this->getOrCreate('emailUtils');
    }

    public function createEmailUtils() {
        return EmailUtils::fromEnv();
    }

    public function setEmailUtils($emailUtils) {
        $this->utilsCache['emailUtils'] = $emailUtils;
    }

    public function entityManager() {
        return $this->getOrCreate('entityManager');
    }

    public function createEntityManager() {
        global $entityManager;
        require_once __DIR__.'/../../_/config/doctrine_db.php';
        return $entityManager;
    }

    public function setEntityManager($entityManager) {
        $this->utilsCache['entityManager'] = $entityManager;
    }

    public function entityUtils() {
        return $this->getOrCreate('entityUtils');
    }

    public function createEntityUtils() {
        return EntityUtils::fromEnv();
    }

    public function setEntityUtils($entityUtils) {
        $this->utilsCache['entityUtils'] = $entityUtils;
    }

    public function envUtils() {
        return $this->getOrCreate('envUtils');
    }

    public function createEnvUtils() {
        return EnvUtils::fromEnv();
    }

    public function setEnvUtils($envUtils) {
        $this->utilsCache['envUtils'] = $envUtils;
    }

    public function fieldUtils() {
        return $this->getOrCreate('fieldUtils');
    }

    public function createFieldUtils() {
        return FieldUtils::create();
    }

    public function setFieldUtils($fieldUtils) {
        $this->utilsCache['fieldUtils'] = $fieldUtils;
    }

    public function generalUtils() {
        return $this->getOrCreate('generalUtils');
    }

    public function createGeneralUtils() {
        return GeneralUtils::fromEnv();
    }

    public function setGeneralUtils($generalUtils) {
        $this->utilsCache['generalUtils'] = $generalUtils;
    }

    public function getParams() {
        return $this->getOrCreate('getParams');
    }

    public function createGetParams() {
        global $_GET;
        return $_GET;
    }

    public function setGetParams($getParams) {
        $this->utilsCache['getParams'] = $getParams;
    }

    public function idUtils() {
        return $this->getOrCreate('idUtils');
    }

    public function createIdUtils() {
        return IdUtils::fromEnv();
    }

    public function setIdUtils($idUtils) {
        $this->utilsCache['idUtils'] = $idUtils;
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
        $this->utilsCache['log'] = $log;
    }

    public function server() {
        return $this->getOrCreate('server');
    }

    public function createServer() {
        global $_SERVER;
        return $_SERVER;
    }

    public function setServer($server) {
        $this->utilsCache['server'] = $server;
    }

    public function session() {
        return $this->getOrCreate('session');
    }

    public function createSession() {
        return new StandardSession();
    }

    public function setSession($session) {
        $this->utilsCache['session'] = $session;
    }

    public function stravaUtils() {
        return $this->getOrCreate('stravaUtils');
    }

    public function createStravaUtils() {
        return StravaUtils::fromEnv();
    }

    public function setStravaUtils($stravaUtils) {
        $this->utilsCache['stravaUtils'] = $stravaUtils;
    }

    public function telegramUtils() {
        return $this->getOrCreate('telegramUtils');
    }

    public function createTelegramUtils() {
        return TelegramUtils::fromEnv();
    }

    public function setTelegramUtils($telegram_utils) {
        $this->utilsCache['telegramUtils'] = $telegram_utils;
    }

    public function uploadUtils() {
        return $this->getOrCreate('uploadUtils');
    }

    public function createUploadUtils() {
        return UploadUtils::fromEnv();
    }

    public function setUploadUtils($uploadUtils) {
        $this->utilsCache['uploadUtils'] = $uploadUtils;
    }

    private $utilsCache = [];

    protected function getOrCreate($util_name) {
        $util = $this->utilsCache[$util_name] ?? null;
        if ($util) {
            return $util;
        }
        $cap_util_name = ucfirst($util_name);
        $creator_name = "create{$cap_util_name}";
        $util = $this->{$creator_name}();
        $this->utilsCache[$util_name] = $util;
        return $util;
    }

    public static function fromEnv() {
        return new self();
    }
}
