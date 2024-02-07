<?php

namespace Olz\Utils;

require_once __DIR__.'/../../_/config/init.php';

class EnvUtils {
    public const UTILS = [];

    private $data_path;
    private $data_href;
    private $code_path;
    private $code_href;

    private $syslog_path;
    private $base_href;
    private $app_env;

    private $mysql_host;
    private $mysql_port;
    private $mysql_username;
    private $mysql_password;
    private $mysql_schema;

    private $has_unlimited_cron = false;

    private $date_utils_class_name = 'LiveDateUtils';
    private $date_utils_class_args = [];

    private $database_backup_key;
    private $email_reaction_key;
    private $id_encryption_key;

    private $cron_authenticity_code;

    private $recaptcha_secret_key;

    private $strava_client_id;
    private $strava_client_secret;

    private $telegram_bot_name;
    private $telegram_bot_token;
    private $telegram_authenticity_code;

    private $imap_host;
    private $imap_port;
    private $imap_flags = '';
    private $imap_username;
    private $imap_password;

    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $smtp_secure = '';
    private $smtp_debug = 0;
    private $smtp_from;

    private $app_search_engines_username;
    private $app_search_engines_password;

    private $app_monitoring_username;
    private $app_monitoring_password;

    private $app_statistics_username;
    private $app_statistics_password;

    public function setDataPath($data_path) {
        $this->data_path = $data_path;
    }

    public function setDataHref($data_href) {
        $this->data_href = $data_href;
    }

    public function setCodePath($code_path) {
        $this->code_path = $code_path;
    }

    public function setCodeHref($code_href) {
        $this->code_href = $code_href;
    }

    public function configure($config_dict) {
        $this->syslog_path = $config_dict['syslog_path'] ?? $this->syslog_path;
        $this->base_href = $config_dict['base_href'] ?? $this->base_href;
        $this->app_env = $config_dict['app_env'] ?? $this->app_env;

        $this->mysql_host = $config_dict['mysql_host'] ?? $this->mysql_host;
        $this->mysql_port = $config_dict['mysql_port'] ?? $this->mysql_port;
        $this->mysql_username = $config_dict['mysql_username'] ?? $this->mysql_username;
        $this->mysql_password = $config_dict['mysql_password'] ?? $this->mysql_password;
        $this->mysql_schema = $config_dict['mysql_schema'] ?? $this->mysql_schema;

        $this->has_unlimited_cron = $config_dict['has_unlimited_cron'] ?? $this->has_unlimited_cron;

        $this->date_utils_class_name = $config_dict['date_utils_class_name'] ?? $this->date_utils_class_name;
        $this->date_utils_class_args = $config_dict['date_utils_class_args'] ?? $this->date_utils_class_args;

        $this->database_backup_key = $config_dict['database_backup_key'] ?? $this->database_backup_key;
        $this->email_reaction_key = $config_dict['email_reaction_key'] ?? $this->email_reaction_key;
        $this->id_encryption_key = $config_dict['id_encryption_key'] ?? $this->id_encryption_key;

        $this->cron_authenticity_code = $config_dict['cron_authenticity_code'] ?? $this->cron_authenticity_code;

        $this->recaptcha_secret_key = $config_dict['recaptcha_secret_key'] ?? $this->recaptcha_secret_key;

        $this->strava_client_id = $config_dict['strava_client_id'] ?? $this->strava_client_id;
        $this->strava_client_secret = $config_dict['strava_client_secret'] ?? $this->strava_client_secret;

        $this->telegram_bot_name = $config_dict['telegram_bot_name'] ?? $this->telegram_bot_name;
        $this->telegram_bot_token = $config_dict['telegram_bot_token'] ?? $this->telegram_bot_token;
        $this->telegram_authenticity_code = $config_dict['telegram_authenticity_code'] ?? $this->telegram_authenticity_code;

        $this->imap_host = $config_dict['imap_host'] ?? $this->imap_host;
        $this->imap_port = $config_dict['imap_port'] ?? $this->imap_port;
        $this->imap_flags = $config_dict['imap_flags'] ?? $this->imap_flags;
        $this->imap_username = $config_dict['imap_username'] ?? $this->imap_username;
        $this->imap_password = $config_dict['imap_password'] ?? $this->imap_password;

        $this->smtp_host = $config_dict['smtp_host'] ?? $this->smtp_host;
        $this->smtp_port = $config_dict['smtp_port'] ?? $this->smtp_port;
        $this->smtp_username = $config_dict['smtp_username'] ?? $this->smtp_username;
        $this->smtp_password = $config_dict['smtp_password'] ?? $this->smtp_password;
        $this->smtp_secure = $config_dict['smtp_secure'] ?? $this->smtp_secure;
        $this->smtp_debug = $config_dict['smtp_debug'] ?? $this->smtp_debug;
        $this->smtp_from = $config_dict['smtp_from'] ?? $this->smtp_from;

        $this->app_search_engines_username = $config_dict['app_search_engines_username'] ?? $this->app_search_engines_username;
        $this->app_search_engines_password = $config_dict['app_search_engines_password'] ?? $this->app_search_engines_password;

        $this->app_monitoring_username = $config_dict['app_monitoring_username'] ?? $this->app_monitoring_username;
        $this->app_monitoring_password = $config_dict['app_monitoring_password'] ?? $this->app_monitoring_password;

        $this->app_statistics_username = $config_dict['app_statistics_username'] ?? $this->app_statistics_username;
        $this->app_statistics_password = $config_dict['app_statistics_password'] ?? $this->app_statistics_password;
    }

