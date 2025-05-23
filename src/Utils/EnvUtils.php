<?php

namespace Olz\Utils;

class EnvUtils {
    private ?string $private_path = null;
    private ?string $data_path = null;
    private ?string $data_href = null;
    private ?string $code_path = null;
    private ?string $code_href = null;

    private ?string $syslog_path = null;
    private ?string $base_href = null;
    private ?string $app_env = null;

    private ?string $mysql_host = null;
    private ?string $mysql_port = null;
    private ?string $mysql_username = null;
    private ?string $mysql_password = null;
    private ?string $mysql_schema = null;

    private bool $has_unlimited_cron = false;

    private string $date_utils_class_name = 'LiveDateUtils';
    /** @var array<string, mixed> */
    private array $date_utils_class_args = [];

    private ?string $database_backup_key = null;
    private ?string $email_reaction_key = null;
    private ?string $id_encryption_key = null;

    private ?string $cron_authenticity_code = null;

    private ?string $strava_client_id = null;
    private ?string $strava_client_secret = null;

    private ?string $telegram_bot_name = null;
    private ?string $telegram_bot_token = null;
    private ?string $telegram_authenticity_code = null;

    private ?string $imap_host = null;
    private ?string $imap_port = null;
    private ?string $imap_flags = '';
    private ?string $imap_username = null;
    private ?string $imap_password = null;

    private ?string $smtp_host = null;
    private ?string $smtp_port = null;
    private ?string $smtp_username = null;
    private ?string $smtp_password = null;
    private ?string $smtp_secure = '';
    private int $smtp_debug = 0;
    private ?string $smtp_from = null;

    private ?string $email_forwarding_host = null;

    private ?string $app_search_engines_username = null;
    private ?string $app_search_engines_password = null;

    private ?string $app_monitoring_username = null;
    private ?string $app_monitoring_password = null;

    private ?string $app_statistics_username = null;
    private ?string $app_statistics_password = null;

    private bool $lazyInitCompleted = false;

    public function lazyInit(): void {
        if ($this->lazyInitCompleted) {
            return;
        }
        global $_SERVER;

        // TODO: Also use the configuration file?
        $private_path = self::computePrivatePath();
        $this->setPrivatePath($private_path);

        // TODO: Also use the configuration file?
        $data_path = self::computeDataPath();
        $this->setDataPath($data_path);
        $this->setDataHref('/');

        $code_href = '/';
        if (isset($_SERVER['OLZ_SYMFONY_HREF']) && preg_match('/^\/.+\/$/', $_SERVER['OLZ_SYMFONY_HREF'])) {
            $code_href = $_SERVER['OLZ_SYMFONY_HREF'];
        }
        $code_path = realpath(__DIR__.'/../../').'/';
        $this->setCodePath($code_path);
        $this->setCodeHref($code_href);

        $config_path = self::getConfigPath();
        if (!is_file($config_path)) {
            throw new \Exception("Konfigurationsdatei ({$config_path}) nicht gefunden!");
        }

        $configuration = require $config_path;
        $configure_env_utils_function = $configuration['configure_env_utils'];
        $configure_env_utils_function($this);

        $this->lazyInitCompleted = true;
    }

    public function setPrivatePath(?string $private_path): void {
        $this->private_path = $private_path;
    }

    public function setDataPath(string $data_path): void {
        $this->data_path = $data_path;
    }

    public function setDataHref(string $data_href): void {
        $this->data_href = $data_href;
    }

    public function setCodePath(string $code_path): void {
        $this->code_path = $code_path;
    }

    public function setCodeHref(string $code_href): void {
        $this->code_href = $code_href;
    }

    /** @param array<string, mixed> $config_dict */
    public function configure(array $config_dict): void {
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

        $this->email_forwarding_host = $config_dict['email_forwarding_host'] ?? $this->email_forwarding_host;

        $this->app_search_engines_username = $config_dict['app_search_engines_username'] ?? $this->app_search_engines_username;
        $this->app_search_engines_password = $config_dict['app_search_engines_password'] ?? $this->app_search_engines_password;

        $this->app_monitoring_username = $config_dict['app_monitoring_username'] ?? $this->app_monitoring_username;
        $this->app_monitoring_password = $config_dict['app_monitoring_password'] ?? $this->app_monitoring_password;

        $this->app_statistics_username = $config_dict['app_statistics_username'] ?? $this->app_statistics_username;
        $this->app_statistics_password = $config_dict['app_statistics_password'] ?? $this->app_statistics_password;
    }

    public function getPrivatePath(): ?string {
        $this->lazyInit();
        return $this->private_path;
    }

    public function getDataPath(): string {
        $this->lazyInit();
        $this->checkNotNull($this->data_path, "data_path not set");
        return $this->data_path;
    }

