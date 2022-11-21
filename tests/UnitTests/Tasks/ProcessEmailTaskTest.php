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
    public function __construct(
        $id,
        $xOriginalTo = null,
        $to = [],
        $fromAddress = '',
        $fromName = '',
        $subject = '',
        $textHtml = '',
        $textPlain = '',
    ) {
        $this->id = $id;
        $this->xOriginalTo = $xOriginalTo;
        $this->to = $to;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
        $this->subject = $subject;
        $this->textHtml = $textHtml;
        $this->textPlain = $textPlain;
    }

    public function hasAttachments() {
        return false;
    }

    public function getAttachments() {
        return [];
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

        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'CRITICAL UnexpectedValueException in searchMailbox.',
            'ERROR Error running task ProcessEmail: Phew, that was unexpected.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
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

        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'CRITICAL Could not search IMAP mailbox.',
            'ERROR Error running task ProcessEmail: ["Host not found or something"].',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
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

        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'CRITICAL Exception in searchMailbox.',
            'ERROR Error running task ProcessEmail: Failed at something else.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskWithMailToWrongDomain(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            '12' => new FakeProcessEmailTaskMail(12, 'someone@other-domain.com'),
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

        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO E-Mail 12 to non-olzimmerberg.ch address: someone@other-domain.com',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskNoSuchUser(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            '12' => new FakeProcessEmailTaskMail(12, 'no-such-username@olzimmerberg.ch'),
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

        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO E-Mail 12 to inexistent user/role username: no-such-username',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskNoUserEmailPermission(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            '12' => new FakeProcessEmailTaskMail(12, 'no-permission@olzimmerberg.ch'),
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

        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO E-Mail 12 to user with no user_email permission: no-permission',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskToUser(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query['user_email'] = true;
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            '12' => new FakeProcessEmailTaskMail(12,
                'someone@olzimmerberg.ch',
                [],
                'from@from-domain.com',
                'From Name',
                'Test subject',
                'Test html',
                'Test text',
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

        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO Email forwarded from someone@olzimmerberg.ch to someone@gmail.com',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
        $user_repo = $entity_manager->repositories[User::class];
        $this->assertSame([
            [$user_repo->fakeProcessEmailTaskUser, 'Test subject', 'Test html', 'Test text'],
        ], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskToOldUser(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query['user_email'] = true;
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            '12' => new FakeProcessEmailTaskMail(12,
                'someone-old@olzimmerberg.ch',
                [],
                'from@from-domain.com',
                'From Name',
                'Test subject',
                'Test html',
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

        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO Email forwarded from someone-old@olzimmerberg.ch to someone-old@gmail.com',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
        $user_repo = $entity_manager->repositories[User::class];
        $this->assertSame([
            [$user_repo->fakeProcessEmailTaskUser, 'Test subject', 'Test html', 'Test text'],
        ], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskNoRoleEmailPermission(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            '12' => new FakeProcessEmailTaskMail(12, 'no-role-permission@olzimmerberg.ch'),
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

        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO E-Mail 12 to role with no role_email permission: no-role-permission',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskToRole(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_role_permission_by_query['role_email'] = true;
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            '12' => new FakeProcessEmailTaskMail(12,
                'somerole@olzimmerberg.ch',
                [],
                'from@from-domain.com',
                'From Name',
                'Test subject',
                'Test html',
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

        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO Email forwarded from somerole@olzimmerberg.ch to admin-user@test.olzimmerberg.ch',
            'INFO Email forwarded from somerole@olzimmerberg.ch to vorstand-user@test.olzimmerberg.ch',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
        $role_repo = $entity_manager->repositories[Role::class];
        $expected_emails = array_map(function ($user) {
            return [$user, 'Test subject', 'Test html', 'Test text'];
        }, $role_repo->fakeProcessEmailTaskRole->getUsers()->toArray());
        $this->assertSame($expected_emails, $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskToOldRole(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_role_permission_by_query['role_email'] = true;
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            '12' => new FakeProcessEmailTaskMail(12,
                'somerole-old@olzimmerberg.ch',
                [],
                'from@from-domain.com',
                'From Name',
                'Test subject',
                'Test html',
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

        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO Email forwarded from somerole-old@olzimmerberg.ch to admin-user@test.olzimmerberg.ch',
            'INFO Email forwarded from somerole-old@olzimmerberg.ch to vorstand-user@test.olzimmerberg.ch',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
        $role_repo = $entity_manager->repositories[Role::class];
        $expected_emails = array_map(function ($user) {
            return [$user, 'Test subject', 'Test html', 'Test text'];
        }, $role_repo->fakeProcessEmailTaskRole->getUsers()->toArray());
        $this->assertSame($expected_emails, $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskSendingError(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query['user_email'] = true;
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            '12' => new FakeProcessEmailTaskMail(12,
                'someone@olzimmerberg.ch',
                [],
                'from@from-domain.com',
                'From Name',
                'provoke_error',
                'Provoke error',
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

        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'CRITICAL Error forwarding email from someone@olzimmerberg.ch to someone@gmail.com: Provoked Mailer Error',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskToMultiple(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query['user_email'] = true;
        $auth_utils->has_role_permission_by_query['role_email'] = true;
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            '12' => new FakeProcessEmailTaskMail(12,
                null,
                ['someone@olzimmerberg.ch' => true, 'somerole@olzimmerberg.ch' => true],
                'from@from-domain.com',
                'From Name',
                'Test subject',
                'Test html',
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

        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO Email forwarded from someone@olzimmerberg.ch to someone@gmail.com',
            'INFO Email forwarded from somerole@olzimmerberg.ch to admin-user@test.olzimmerberg.ch',
            'INFO Email forwarded from somerole@olzimmerberg.ch to vorstand-user@test.olzimmerberg.ch',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
        $user_repo = $entity_manager->repositories[User::class];
        $role_repo = $entity_manager->repositories[Role::class];
        $expected_role_emails = array_map(function ($user) {
            return [$user, 'Test subject', 'Test html', 'Test text'];
        }, $role_repo->fakeProcessEmailTaskRole->getUsers()->toArray());
        $this->assertSame([
            [$user_repo->fakeProcessEmailTaskUser, 'Test subject', 'Test html', 'Test text'],
            ...$expected_role_emails,
        ], $email_utils->olzMailer->emails_sent);
    }
}
