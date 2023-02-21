<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Tasks;

use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\Tasks\ProcessEmailTask;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Webklex\PHPIMAP\Address;
use Webklex\PHPIMAP\Attribute;

require_once __DIR__.'/../../Fake/fake_notification_subscription.php';

class FakeProcessEmailAddress {
    public function __construct(
        public $mail,
        public $personal,
    ) {
    }
}

function getAddress(string $address, string $name = null): Address {
    return new Address(new FakeProcessEmailAddress($address, $name));
}

class FakeProcessEmailTaskMail {
    public $is_body_fetched = false;
    public $moved_to;

    public function __construct(
        public $uid,
        public $x_original_to = null,
        public $to = [],
        public $cc = [],
        public $bcc = [],
        public $from = '',
        public $subject = '',
        protected $textHtml = null,
        protected $textPlain = null,
        public $message_id = null,
    ) {
    }

    public function parseBody() {
        $this->is_body_fetched = true;
    }

    public function hasTextBody(): bool {
        return $this->textPlain !== null;
    }

    public function getTextBody(): string {
        return $this->textPlain;
    }

    public function hasHTMLBody(): bool {
        return $this->textHtml !== null;
    }

    public function getHTMLBody(): string {
        return $this->textHtml;
    }

    public function hasAttachments() {
        return false;
    }

    public function getAttachments() {
        return [];
    }