    public function getDataHref(): string {
        $this->lazyInit();
        $this->checkNotNull($this->data_href, "data_href not set");
        return $this->data_href;
    }

    public function getCodePath(): string {
        $this->lazyInit();
        $this->checkNotNull($this->code_path, "code_path not set");
        return $this->code_path;
    }

    public function getCodeHref(): string {
        $this->lazyInit();
        $this->checkNotNull($this->code_href, "code_href not set");
        return $this->code_href;
    }

    public function getSyslogPath(): string {
        $this->lazyInit();
        $this->checkNotNull($this->syslog_path, "syslog_path not set");
        return $this->syslog_path;
    }

    public function getBaseHref(): string {
        $this->lazyInit();
        $this->checkNotNull($this->base_href, "base_href not set");
        return $this->base_href;
    }

    public function getAppEnv(): string {
        $this->lazyInit();
        $this->checkNotNull($this->app_env, "app_env not set");
        return $this->app_env;
    }

    public function getMysqlHost(): string {
        $this->lazyInit();
        $this->checkNotNull($this->mysql_host, "mysql_host not set");
        return $this->mysql_host;
    }

    public function getMysqlPort(): string {
        $this->lazyInit();
        $this->checkNotNull($this->mysql_port, "mysql_port not set");
        return $this->mysql_port;
    }

    public function getMysqlServer(): string {
        $this->lazyInit();
        return "{$this->mysql_host}:{$this->mysql_port}";
    }

    public function getMysqlUsername(): string {
        $this->lazyInit();
        $this->checkNotNull($this->mysql_username, "mysql_username not set");
        return $this->mysql_username;
    }

    public function getMysqlPassword(): string {
        $this->lazyInit();
        $this->checkNotNull($this->mysql_password, "mysql_password not set");
        return $this->mysql_password;
    }

    public function getMysqlSchema(): string {
        $this->lazyInit();
        $this->checkNotNull($this->mysql_schema, "mysql_schema not set");
        return $this->mysql_schema;
    }

    public function hasUnlimitedCron(): bool {
        $this->lazyInit();
        return $this->has_unlimited_cron;
    }

    public function getDateUtilsClassName(): string {
        $this->lazyInit();
        return $this->date_utils_class_name;
    }

    /** @return array<mixed> */
    public function getDateUtilsClassArgs(): array {
        $this->lazyInit();
        return $this->date_utils_class_args;
    }

    public function getDatabaseBackupKey(): string {
        $this->lazyInit();
        $this->checkNotNull($this->database_backup_key, "database_backup_key not set");
        return $this->database_backup_key;
    }

    public function getEmailReactionKey(): string {
        $this->lazyInit();
        $this->checkNotNull($this->email_reaction_key, "email_reaction_key not set");
        return $this->email_reaction_key;
    }

    public function getIdEncryptionKey(): string {
        $this->lazyInit();
        $this->checkNotNull($this->id_encryption_key, "id_encryption_key not set");
        return $this->id_encryption_key;
    }

    public function getCronAuthenticityCode(): string {
        $this->lazyInit();
        $this->checkNotNull($this->cron_authenticity_code, "cron_authenticity_code not set");
        return $this->cron_authenticity_code;
    }

    public function getStravaClientId(): string {
        $this->lazyInit();
        $this->checkNotNull($this->strava_client_id, "strava_client_id not set");
        return $this->strava_client_id;
    }

    public function getStravaClientSecret(): string {
        $this->lazyInit();
        $this->checkNotNull($this->strava_client_secret, "strava_client_secret not set");
        return $this->strava_client_secret;
    }

    public function getTelegramBotName(): string {
        $this->lazyInit();
        $this->checkNotNull($this->telegram_bot_name, "telegram_bot_name not set");
        return $this->telegram_bot_name;
    }

    public function getTelegramBotToken(): string {
        $this->lazyInit();
        $this->checkNotNull($this->telegram_bot_token, "telegram_bot_token not set");
        return $this->telegram_bot_token;
    }

    public function getTelegramAuthenticityCode(): string {
        $this->lazyInit();
        $this->checkNotNull($this->telegram_authenticity_code, "telegram_authenticity_code not set");
        return $this->telegram_authenticity_code;
    }

    public function getImapHost(): string {
        $this->lazyInit();
        $this->checkNotNull($this->imap_host, "imap_host not set");
        return $this->imap_host;
    }

    public function getImapPort(): string {
        $this->lazyInit();
        $this->checkNotNull($this->imap_port, "imap_port not set");
        return $this->imap_port;
    }

    public function getImapFlags(): string {
        $this->lazyInit();
        $this->checkNotNull($this->imap_flags, "imap_flags not set");
        return $this->imap_flags;
    }