    public function getDataPath() {
        return $this->data_path;
    }

    public function getDataHref() {
        return $this->data_href;
    }

    public function getCodePath() {
        return $this->code_path;
    }

    public function getCodeHref() {
        return $this->code_href;
    }

    public function getSyslogPath() {
        return $this->syslog_path;
    }

    public function getBaseHref() {
        return $this->base_href;
    }

    public function getAppEnv() {
        return $this->app_env;
    }

    public function getMysqlHost() {
        return $this->mysql_host;
    }

    public function getMysqlPort() {
        return $this->mysql_port;
    }

    public function getMysqlServer() {
        return "{$this->mysql_host}:{$this->mysql_port}";
    }

    public function getMysqlUsername() {
        return $this->mysql_username;
    }

    public function getMysqlPassword() {
        return $this->mysql_password;
    }

    public function getMysqlSchema() {
        return $this->mysql_schema;
    }

    public function hasUnlimitedCron() {
        return $this->has_unlimited_cron;
    }

    public function getDateUtilsClassName() {
        return $this->date_utils_class_name;
    }

    public function getDateUtilsClassArgs() {
        return $this->date_utils_class_args;
    }

    public function getDatabaseBackupKey() {
        return $this->database_backup_key;
    }

    public function getEmailReactionKey() {
        return $this->email_reaction_key;
    }

    public function getIdEncryptionKey() {
        return $this->id_encryption_key;
    }

    public function getCronAuthenticityCode() {
        return $this->cron_authenticity_code;
    }

    public function getRecaptchaSecretKey() {
        return $this->recaptcha_secret_key;
    }

    public function getStravaClientId() {
        return $this->strava_client_id;
    }

    public function getStravaClientSecret() {
        return $this->strava_client_secret;
    }

    public function getTelegramBotName() {
        return $this->telegram_bot_name;
    }

    public function getTelegramBotToken() {
        return $this->telegram_bot_token;
    }

    public function getTelegramAuthenticityCode() {
        return $this->telegram_authenticity_code;
    }

    public function getImapHost() {
        return $this->imap_host;
    }

    public function getImapPort() {
        return $this->imap_port;
    }

    public function getImapFlags() {
        return $this->imap_flags;
    }

    public function getImapUsername() {
        return $this->imap_username;
    }

    public function getImapPassword() {
        return $this->imap_password;
    }

