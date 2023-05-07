<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\ProcessEmailCommand;
use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Webklex\PHPIMAP\Address;
use Webklex\PHPIMAP\Attribute;

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

class FakeProcessEmailCommandMail {
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
 * @covers \Olz\Command\ProcessEmailCommand
 */
final class ProcessEmailCommandTest extends UnitTestCase {
    public function testProcessEmailCommandWithError(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('emailUtils')->client->exception = true;
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'ERROR Error running command Olz\Command\ProcessEmailCommand: Failed at something.',
        ], $this->getLogs());
        $this->assertSame(false, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame([], WithUtilsCache::get('emailUtils')->olzMailer->emails_sent);
    }

    public function testProcessEmailCommandWithMailToWrongDomain(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $mail = new FakeProcessEmailCommandMail(12, 'someone@other-domain.com');
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'INFO E-Mail 12 to non-olzimmerberg.ch address: someone@other-domain.com',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());

        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(false, $mail->is_body_fetched);
        $this->assertSame([], WithUtilsCache::get('emailUtils')->olzMailer->emails_sent);
    }

    public function testProcessEmailCommandNoSuchUser(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $mail = new FakeProcessEmailCommandMail(12, 'no-such-username@olzimmerberg.ch');
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'INFO E-Mail 12 to inexistent user/role username: no-such-username',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(false, $mail->is_body_fetched);
        $this->assertSame([], WithUtilsCache::get('emailUtils')->olzMailer->emails_sent);
    }

    public function testProcessEmailCommandNoUserEmailPermission(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $mail = new FakeProcessEmailCommandMail(12, 'no-permission@olzimmerberg.ch');
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING E-Mail 12 to user with no user_email permission: no-permission',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(false, $mail->is_body_fetched);
        $this->assertSame([], WithUtilsCache::get('emailUtils')->olzMailer->emails_sent);
    }

    public function testProcessEmailCommandToUser(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(12,
            'someone@olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'Test subject'),
            'Test html',
            'Test text',
        );
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'INFO Email forwarded from someone@olzimmerberg.ch to someone@gmail.com',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $user_repo = $entity_manager->repositories[User::class];
        $this->assertSame([
            [
                'user' => $user_repo->fakeProcessEmailCommandUser,
                'from' => ['fake@olzimmerberg.ch', 'From Name <from@from-domain.com> (via OLZ)'],
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
            ],
        ], WithUtilsCache::get('emailUtils')->olzMailer->emails_sent);
    }

    public function testProcessEmailCommandToOldUser(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(12,
            'someone-old@olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'Test subject'),
            'Test html',
            'Test text'
        );
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'INFO Email forwarded from someone-old@olzimmerberg.ch to someone-old@gmail.com',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $user_repo = $entity_manager->repositories[User::class];
        $this->assertSame([
            [
                'user' => $user_repo->fakeProcessEmailCommandUser,
                'from' => ['fake@olzimmerberg.ch', 'From Name <from@from-domain.com> (via OLZ)'],
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
            ],
        ], WithUtilsCache::get('emailUtils')->olzMailer->emails_sent);
    }

    public function testProcessEmailCommandNoRoleEmailPermission(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $mail = new FakeProcessEmailCommandMail(12, 'no-role-permission@olzimmerberg.ch');
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING E-Mail 12 to role with no role_email permission: no-role-permission',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(false, $mail->is_body_fetched);
        $this->assertSame([], WithUtilsCache::get('emailUtils')->olzMailer->emails_sent);
    }

    public function testProcessEmailCommandToRole(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_role_permission_by_query['role_email'] = true;
        $mail = new FakeProcessEmailCommandMail(12,
            'somerole@olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'Test subject'),
            'Test html',
            'Test text'
        );
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'INFO Email forwarded from somerole@olzimmerberg.ch to admin-user@staging.olzimmerberg.ch',
            'INFO Email forwarded from somerole@olzimmerberg.ch to vorstand-user@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $role_repo = $entity_manager->repositories[Role::class];
        $expected_emails = array_map(function ($user) {
            return [
                'user' => $user,
                'from' => ['fake@olzimmerberg.ch', 'From Name <from@from-domain.com> (via OLZ)'],
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
            ];
        }, $role_repo->fakeProcessEmailCommandRole->getUsers()->toArray());
        $this->assertSame($expected_emails, WithUtilsCache::get('emailUtils')->olzMailer->emails_sent);
    }

    public function testProcessEmailCommandToOldRole(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_role_permission_by_query['role_email'] = true;
        $mail = new FakeProcessEmailCommandMail(12,
            'somerole-old@olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'Test subject'),
            'Test html',
            'Test text'
        );
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'INFO Email forwarded from somerole-old@olzimmerberg.ch to admin-user@staging.olzimmerberg.ch',
            'INFO Email forwarded from somerole-old@olzimmerberg.ch to vorstand-user@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $role_repo = $entity_manager->repositories[Role::class];
        $expected_emails = array_map(function ($user) {
            return [
                'user' => $user,
                'from' => ['fake@olzimmerberg.ch', 'From Name <from@from-domain.com> (via OLZ)'],
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
            ];
        }, $role_repo->fakeProcessEmailCommandRole->getUsers()->toArray());
        $this->assertSame($expected_emails, WithUtilsCache::get('emailUtils')->olzMailer->emails_sent);
    }

    public function testProcessEmailCommandSendingError(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(12,
            'someone@olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'provoke_error'),
            'Provoke error',
            'Provoke error',
        );
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'CRITICAL Error forwarding email from someone@olzimmerberg.ch to someone@gmail.com: Provoked Mailer Error',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame(null, $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $this->assertSame([], WithUtilsCache::get('emailUtils')->olzMailer->emails_sent);
    }

    public function testProcessEmailCommandToMultiple(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        WithUtilsCache::get('authUtils')->has_role_permission_by_query['role_email'] = true;
        $mail = new FakeProcessEmailCommandMail(12,
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
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'INFO Email forwarded from someone@olzimmerberg.ch to someone@gmail.com',
            'INFO Email forwarded from somerole@olzimmerberg.ch to admin-user@staging.olzimmerberg.ch',
            'INFO Email forwarded from somerole@olzimmerberg.ch to vorstand-user@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $user_repo = $entity_manager->repositories[User::class];
        $role_repo = $entity_manager->repositories[Role::class];
        $expected_role_emails = array_map(function ($user) {
            return [
                'user' => $user,
                'from' => ['fake@olzimmerberg.ch', 'From Name <from@from-domain.com> (via OLZ)'],
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
            ];
        }, $role_repo->fakeProcessEmailCommandRole->getUsers()->toArray());
        $this->assertSame([
            [
                'user' => $user_repo->fakeProcessEmailCommandUser,
                'from' => ['fake@olzimmerberg.ch', 'From Name <from@from-domain.com> (via OLZ)'],
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
            ],
            ...$expected_role_emails,
        ], WithUtilsCache::get('emailUtils')->olzMailer->emails_sent);
    }

    // TODO: Multiple mails?
}