    public function getImapUsername(): string {
        $this->lazyInit();
        $this->checkNotNull($this->imap_username, "imap_username not set");
        return $this->imap_username;
    }

    public function getImapPassword(): string {
        $this->lazyInit();
        $this->checkNotNull($this->imap_password, "imap_password not set");
        return $this->imap_password;
    }

    public function getSmtpHost(): string {
        $this->lazyInit();
        $this->checkNotNull($this->smtp_host, "smtp_host not set");
        return $this->smtp_host;
    }

    public function getSmtpPort(): string {
        $this->lazyInit();
        $this->checkNotNull($this->smtp_port, "smtp_port not set");
        return $this->smtp_port;
    }

    public function getSmtpUsername(): string {
        $this->lazyInit();
        $this->checkNotNull($this->smtp_username, "smtp_username not set");
        return $this->smtp_username;
    }

    public function getSmtpPassword(): string {
        $this->lazyInit();
        $this->checkNotNull($this->smtp_password, "smtp_password not set");
        return $this->smtp_password;
    }

    public function getSmtpSecure(): string {
        $this->lazyInit();
        $this->checkNotNull($this->smtp_secure, "smtp_secure not set");
        return $this->smtp_secure;
    }

    public function getSmtpDebug(): int {
        $this->lazyInit();
        return $this->smtp_debug;
    }

    public function getSmtpFrom(): string {
        $this->lazyInit();
        $this->checkNotNull($this->smtp_from, "smtp_from not set");
        return $this->smtp_from;
    }

    public function getEmailForwardingHost(): string {
        $this->lazyInit();
        $this->checkNotNull($this->email_forwarding_host, "email_forwarding_host not set");
        return $this->email_forwarding_host;
    }

    public function getAppSearchEnginesUsername(): string {
        $this->lazyInit();
        $this->checkNotNull($this->app_search_engines_username, "app_search_engines_username not set");
        return $this->app_search_engines_username;
    }

    public function getAppSearchEnginesPassword(): string {
        $this->lazyInit();
        $this->checkNotNull($this->app_search_engines_password, "app_search_engines_password not set");
        return $this->app_search_engines_password;
    }

    public function getAppMonitoringUsername(): string {
        $this->lazyInit();
        $this->checkNotNull($this->app_monitoring_username, "app_monitoring_username not set");
        return $this->app_monitoring_username;
    }

    public function getAppMonitoringPassword(): string {
        $this->lazyInit();
        $this->checkNotNull($this->app_monitoring_password, "app_monitoring_password not set");
        return $this->app_monitoring_password;
    }

    public function getAppStatisticsUsername(): string {
        $this->lazyInit();
        $this->checkNotNull($this->app_statistics_username, "app_statistics_username not set");
        return $this->app_statistics_username;
    }

    public function getAppStatisticsPassword(): string {
        $this->lazyInit();
        $this->checkNotNull($this->app_statistics_password, "app_statistics_password not set");
        return $this->app_statistics_password;
    }

    /** @phpstan-assert !null $value */
    protected function checkNotNull(mixed $value, string $error_message): void {
        if ($value === null) {
            throw new \Exception($error_message);
        }
    }

    public static function computeDataPath(): string {
        $document_root = $_SERVER['DOCUMENT_ROOT'] ?? '';
        if ($document_root) {
            return "{$document_root}/";
        }
        $injected_path = __DIR__.'/data/DATA_PATH';
        if (is_file($injected_path)) {
            $injected_data_path = file_get_contents($injected_path);
            if ($injected_data_path && is_dir($injected_data_path)) {
                return "{$injected_data_path}/";
            }
        }
        $local_root = realpath(__DIR__.'/../../public');
        return "{$local_root}/";
    }

    public static function computePrivatePath(): ?string {
        global $_SERVER;

        $server_name = $_SERVER['SERVER_NAME'] ?? 'localhost';
        if ($server_name === '127.0.0.1' || $server_name === 'localhost') {
            $local_private = __DIR__.'/../../private';
            if (!is_dir($local_private)) {
                mkdir($local_private, 0o777, true);
            }
            return realpath($local_private).'/';
        }
        return realpath(__DIR__.'/../../../../').'/';
    }

    public static function getConfigPath(): string {
        global $_ENV;
        $env = $_ENV['APP_ENV'] ?? getenv('APP_ENV');
        if ($env) {
            $injected_env_path = dirname(__DIR__, 2)."/config/olz.{$env}.php";
            if (is_file($injected_env_path)) {
                return $injected_env_path;
            }
        }
        $injected_path = dirname(__DIR__, 2).'/config/olz.php';
        return $injected_path;
    }

    public static function fromEnv(): self {
        $instance = new self();
        $instance->lazyInit();
        return $instance;
    }
}
