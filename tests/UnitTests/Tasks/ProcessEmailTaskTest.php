<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Tasks;

use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\Tasks\ProcessEmailTask;
use Olz\Tests\Fake\FakeAuthUtils;
use Olz\Tests\Fake\FakeEmailUtils;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeLogger;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;

require_once __DIR__.'/../../Fake/fake_notification_subscription.php';

class FakeProcessEmailTaskMail {
    public function __construct($to = [], $fromAddress = '', $fromName = '', $subject = '', $textPlain = '') {
        $this->to = $to;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
        $this->subject = $subject;
        $this->textPlain = $textPlain;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Tasks\ProcessEmailTask
 */
final class ProcessEmailTaskTest extends UnitTestCase {
    public function testProcessEmailTaskWithUnexpectedValueError(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->unexpected_value_exception = true;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();

        $job = new ProcessEmailTask();
        $job->setAuthUtils($auth_utils);
        $job->setDateUtils($date_utils);
        $job->setEmailUtils($email_utils);
        $job->setEntityManager($entity_manager);
        $job->setEnvUtils($env_utils);
        $job->setLog($logger);
        $job->run();

        $this->assertSame([], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'CRITICAL UnexpectedValueException in searchMailbox.',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
    }

    public function testProcessEmailTaskWithConnectionError(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->connection_exception = true;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();

        $job = new ProcessEmailTask();
        $job->setAuthUtils($auth_utils);
        $job->setDateUtils($date_utils);
        $job->setEmailUtils($email_utils);
        $job->setEntityManager($entity_manager);
        $job->setEnvUtils($env_utils);
        $job->setLog($logger);
        $job->run();

        $this->assertSame([], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'CRITICAL Could not search IMAP mailbox.',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
    }

    public function testProcessEmailTaskWithOtherError(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->exception = true;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();

        $job = new ProcessEmailTask();
        $job->setAuthUtils($auth_utils);
        $job->setDateUtils($date_utils);
        $job->setEmailUtils($email_utils);
        $job->setEntityManager($entity_manager);
        $job->setEnvUtils($env_utils);
        $job->setLog($logger);
        $job->run();

        $this->assertSame([], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'CRITICAL Exception in searchMailbox.',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
    }

    public function testProcessEmailTaskWithMailToWrongDomain(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            'fake-mail-id-1' => new FakeProcessEmailTaskMail(['someone@other-domain.com' => true]),
        ];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();

        $job = new ProcessEmailTask();
        $job->setAuthUtils($auth_utils);
        $job->setDateUtils($date_utils);
        $job->setEmailUtils($email_utils);
        $job->setEntityManager($entity_manager);
        $job->setEnvUtils($env_utils);
        $job->setLog($logger);
        $job->run();

        $this->assertSame([], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO E-Mail to non-olzimmerberg.ch address: someone@other-domain.com',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
    }

    public function testProcessEmailTaskNoSuchUser(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            'fake-mail-id-1' => new FakeProcessEmailTaskMail(['no-such-username@olzimmerberg.ch' => true]),
        ];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();

        $job = new ProcessEmailTask();
        $job->setAuthUtils($auth_utils);
        $job->setDateUtils($date_utils);
        $job->setEmailUtils($email_utils);
        $job->setEntityManager($entity_manager);
        $job->setEnvUtils($env_utils);
        $job->setLog($logger);
        $job->run();

        $this->assertSame([], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO E-Mail to inexistent user/role username: no-such-username',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
    }

    public function testProcessEmailTaskNoUserEmailPermission(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            'fake-mail-id-1' => new FakeProcessEmailTaskMail(['no-permission@olzimmerberg.ch' => true]),
        ];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();

        $job = new ProcessEmailTask();
        $job->setAuthUtils($auth_utils);
        $job->setDateUtils($date_utils);
        $job->setEmailUtils($email_utils);
        $job->setEntityManager($entity_manager);
        $job->setEnvUtils($env_utils);
        $job->setLog($logger);
        $job->run();

        $this->assertSame([], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO E-Mail to user with no user_email permission: no-permission',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
    }

    public function testProcessEmailTaskToUser(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query['user_email'] = true;
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            'fake-mail-id-1' => new FakeProcessEmailTaskMail(
                ['someone@olzimmerberg.ch' => true],
                'from@from-domain.com',
                'From Name',
                'Test subject',
                'Test text'
            ),
        ];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();

        $job = new ProcessEmailTask();
        $job->setAuthUtils($auth_utils);
        $job->setDateUtils($date_utils);
        $job->setEmailUtils($email_utils);
        $job->setEntityManager($entity_manager);
        $job->setEnvUtils($env_utils);
        $job->setLog($logger);
        $job->run();

        $user_repo = $entity_manager->repositories[User::class];
        $this->assertSame([
            [$user_repo->fakeProcessEmailTaskUser, 'Test subject', 'Test text'],
        ], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO Email forwarded from someone@olzimmerberg.ch to someone@gmail.com',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
    }

    public function testProcessEmailTaskNoRoleEmailPermission(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            'fake-mail-id-1' => new FakeProcessEmailTaskMail(['no-role-permission@olzimmerberg.ch' => true]),
        ];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();

        $job = new ProcessEmailTask();
        $job->setAuthUtils($auth_utils);
        $job->setDateUtils($date_utils);
        $job->setEmailUtils($email_utils);
        $job->setEntityManager($entity_manager);
        $job->setEnvUtils($env_utils);
        $job->setLog($logger);
        $job->run();

        $this->assertSame([], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO E-Mail to role with no role_email permission: no-role-permission',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
    }

    public function testProcessEmailTaskToRole(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_role_permission_by_query['role_email'] = true;
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            'fake-mail-id-1' => new FakeProcessEmailTaskMail(
                ['somerole@olzimmerberg.ch' => true],
                'from@from-domain.com',
                'From Name',
                'Test subject',
                'Test text'
            ),
        ];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();

        $job = new ProcessEmailTask();
        $job->setAuthUtils($auth_utils);
        $job->setDateUtils($date_utils);
        $job->setEmailUtils($email_utils);
        $job->setEntityManager($entity_manager);
        $job->setEnvUtils($env_utils);
        $job->setLog($logger);
        $job->run();

        $role_repo = $entity_manager->repositories[Role::class];
        $expected_emails = array_map(function ($user) {
            return [$user, 'Test subject', 'Test text'];
        }, $role_repo->fakeProcessEmailTaskRole->getUsers()->toArray());
        $this->assertSame($expected_emails, $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO Email forwarded from somerole@olzimmerberg.ch to admin-user@test.olzimmerberg.ch',
            'INFO Email forwarded from somerole@olzimmerberg.ch to vorstand-user@test.olzimmerberg.ch',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
    }

    public function testProcessEmailTaskSendingError(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query['user_email'] = true;
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            'fake-mail-id-1' => new FakeProcessEmailTaskMail(
                ['someone@olzimmerberg.ch' => true],
                'from@from-domain.com',
                'From Name',
                'provoke_error',
                'Provoke error',
            ),
        ];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();

        $job = new ProcessEmailTask();
        $job->setAuthUtils($auth_utils);
        $job->setDateUtils($date_utils);
        $job->setEmailUtils($email_utils);
        $job->setEntityManager($entity_manager);
        $job->setEnvUtils($env_utils);
        $job->setLog($logger);
        $job->run();

        $this->assertSame([], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'CRITICAL Error forwarding email from someone@olzimmerberg.ch to someone@gmail.com: Provoked Mailer Error',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
    }
}
