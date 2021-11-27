<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../src/config/vendor/autoload.php';

class FakeEnvUtils {
    public $has_unlimited_cron = false;
    public $fake_data_path;

    public function getLogsUtils() {
        return new FakeLogsUtils();
    }

    public function hasUnlimitedCron() {
        return $this->has_unlimited_cron;
    }

    public function getCronAuthenticityCode() {
        return 'some-token';
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
        return __DIR__.'/../unit_tests/tmp/';
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

    public function getSmtpFrom() {
        return 'fake@olzimmerberg.ch';
    }

    public function getEmailReactionKey() {
        return 'aaaaaaaaaaaaaaaaaaaa';
    }
}

class FakeLogsUtils {
    public function getLogger($ident) {
        return new Logger('');
    }
}