    public function move($folder) {
        $this->moved_to = $folder;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Tasks\ProcessEmailTask
 */
final class ProcessEmailTaskTest extends UnitTestCase {
    public function testProcessEmailTaskWithError(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_utils = new Fake\FakeAuthUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $email_utils = new Fake\FakeEmailUtils();
        $email_utils->client->exception = true;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = Fake\FakeLogger::create();

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
            'ERROR Error running task ProcessEmail: Failed at something.',
            'INFO Teardown task ProcessEmail...',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(false, $email_utils->client->is_connected);
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskWithMailToWrongDomain(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_utils = new Fake\FakeAuthUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $email_utils = new Fake\FakeEmailUtils();
        $mail = new FakeProcessEmailTaskMail(12, 'someone@other-domain.com');
        $email_utils->client->folders['INBOX'] = [$mail];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = Fake\FakeLogger::create();

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

        $this->assertSame(true, $email_utils->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(false, $mail->is_body_fetched);
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskNoSuchUser(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_utils = new Fake\FakeAuthUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $email_utils = new Fake\FakeEmailUtils();
        $mail = new FakeProcessEmailTaskMail(12, 'no-such-username@olzimmerberg.ch');
        $email_utils->client->folders['INBOX'] = [$mail];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = Fake\FakeLogger::create();

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
        $this->assertSame(true, $email_utils->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(false, $mail->is_body_fetched);
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskNoUserEmailPermission(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_utils = new Fake\FakeAuthUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $email_utils = new Fake\FakeEmailUtils();
        $mail = new FakeProcessEmailTaskMail(12, 'no-permission@olzimmerberg.ch');
        $email_utils->client->folders['INBOX'] = [$mail];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = Fake\FakeLogger::create();

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
        $this->assertSame(true, $email_utils->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(false, $mail->is_body_fetched);
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskToUser(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query['user_email'] = true;
        $env_utils = new Fake\FakeEnvUtils();
        $email_utils = new Fake\FakeEmailUtils();
        $mail = new FakeProcessEmailTaskMail(12,
            'someone@olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'Test subject'),
            'Test html',
            'Test text',
        );
        $email_utils->client->folders['INBOX'] = [$mail];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = Fake\FakeLogger::create();

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
        $this->assertSame(true, $email_utils->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $user_repo = $entity_manager->repositories[User::class];
        $this->assertSame([
            [
                'user' => $user_repo->fakeProcessEmailTaskUser,
                'from' => ['fake@olzimmerberg.ch', 'From Name (via OLZ) <from@from-domain.com>'],
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
            ],
        ], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskToOldUser(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query['user_email'] = true;
        $env_utils = new Fake\FakeEnvUtils();
        $email_utils = new Fake\FakeEmailUtils();
        $mail = new FakeProcessEmailTaskMail(12,
            'someone-old@olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'Test subject'),
            'Test html',
            'Test text'
        );
        $email_utils->client->folders['INBOX'] = [$mail];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = Fake\FakeLogger::create();

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
        $this->assertSame(true, $email_utils->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $user_repo = $entity_manager->repositories[User::class];
        $this->assertSame([
            [
                'user' => $user_repo->fakeProcessEmailTaskUser,
                'from' => ['fake@olzimmerberg.ch', 'From Name (via OLZ) <from@from-domain.com>'],
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
            ],
        ], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskNoRoleEmailPermission(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_utils = new Fake\FakeAuthUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $email_utils = new Fake\FakeEmailUtils();
        $mail = new FakeProcessEmailTaskMail(12, 'no-role-permission@olzimmerberg.ch');
        $email_utils->client->folders['INBOX'] = [$mail];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = Fake\FakeLogger::create();

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
        $this->assertSame(true, $email_utils->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(false, $mail->is_body_fetched);
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskToRole(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_role_permission_by_query['role_email'] = true;
        $env_utils = new Fake\FakeEnvUtils();
        $email_utils = new Fake\FakeEmailUtils();
        $mail = new FakeProcessEmailTaskMail(12,
            'somerole@olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'Test subject'),
            'Test html',
            'Test text'
        );
        $email_utils->client->folders['INBOX'] = [$mail];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = Fake\FakeLogger::create();

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
        $this->assertSame(true, $email_utils->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $role_repo = $entity_manager->repositories[Role::class];
        $expected_emails = array_map(function ($user) {
            return [
                'user' => $user,
                'from' => ['fake@olzimmerberg.ch', 'From Name (via OLZ) <from@from-domain.com>'],
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
            ];
        }, $role_repo->fakeProcessEmailTaskRole->getUsers()->toArray());
        $this->assertSame($expected_emails, $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskToOldRole(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_role_permission_by_query['role_email'] = true;
        $env_utils = new Fake\FakeEnvUtils();
        $email_utils = new Fake\FakeEmailUtils();
        $mail = new FakeProcessEmailTaskMail(12,
            'somerole-old@olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'Test subject'),
            'Test html',
            'Test text'
        );
        $email_utils->client->folders['INBOX'] = [$mail];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = Fake\FakeLogger::create();

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
        $this->assertSame(true, $email_utils->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $role_repo = $entity_manager->repositories[Role::class];
        $expected_emails = array_map(function ($user) {
            return [
                'user' => $user,
                'from' => ['fake@olzimmerberg.ch', 'From Name (via OLZ) <from@from-domain.com>'],
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
            ];
        }, $role_repo->fakeProcessEmailTaskRole->getUsers()->toArray());
        $this->assertSame($expected_emails, $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskSendingError(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query['user_email'] = true;
        $env_utils = new Fake\FakeEnvUtils();
        $email_utils = new Fake\FakeEmailUtils();
        $mail = new FakeProcessEmailTaskMail(12,
            'someone@olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'provoke_error'),
            'Provoke error',
            'Provoke error',
        );
        $email_utils->client->folders['INBOX'] = [$mail];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = Fake\FakeLogger::create();

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
        $this->assertSame(true, $email_utils->client->is_connected);
        $this->assertSame(null, $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
    }

    public function testProcessEmailTaskToMultiple(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query['user_email'] = true;
        $auth_utils->has_role_permission_by_query['role_email'] = true;
        $env_utils = new Fake\FakeEnvUtils();
        $email_utils = new Fake\FakeEmailUtils();
        $mail = new FakeProcessEmailTaskMail(12,
            null,
            new Attribute('to', [
                getAddress('someone@olzimmerberg.ch', ''),
                getAddress('somerole@olzimmerberg.ch', ''),
            ]),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'Test subject'),
            'Test html',
            'Test text'
        );
        $email_utils->client->folders['INBOX'] = [$mail];
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = Fake\FakeLogger::create();

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
        $this->assertSame(true, $email_utils->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $user_repo = $entity_manager->repositories[User::class];
        $role_repo = $entity_manager->repositories[Role::class];
        $expected_role_emails = array_map(function ($user) {
            return [
                'user' => $user,
                'from' => ['fake@olzimmerberg.ch', 'From Name (via OLZ) <from@from-domain.com>'],
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
            ];
        }, $role_repo->fakeProcessEmailTaskRole->getUsers()->toArray());
        $this->assertSame([
            [
                'user' => $user_repo->fakeProcessEmailTaskUser,
                'from' => ['fake@olzimmerberg.ch', 'From Name (via OLZ) <from@from-domain.com>'],
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
            ],
            ...$expected_role_emails,
        ], $email_utils->olzMailer->emails_sent);
    }

    // TODO: Multiple mails?
}
