<?php

declare(strict_types=1);

use Olz\Entity\User;
use Olz\Utils\FixedDateUtils;

require_once __DIR__.'/../../fake/fake_notification_subscription.php';
require_once __DIR__.'/../../fake/FakeUsers.php';
require_once __DIR__.'/../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../fake/FakeEmailUtils.php';
require_once __DIR__.'/../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../fake/FakeLogger.php';
require_once __DIR__.'/../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../_/tasks/ProcessEmailTask.php';
require_once __DIR__.'/../common/UnitTestCase.php';

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
 * @covers \ProcessEmailTask
 */
final class ProcessEmailTaskTest extends UnitTestCase {
    public function testProcessEmailTaskWithImapError(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->connection_exception = true;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();

        $job = new ProcessEmailTask($entity_manager, $auth_utils, $email_utils, $date_utils, $env_utils);
        $job->setLogger($logger);
        $job->run();

        global $fake_process_email_task_user, $user2;
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'CRITICAL Could not search IMAP mailbox.',
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

        $job = new ProcessEmailTask($entity_manager, $auth_utils, $email_utils, $date_utils, $env_utils);
        $job->setLogger($logger);
        $job->run();

        global $fake_process_email_task_user, $user2;
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
            'fake-mail-id-1' => new FakeProcessEmailTaskMail(['no-such-user@olzimmerberg.ch' => true]),
        ];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();

        $job = new ProcessEmailTask($entity_manager, $auth_utils, $email_utils, $date_utils, $env_utils);
        $job->setLogger($logger);
        $job->run();

        $this->assertSame([], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO E-Mail to inexistent username: no-such-user',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
    }

    public function testProcessEmailTaskNoEmailPermission(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $email_utils->mailbox->mail_dict = [
            'fake-mail-id-1' => new FakeProcessEmailTaskMail(['no-permission@olzimmerberg.ch' => true]),
        ];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();

        $job = new ProcessEmailTask($entity_manager, $auth_utils, $email_utils, $date_utils, $env_utils);
        $job->setLogger($logger);
        $job->run();

        $this->assertSame([], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO E-Mail to username with no email permission: no-permission',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
    }

    public function testProcessEmailTask(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query['email'] = true;
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

        $job = new ProcessEmailTask($entity_manager, $auth_utils, $email_utils, $date_utils, $env_utils);
        $job->setLogger($logger);
        $job->run();

        $user_repo = $entity_manager->repositories[User::class];
        $this->assertSame([
            [$user_repo->fake_process_email_task_user, 'Test subject', 'Test text'],
        ], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'INFO Email forwarded from someone@olzimmerberg.ch to someone@gmail.com',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
    }

    public function testProcessEmailTaskSendingError(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query['email'] = true;
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

        $job = new ProcessEmailTask($entity_manager, $auth_utils, $email_utils, $date_utils, $env_utils);
        $job->setLogger($logger);
        $job->run();

        global $fake_process_email_task_user;
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            'INFO Setup task ProcessEmail...',
            'INFO Running task ProcessEmail...',
            'CRITICAL Error forwarding email from someone@olzimmerberg.ch to someone@gmail.com: Provoked Error',
            'INFO Finished task ProcessEmail.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
    }
}