    public function getSmtpHost() {
        return $this->smtp_host;
    }

    public function getSmtpPort() {
        return $this->smtp_port;
    }

    public function getSmtpUsername() {
        return $this->smtp_username;
    }

    public function getSmtpPassword() {
        return $this->smtp_password;
    }

    public function getSmtpSecure() {
        return $this->smtp_secure;
    }

    public function getSmtpDebug() {
        return $this->smtp_debug;
    }

    public function getSmtpFrom() {
        return $this->smtp_from;
    }

    public function getAppSearchEnginesUsername() {
        return $this->app_search_engines_username;
    }

    public function getAppSearchEnginesPassword() {
        return $this->app_search_engines_password;
    }

    public function getAppMonitoringUsername() {
        return $this->app_monitoring_username;
    }

    public function getAppMonitoringPassword() {
        return $this->app_monitoring_password;
    }

    public function getAppStatisticsUsername() {
        return $this->app_statistics_username;
    }

    public function getAppStatisticsPassword() {
        return $this->app_statistics_password;
    }

    protected static $from_env_instance;

    public static function fromEnv() {
        if (self::$from_env_instance == null) {
            global $_SERVER;

            self::assertValidFromEnvContext();

            $env_utils = new self();

            $data_path = self::computeDataPath();

            // TODO: Also use the configuration file?
            $env_utils->setDataPath($data_path);
            $env_utils->setDataHref('/');

            $code_href = '/';
            if (isset($_SERVER['OLZ_SYMFONY_HREF']) && preg_match('/^\/.+\/$/', $_SERVER['OLZ_SYMFONY_HREF'])) {
                $code_href = $_SERVER['OLZ_SYMFONY_HREF'];
            }
            $code_path = realpath(__DIR__.'/../../').'/';
            $env_utils->setCodePath($code_path);
            $env_utils->setCodeHref($code_href);

            $config_path = self::getConfigPath();
            if (!$config_path || !is_file($config_path)) {
                throw new \Exception("Konfigurationsdatei nicht gefunden!");
            }

            $configuration = require $config_path;
            $configure_env_utils_function = $configuration['configure_env_utils'];
            $configure_env_utils_function($env_utils);

            self::$from_env_instance = $env_utils;
        }
        return self::$from_env_instance;
    }

    public static function computeDataPath() {
        $document_root = $_SERVER['DOCUMENT_ROOT'] ?? '';
        if ($document_root) {
            return "{$document_root}/";
        }
        $injected_path = __DIR__.'/data/DATA_PATH';
        if (is_file($injected_path)) {
            $injected_data_path = file_get_contents($injected_path);
            if (is_dir($injected_data_path)) {
                return "{$injected_data_path}/";
            }
        }
        $local_root = realpath(__DIR__.'/../../public');
        return "{$local_root}/";
    }

    public static function getConfigPath() {
        global $_SERVER;

        $env = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? null;
        if ($env) {
            $injected_env_path = __DIR__."/../../config/olz.{$env}.php";
            if (is_file($injected_env_path)) {
                return realpath($injected_env_path);
            }
        }
        $injected_path = __DIR__.'/../../config/olz.php';
        if (is_file($injected_path)) {
            return realpath($injected_path);
        }
        return null;
    }

    public static function assertValidFromEnvContext() {
        global $_SERVER;

        $argv = $_SERVER['argv'] ?? [];
        $first_arg = $argv[0] ?? '';
        $is_phpunit = preg_match('/phpunit$/', $first_arg);
        $last_arg = $argv[count($argv) - 1] ?? '';
        $executing_unit_tests = preg_match('/UnitTests$/', $last_arg);
        if ($is_phpunit && $executing_unit_tests) {
            $trace = debug_backtrace();
            $general_utils = GeneralUtils::fromEnv();
            $pretty_trace = $general_utils->getPrettyTrace($trace);

            throw new \Exception("Unit tests should never use EnvUtils::fromEnv!\n\n{$pretty_trace}");
        }
    }
}
