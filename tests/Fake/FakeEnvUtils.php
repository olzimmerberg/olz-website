<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Monolog\Logger;
use Olz\Utils\EnvUtils;

class FakeEnvUtils extends EnvUtils {
    public string $app_env = 'test';
    public bool $has_unlimited_cron = false;
    public ?string $fake_data_path = null;

    public function hasUnlimitedCron(): bool {
        return $this->has_unlimited_cron;
    }

    public function getCronAuthenticityCode(): string {
        return 'some-token';
    }

    public function getDatabaseBackupKey(): string {
        return 'some-secret-key';
    }

    public function getRecaptchaSecretKey(): string {
        return 'some-secret-key';
    }

    public function getTelegramAuthenticityCode(): string {
        return 'some-token';
    }

    public function getBaseHref(): string {
        return 'http://fake-base-url';
    }

    public function getAppEnv(): string {
        return $this->app_env;
    }

    public function getCodeHref(): string {
        return '/_/';
    }

    public function getPrivatePath(): string {
        return __DIR__.'/../UnitTests/tmp/private/';
    }

    public function getDataPath(): string {
        if ($this->fake_data_path !== null) {
            return $this->fake_data_path;
        }
        return __DIR__.'/../UnitTests/tmp/';
    }

    public function getDataHref(): string {
        return '/data-href/';
    }

    public function getSyslogPath(): string {
        return __DIR__.'/../UnitTests/tmp/syslog/';
    }

    public function getImapHost(): string {
        return '127.0.0.1';
    }

    public function getImapPort(): string {
        return '143';
    }

    public function getImapFlags(): string {
        return '/notls';
    }

    public function getImapUsername(): string {
        return 'imap@staging.olzimmerberg.ch';
    }

    public function getImapPassword(): string {
        return '123456';
    }

    public function getSmtpHost(): string {
        return 'localhost';
    }

    public function getSmtpPort(): string {
        return '25';
    }

    public function getSmtpUsername(): string {
        return 'fake@staging.olzimmerberg.ch';
    }

    public function getSmtpPassword(): string {
        return '1234';
    }

    public function getSmtpSecure(): string {
        return 'tls';
    }

    public function getSmtpDebug(): int {
        return 3;
    }

    public function getSmtpFrom(): string {
        return 'fake@staging.olzimmerberg.ch';
    }

    public function getEmailForwardingHost(): string {
        return 'staging.olzimmerberg.ch';
    }

    public function getEmailReactionKey(): string {
        return 'aaaaaaaaaaaaaaaaaaaa';
    }

    public function getIdEncryptionKey(): string {
        return 'aaaaaaaaaaaaaaaaaaac';
    }

    public function getTelegramBotName(): string {
        return 'fake-bot-name';
    }

    public function getTelegramBotToken(): string {
        return 'fake-bot-token';
    }

    public function getAppSearchEnginesUsername(): string {
        return 'fake@gmail.com';
    }

    public function getAppSearchEnginesPassword(): string {
        return 'zxcv';
    }

    public function getAppMonitoringUsername(): string {
        return 'fake';
    }

    public function getAppMonitoringPassword(): string {
        return 'asdf';
    }

    public function getAppStatisticsUsername(): string {
        return 'fake';
    }

    public function getAppStatisticsPassword(): string {
        return 'qwer';
    }
}

class FakeLogsUtils {
    public function getLogger(string $ident): Logger {
        return new Logger('');
    }
}
