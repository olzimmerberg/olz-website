<?php

use PhpTypeScriptApi\Fields\FieldUtils;

require_once __DIR__.'/../config/vendor/autoload.php';

trait WithUtilsTrait {
    use Psr\Log\LoggerAwareTrait;

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
        'logger',
        'server',
        'session',
        'stravaUtils',
        'telegramUtils',
        'uploadUtils',
    ];

    public function getAuthUtils() {
        require_once __DIR__.'/auth/AuthUtils.php';
        return AuthUtils::fromEnv();
    }

    public function setAuthUtils($authUtils) {
        $this->authUtils = $authUtils;
    }

    public function getDateUtils() {
        require_once __DIR__.'/date/AbstractDateUtils.php';
        return AbstractDateUtils::fromEnv();
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function getEmailUtils() {
        require_once __DIR__.'/notify/EmailUtils.php';
        return EmailUtils::fromEnv();
    }

    public function setEmailUtils($emailUtils) {
        $this->emailUtils = $emailUtils;
    }

    public function getEntityManager() {
        global $entityManager;
        require_once __DIR__.'/../config/doctrine_db.php';
        return $entityManager;
    }

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function getEntityUtils() {
        require_once __DIR__.'/EntityUtils.php';
        return EntityUtils::fromEnv();
    }

    public function setEntityUtils($entityUtils) {
        $this->entityUtils = $entityUtils;
    }

    public function getEnvUtils() {
        require_once __DIR__.'/env/EnvUtils.php';
        return EnvUtils::fromEnv();
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function getFieldUtils() {
        return FieldUtils::create();
    }

    public function setFieldUtils($fieldUtils) {
        $this->fieldUtils = $fieldUtils;
    }

    public function getGeneralUtils() {
        require_once __DIR__.'/GeneralUtils.php';
        return GeneralUtils::fromEnv();
    }

    public function setGeneralUtils($generalUtils) {
        $this->generalUtils = $generalUtils;
    }

    public function getGetParams() {
        global $_GET;
        return $_GET;
    }

    public function setGetParams($getParams) {
        $this->getParams = $getParams;
    }

    public function getIdUtils() {
        require_once __DIR__.'/IdUtils.php';
        return IdUtils::fromEnv();
    }

    public function setIdUtils($idUtils) {
        $this->idUtils = $idUtils;
    }

    public function getLogger() {
        $called_class = get_called_class();
        $env_utils = $this->getEnvUtils();
        return $env_utils->getLogsUtils()->getLogger(strval($called_class));
    }

    public function getServer() {
        global $_SERVER;
        return $_SERVER;
    }

    public function setServer($server) {
        $this->server = $server;
    }

    public function getSession() {
        require_once __DIR__.'/session/StandardSession.php';
        return new StandardSession();
    }

    public function setSession($session) {
        $this->session = $session;
    }

    public function getStravaUtils() {
        require_once __DIR__.'/auth/StravaUtils.php';
        return StravaUtils::fromEnv();
    }

    public function setStravaUtils($stravaUtils) {
        $this->stravaUtils = $stravaUtils;
    }

    public function getTelegramUtils() {
        require_once __DIR__.'/notify/TelegramUtils.php';
        return TelegramUtils::fromEnv();
    }

    public function setTelegramUtils($telegram_utils) {
        $this->telegramUtils = $telegram_utils;
    }

    public function getUploadUtils() {
        require_once __DIR__.'/UploadUtils.php';
        return UploadUtils::fromEnv();
    }

    public function setUploadUtils($uploadUtils) {
        $this->uploadUtils = $uploadUtils;
    }

    public function populateFromEnv($utils = null) {
        if ($utils === null) {
            $utils = self::$ALL_UTILS;
        }
        foreach ($utils as $util_name) {
            $cap_util_name = ucfirst($util_name);
            $getter_name = "get{$cap_util_name}";
            $setter_name = "set{$cap_util_name}";
            $value_from_env = $this->{$getter_name}();
            $this->{$setter_name}($value_from_env);
        }
    }

    public static function fromEnv() {
        $instance = new self();
        $instance->populateFromEnv(self::UTILS);
        return $instance;
    }
}
