<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Monolog\Logger;

class FakeEnvUtils {
    public $has_unlimited_cron = false;
    public $fake_data_path;

    public function hasUnlimitedCron() {
        return $this->has_unlimited_cron;
    }

    public function getCronAuthenticityCode() {
        return 'some-token';
    }

    public function getDatabaseBackupKey() {
        return 'some-secret-key';
    }

    public function getRecaptchaSecretKey() {
        return 'some-secret-key';
    }

    public function getTelegramAuthenticityCode() {
        return 'some-token';
    }

    public function getBaseHref() {
        return 'http://fake-base-url';
    }

    public function getCodeHref() {
        return '/_/';
    }

    public function getDataPath() {
        if ($this->fake_data_path !== null) {
            return $this->fake_data_path;
        }
        return __DIR__.'/../UnitTests/tmp/';
    }

    public function getDataHref() {
        return '/data-href/';
    }

    public function getSyslogPath() {
        return __DIR__.'/../UnitTests/tmp/syslog/';
    }

    public function getImapHost() {
        return '127.0.0.1';
    }

    public function getImapPort() {
        return '143';
    }

    public function getImapFlags() {
        return '/notls';
    }

    public function getImapUsername() {
        return 'imap@olzimmerberg.ch';
    }

    public function getImapPassword() {
        return '123456';
    }

    public function getSmtpHost() {
        return 'localhost';
    }

    public function getSmtpPort() {
        return '25';
    }

    public function getSmtpUsername() {
        return 'fake@olzimmerberg.ch';
    }

    public function getSmtpPassword() {
        return '1234';
    }

    public function getSmtpSecure() {
        return 'tls';
    }

    public function getSmtpDebug() {
        return 3;
    }

    public function getSmtpFrom() {
        return 'fake@olzimmerberg.ch';
    }

    public function getEmailReactionKey() {
        return 'aaaaaaaaaaaaaaaaaaaa';
    }

    public function getIdEncryptionKey() {
        return 'aaaaaaaaaaaaaaaaaaac';
    }

    public function getTelegramBotName() {
        return 'fake-bot-name';
    }

    public function getTelegramBotToken() {
        return 'fake-bot-token';
    }

    public function getAppGoogleSearchUsername() {
        return 'fake@gmail.com';
    }

    public function getAppGoogleSearchPassword() {
        return 'zxcv';
    }

    public function getAppMonitoringUsername() {
        return 'fake';
    }

    public function getAppMonitoringPassword() {
        return 'asdf';
    }

    public function getAppStatisticsUsername() {
        return 'fake';
    }

    public function getAppStatisticsPassword() {
        return 'qwer';
    }
}

class FakeLogsUtils {
    public function getLogger($ident) {
        return new Logger('');
    }
}
