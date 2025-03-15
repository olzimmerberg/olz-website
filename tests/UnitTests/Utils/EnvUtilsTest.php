<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\EnvUtils;

class TestOnlyEnvUtils extends EnvUtils {
    public function lazyInit(): void {
        // Avoid the error for config file not found...
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\EnvUtils
 */
final class EnvUtilsTest extends UnitTestCase {
    public function testConfigure(): void {
        $env_utils = new TestOnlyEnvUtils();

        $env_utils->setPrivatePath('/private/');

        $env_utils->setDataPath('//');
        $env_utils->setDataHref('/');

        $env_utils->setCodePath('//_/');
        $env_utils->setCodeHref('/_/');

        $env_utils->configure([
            'syslog_path' => 'fake-syslog-path',
            'base_href' => 'http://localhost/',

            'mysql_host' => 'localhost',
            'mysql_port' => '3306',
            'mysql_username' => 'db-username',
            'mysql_password' => 'db-password',
            'mysql_schema' => 'db-schema',

            'has_unlimited_cron' => true,

            'date_utils_class_name' => 'DateUtils',
            'date_utils_class_args' => ['2020-08-15 12:34:56'],

            'database_backup_key' => 'aaaaaaaaaaaaaaaaaaaa',
            'email_reaction_key' => 'aaaaaaaaaaaaaaaaaaab',
            'id_encryption_key' => 'aaaaaaaaaaaaaaaaaaac',

            'cron_authenticity_code' => 'this_is_just_local',

            'recaptcha_secret_key' => 'abcdefghijklmnopqrstuvw-abcdefghijklmnop',

            'strava_client_id' => '123456',
            'strava_client_secret' => '0123456789abcdef0123456789abcdef01234567',

            'telegram_bot_name' => 'olz_bot',
            'telegram_bot_token' => '123456789:abcdefghijklmnopqrstuvwxyz123456789',
            'telegram_authenticity_code' => 'this_is_just_local',

            'imap_host' => 'localhost',
            'imap_port' => '143',
            'imap_flags' => '/notls',
            'imap_username' => 'fake-user@staging.olzimmerberg.ch',
            'imap_password' => '1234',

            'smtp_host' => 'localhost',
            'smtp_port' => '465',
            'smtp_username' => 'fake-user@staging.olzimmerberg.ch',
            'smtp_password' => '1234',
            'smtp_secure' => 'ssl',
            'smtp_debug' => 0,
            'smtp_from' => 'fake-user@staging.olzimmerberg.ch',

            'email_forwarding_host' => 'staging.olzimmerberg.ch',

            'app_search_engines_username' => 'fake-user@gmail.com',
            'app_search_engines_password' => 'zxcv',

            'app_monitoring_username' => 'fake-user',
            'app_monitoring_password' => 'asdf',

            'app_statistics_username' => 'fake-user',
            'app_statistics_password' => 'qwer',
        ]);

        $this->assertSame('/private/', $env_utils->getPrivatePath());
        $this->assertSame('//', $env_utils->getDataPath());
        $this->assertSame('/', $env_utils->getDataHref());
        $this->assertSame('//_/', $env_utils->getCodePath());
        $this->assertSame('/_/', $env_utils->getCodeHref());
        $this->assertSame('http://localhost/', $env_utils->getBaseHref());
        $this->assertSame('fake-syslog-path', $env_utils->getSyslogPath());
        $this->assertSame('localhost', $env_utils->getMysqlHost());
        $this->assertSame('3306', $env_utils->getMysqlPort());
        $this->assertSame('localhost:3306', $env_utils->getMysqlServer());
        $this->assertSame('db-username', $env_utils->getMysqlUsername());
        $this->assertSame('db-password', $env_utils->getMysqlPassword());
        $this->assertSame('db-schema', $env_utils->getMysqlSchema());
        $this->assertTrue($env_utils->hasUnlimitedCron());
        $this->assertSame('DateUtils', $env_utils->getDateUtilsClassName());
        $this->assertSame(['2020-08-15 12:34:56'], $env_utils->getDateUtilsClassArgs());
        $this->assertSame('aaaaaaaaaaaaaaaaaaaa', $env_utils->getDatabaseBackupKey());
        $this->assertSame('aaaaaaaaaaaaaaaaaaab', $env_utils->getEmailReactionKey());
        $this->assertSame('aaaaaaaaaaaaaaaaaaac', $env_utils->getIdEncryptionKey());
        $this->assertSame('this_is_just_local', $env_utils->getCronAuthenticityCode());
        $this->assertSame('abcdefghijklmnopqrstuvw-abcdefghijklmnop', $env_utils->getRecaptchaSecretKey());
        $this->assertSame('123456', $env_utils->getStravaClientId());
        $this->assertSame('0123456789abcdef0123456789abcdef01234567', $env_utils->getStravaClientSecret());
        $this->assertSame('olz_bot', $env_utils->getTelegramBotName());
        $this->assertSame('123456789:abcdefghijklmnopqrstuvwxyz123456789', $env_utils->getTelegramBotToken());
        $this->assertSame('this_is_just_local', $env_utils->getTelegramAuthenticityCode());
        $this->assertSame('localhost', $env_utils->getImapHost());
        $this->assertSame('143', $env_utils->getImapPort());
        $this->assertSame('/notls', $env_utils->getImapFlags());
        $this->assertSame('fake-user@staging.olzimmerberg.ch', $env_utils->getImapUsername());
        $this->assertSame('1234', $env_utils->getImapPassword());
        $this->assertSame('localhost', $env_utils->getSmtpHost());
        $this->assertSame('465', $env_utils->getSmtpPort());
        $this->assertSame('fake-user@staging.olzimmerberg.ch', $env_utils->getSmtpUsername());
        $this->assertSame('1234', $env_utils->getSmtpPassword());
        $this->assertSame('ssl', $env_utils->getSmtpSecure());
        $this->assertSame(0, $env_utils->getSmtpDebug());
        $this->assertSame('fake-user@staging.olzimmerberg.ch', $env_utils->getSmtpFrom());
        $this->assertSame('staging.olzimmerberg.ch', $env_utils->getEmailForwardingHost());
        $this->assertSame('fake-user@gmail.com', $env_utils->getAppSearchEnginesUsername());
        $this->assertSame('zxcv', $env_utils->getAppSearchEnginesPassword());
        $this->assertSame('fake-user', $env_utils->getAppMonitoringUsername());
        $this->assertSame('asdf', $env_utils->getAppMonitoringPassword());
        $this->assertSame('fake-user', $env_utils->getAppStatisticsUsername());
        $this->assertSame('qwer', $env_utils->getAppStatisticsPassword());
    }

    public function testComputePrivatePathDeployed(): void {
        global $_SERVER;
        $_SERVER['SERVER_NAME'] = 'fake.url';
        $this->assertSame(realpath(__DIR__.'/../../../../../').'/', EnvUtils::computePrivatePath());
    }

    public function testComputePrivatePathLocal(): void {
        global $_SERVER;
        $_SERVER['SERVER_NAME'] = '127.0.0.1';
        $this->assertSame(realpath(__DIR__.'/../../../private/').'/', EnvUtils::computePrivatePath());
    }

    public function testComputeDataPathFromDocumentRoot(): void {
        global $_SERVER;
        $_SERVER['DOCUMENT_ROOT'] = 'fake-document-root';
        $this->assertSame('fake-document-root/', EnvUtils::computeDataPath());
    }

    public function testComputeDataPathFromInjectedPath(): void {
        global $_SERVER;
        $_SERVER['DOCUMENT_ROOT'] = '';
        file_put_contents(__DIR__.'/../../../src/Utils/data/DATA_PATH', __DIR__);
        $this->assertSame(__DIR__.'/', EnvUtils::computeDataPath());
        unlink(__DIR__.'/../../../src/Utils/data/DATA_PATH');
    }

    public function testComputeDataPathFromLocalRoot(): void {
        global $_SERVER;
        $_SERVER['DOCUMENT_ROOT'] = '';
        $_SERVER['argv'] = 'isset';
        $this->assertSame(
            realpath(__DIR__.'/../../../public').'/',
            EnvUtils::computeDataPath(),
        );
    }

    public function testGetConfigPathFromInjectedEnvPath(): void {
        $config_dir = __DIR__.'/../../../config/';
        if (is_file("{$config_dir}olz.test.php")) {
            rename("{$config_dir}olz.test.php", "{$config_dir}_olz.test.php");
        }
        file_put_contents("{$config_dir}olz.test.php", '');
        $this->assertSame(
            realpath("{$config_dir}olz.test.php"),
            EnvUtils::getConfigPath(),
        );
        unlink("{$config_dir}olz.test.php");
        if (is_file("{$config_dir}_olz.test.php")) {
            rename("{$config_dir}_olz.test.php", "{$config_dir}olz.test.php");
        }
    }

    public function testGetConfigPathFromInjectedPath(): void {
        $config_dir = __DIR__.'/../../../config/';
        if (is_file("{$config_dir}olz.test.php")) {
            rename("{$config_dir}olz.test.php", "{$config_dir}_olz.test.php");
        }
        file_put_contents("{$config_dir}olz.php", '');
        $this->assertSame(
            realpath("{$config_dir}olz.php"),
            EnvUtils::getConfigPath(),
        );
        unlink("{$config_dir}olz.php");
        if (is_file("{$config_dir}_olz.test.php")) {
            rename("{$config_dir}_olz.test.php", "{$config_dir}olz.test.php");
        }
    }
}
