<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/env/EnvUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \EnvUtils
 */
final class EnvUtilsTest extends UnitTestCase {
    public function testConfigure(): void {
        $env_utils = new EnvUtils();

        $env_utils->setDataPath('//');
        $env_utils->setDataHref('/');

        $env_utils->setCodePath('//_/');
        $env_utils->setCodeHref('/_/');

        $env_utils->setDeployPath('//deploy/');
        $env_utils->setDeployHref('/deploy/');

        $env_utils->setBaseHref("http://localhost/");

        $env_utils->configure([
            'mysql_host' => 'localhost',
            'mysql_port' => '3306',
            'mysql_username' => 'db-username',
            'mysql_password' => 'db-password',
            'mysql_schema' => 'db-schema',

            'has_unlimited_cron' => true,

            'date_utils_class_name' => 'FixedDateUtils',
            'date_utils_class_args' => ['2020-08-15 12:51:00'],

            'database_backup_key' => 'aaaaaaaaaaaaaaaaaaaa',
            'email_reaction_key' => 'aaaaaaaaaaaaaaaaaaab',
            'id_encryption_key' => 'aaaaaaaaaaaaaaaaaaac',

            'cron_authenticity_code' => 'this_is_just_local',

            'recaptcha_secret_key' => 'abcdefghijklmnopqrstuvw-abcdefghijklmnop',

            'strava_client_id' => '123456',
            'strava_client_secret' => '0123456789abcdef0123456789abcdef01234567',

            'google_client_id' => '12345678901-0123456789abcdefghijklmnopqrstuv.apps.googleusercontent.com',
            'google_client_secret' => '0123456789abcdef01234567',

            'facebook_app_id' => '1234567890123456',
            'facebook_app_secret' => '0123456789abcdef0123456789abcdef',

            'telegram_bot_name' => 'olz_bot',
            'telegram_bot_token' => '123456789:abcdefghijklmnopqrstuvwxyz123456789',
            'telegram_authenticity_code' => 'this_is_just_local',

            'imap_host' => 'localhost',
            'imap_port' => '143',
            'imap_flags' => '/notls',
            'imap_username' => 'fake-user@olzimmerberg.ch',
            'imap_password' => '1234',

            'smtp_host' => 'localhost',
            'smtp_port' => '465',
            'smtp_username' => 'fake-user@olzimmerberg.ch',
            'smtp_password' => '1234',
            'smtp_from' => 'fake-user@olzimmerberg.ch',
        ]);

        $this->assertSame('//', $env_utils->getDataPath());
        $this->assertSame('/', $env_utils->getDataHref());
        $this->assertSame('//_/', $env_utils->getCodePath());
        $this->assertSame('/_/', $env_utils->getCodeHref());
        $this->assertSame('//deploy/', $env_utils->getDeployPath());
        $this->assertSame('/deploy/', $env_utils->getDeployHref());
        $this->assertSame('http://localhost/', $env_utils->getBaseHref());
        $this->assertSame('localhost', $env_utils->getMysqlHost());
        $this->assertSame('3306', $env_utils->getMysqlPort());
        $this->assertSame('localhost:3306', $env_utils->getMysqlServer());
        $this->assertSame('db-username', $env_utils->getMysqlUsername());
        $this->assertSame('db-password', $env_utils->getMysqlPassword());
        $this->assertSame('db-schema', $env_utils->getMysqlSchema());
        $this->assertSame(true, $env_utils->hasUnlimitedCron());
        $this->assertSame('FixedDateUtils', $env_utils->getDateUtilsClassName());
        $this->assertSame(['2020-08-15 12:51:00'], $env_utils->getDateUtilsClassArgs());
        $this->assertSame('aaaaaaaaaaaaaaaaaaaa', $env_utils->getDatabaseBackupKey());
        $this->assertSame('aaaaaaaaaaaaaaaaaaab', $env_utils->getEmailReactionKey());
        $this->assertSame('aaaaaaaaaaaaaaaaaaac', $env_utils->getIdEncryptionKey());
        $this->assertSame('this_is_just_local', $env_utils->getCronAuthenticityCode());
        $this->assertSame('abcdefghijklmnopqrstuvw-abcdefghijklmnop', $env_utils->getRecaptchaSecretKey());
        $this->assertSame('123456', $env_utils->getStravaClientId());
        $this->assertSame('0123456789abcdef0123456789abcdef01234567', $env_utils->getStravaClientSecret());
        $this->assertSame('12345678901-0123456789abcdefghijklmnopqrstuv.apps.googleusercontent.com', $env_utils->getGoogleClientId());
        $this->assertSame('0123456789abcdef01234567', $env_utils->getGoogleClientSecret());
        $this->assertSame('1234567890123456', $env_utils->getFacebookAppId());
        $this->assertSame('0123456789abcdef0123456789abcdef', $env_utils->getFacebookAppSecret());
        $this->assertSame('olz_bot', $env_utils->getTelegramBotName());
        $this->assertSame('123456789:abcdefghijklmnopqrstuvwxyz123456789', $env_utils->getTelegramBotToken());
        $this->assertSame('this_is_just_local', $env_utils->getTelegramAuthenticityCode());
        $this->assertSame('localhost', $env_utils->getImapHost());
        $this->assertSame('143', $env_utils->getImapPort());
        $this->assertSame('/notls', $env_utils->getImapFlags());
        $this->assertSame('fake-user@olzimmerberg.ch', $env_utils->getImapUsername());
        $this->assertSame('1234', $env_utils->getImapPassword());
        $this->assertSame('localhost', $env_utils->getSmtpHost());
        $this->assertSame('465', $env_utils->getSmtpPort());
        $this->assertSame('fake-user@olzimmerberg.ch', $env_utils->getSmtpUsername());
        $this->assertSame('1234', $env_utils->getSmtpPassword());
        $this->assertSame('fake-user@olzimmerberg.ch', $env_utils->getSmtpFrom());
    }
}
