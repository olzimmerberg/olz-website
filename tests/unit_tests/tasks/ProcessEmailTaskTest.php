<?php

declare(strict_types=1);

use PhpImap\Exceptions\ConnectionException;

require_once __DIR__.'/../../fake/fake_notification_subscription.php';
require_once __DIR__.'/../../fake/fake_user.php';
require_once __DIR__.'/../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../fake/FakeLogger.php';
require_once __DIR__.'/../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../fake/FakeUserRepository.php';
require_once __DIR__.'/../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../src/model/NotificationSubscription.php';
require_once __DIR__.'/../../../src/model/TelegramLink.php';
require_once __DIR__.'/../../../src/tasks/ProcessEmailTask.php';
require_once __DIR__.'/../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../common/UnitTestCase.php';

class FakeProcessEmailTaskEmailUtils {
    use Psr\Log\LoggerAwareTrait;

    public function __construct() {
        $this->mailbox = new FakeProcessEmailTaskMailbox();
        $this->olzMailer = new FakeProcessEmailTaskOlzMailer();
    }

    public function getImapMailbox() {
        return $this->mailbox;
    }

    public function createEmail() {
        return $this->olzMailer;
    }
}

class FakeProcessEmailTaskMailbox {
    public $connection_exception = false;
    public $mail_dict = [];
    public $deleted_mail_dict = [];
    public $expunged_mail_dict = [];

    public function setAttachmentsIgnore($should_ignore_attachments) {
    }

    public function searchMailbox($query) {
        if ($this->connection_exception) {
            throw new ConnectionException("Host not found or something.");
        }
        if ($query === 'ALL') {
            return array_keys($this->mail_dict);
        }
        throw new Exception("Expected 'ALL' query to searchMailbox");
    }

    public function getMail($mail_id, $should_mark_read) {
        return $this->mail_dict[$mail_id];
    }

    public function deleteMail($mail_id) {
        $this->deleted_mail_dict[$mail_id] = true;
    }

    public function expungeDeletedMails() {
        $this->expunged_mail_dict = $this->deleted_mail_dict;
    }
}

class FakeProcessEmailTaskMail {
    public function __construct($to = [], $fromAddress = '', $fromName = '', $subject = '', $textPlain = '') {
        $this->to = $to;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
        $this->subject = $subject;
        $this->textPlain = $textPlain;
    }
}

class FakeProcessEmailTaskOlzMailer {
    public $emails_sent = [];
    public $email_to_send;
    public $reply_to;

    public function configure($user, $title, $text) {
        $this->email_to_send = [$user, $title, $text];
    }

    public function addReplyTo($address, $name) {
        $this->reply_to = [$address, $name];
    }

    public function send() {
        if ($this->email_to_send[1] == 'provoke_error') {
            throw new Exception("Provoked Error");
        }
        $this->emails_sent[] = $this->email_to_send;
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
        $email_utils = new FakeProcessEmailTaskEmailUtils();
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
        $email_utils = new FakeProcessEmailTaskEmailUtils();
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
        $user_repo = new FakeUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeProcessEmailTaskEmailUtils();
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
        $user_repo = new FakeUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeProcessEmailTaskEmailUtils();
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
        $user_repo = new FakeUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query['email'] = true;
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeProcessEmailTaskEmailUtils();
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
        $user_repo = new FakeUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query['email'] = true;
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeProcessEmailTaskEmailUtils();
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
