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
use Webklex\PHPIMAP\Support\FlagCollection;

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
    public $attachments = [];

    public $is_body_fetched = false;
    public $flag_actions = [];
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

    public function getAttributes() {
        return [
            new Attribute('fake_attribute', 'fake_attribute value'),
        ];
    }

    public function getFlags() {
        return new FlagCollection();
    }

    public function setFlag($flag) {
        $this->flag_actions[] = "+{$flag}";
    }

    public function unsetFlag($flag) {
        $this->flag_actions[] = "-{$flag}";
    }

    public function hasAttachments() {
        return count($this->attachments) > 0;
    }

    public function getAttachments() {
        return $this->attachments;
    }

    public function move($folder) {
        $this->moved_to = $folder;
    }
}

class FakeProcessEmailCommandAttachment {
    public $saved = [];

    public function __construct(
        public $name,
        protected $should_fail = false,
    ) {
    }

    public function save($path, $filename) {
        $this->saved[] = [$path, $filename];
        return $this->should_fail ? false : true;
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
        $this->assertSame([], WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
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
        $this->assertSame([], $mail->flag_actions);
        $this->assertSame([], WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
    }

    public function testProcessEmailCommandNoSuchUser(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $mail = new FakeProcessEmailCommandMail(12,
            'no-such-username@staging.olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
        );
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
        $this->assertSame(true, $mail->is_body_fetched);
        $this->assertSame([], $mail->flag_actions);
        $this->assertSame([
            [
                'user' => null,
                'from' => ['fake@staging.olzimmerberg.ch', 'OLZ Bot'],
                'sender' => '',
                'replyTo' => null,
                'headers' => [
                    ['To', 'From Name <from@from-domain.com>'],
                ],
                'subject' => 'Undelivered Mail Returned to Sender',
                'body' => $job->getBounceMessage($mail, 'no-such-username@staging.olzimmerberg.ch'),
                'altBody' => null,
                'attachments' => [],
            ],
        ], WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
    }

    public function testProcessEmailCommandNoUserEmailPermission(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $mail = new FakeProcessEmailCommandMail(12,
            'no-permission@staging.olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
        );
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
        $this->assertSame(true, $mail->is_body_fetched);
        $this->assertSame([], $mail->flag_actions);
        $this->assertSame([
            [
                'user' => null,
                'from' => ['fake@staging.olzimmerberg.ch', 'OLZ Bot'],
                'sender' => '',
                'replyTo' => null,
                'headers' => [
                    ['To', 'From Name <from@from-domain.com>'],
                ],
                'subject' => 'Undelivered Mail Returned to Sender',
                'body' => $job->getBounceMessage($mail, 'no-permission@staging.olzimmerberg.ch'),
                'altBody' => null,
                'attachments' => [],
            ],
        ], WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
    }

    public function testProcessEmailCommandToUser(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(12,
            'someone@staging.olzimmerberg.ch',
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
            'INFO Email forwarded from someone@staging.olzimmerberg.ch to someone@gmail.com',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $this->assertSame(['+flagged', '-flagged'], $mail->flag_actions);
        $user_repo = $entity_manager->repositories[User::class];
        $this->assertSame([
            [
                'user' => $user_repo->fakeProcessEmailCommandUser,
                'from' => ['from@from-domain.com', 'From Name'],
                'sender' => 'fake@staging.olzimmerberg.ch',
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'headers' => [],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
                'attachments' => [],
            ],
        ], WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
    }

    public function testProcessEmailCommandToOldUser(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(12,
            'someone-old@staging.olzimmerberg.ch',
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
            'INFO Email forwarded from someone-old@staging.olzimmerberg.ch to someone-old@gmail.com',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $this->assertSame(['+flagged', '-flagged'], $mail->flag_actions);
        $user_repo = $entity_manager->repositories[User::class];
        $this->assertSame([
            [
                'user' => $user_repo->fakeProcessEmailCommandUser,
                'from' => ['from@from-domain.com', 'From Name'],
                'sender' => 'fake@staging.olzimmerberg.ch',
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'headers' => [],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
                'attachments' => [],
            ],
        ], WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
    }

    public function testProcessEmailCommandNoRoleEmailPermission(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $mail = new FakeProcessEmailCommandMail(12,
            'no-role-permission@staging.olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
        );
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
        $this->assertSame(true, $mail->is_body_fetched);
        $this->assertSame([], $mail->flag_actions);
        $this->assertSame([
            [
                'user' => null,
                'from' => ['fake@staging.olzimmerberg.ch', 'OLZ Bot'],
                'sender' => '',
                'replyTo' => null,
                'headers' => [
                    ['To', 'From Name <from@from-domain.com>'],
                ],
                'subject' => 'Undelivered Mail Returned to Sender',
                'body' => $job->getBounceMessage($mail, 'no-role-permission@staging.olzimmerberg.ch'),
                'altBody' => null,
                'attachments' => [],
            ],
        ], WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
    }

    public function testProcessEmailCommandToRole(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_role_permission_by_query['role_email'] = true;
        $mail = new FakeProcessEmailCommandMail(12,
            'somerole@staging.olzimmerberg.ch',
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
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to admin-user@staging.olzimmerberg.ch',
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to vorstand-user@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $this->assertSame(['+flagged', '-flagged', '+flagged', '-flagged'], $mail->flag_actions);
        $role_repo = $entity_manager->repositories[Role::class];
        $expected_emails = array_map(function ($user) {
            return [
                'user' => $user,
                'from' => ['from@from-domain.com', 'From Name'],
                'sender' => 'fake@staging.olzimmerberg.ch',
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'headers' => [],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
                'attachments' => [],
            ];
        }, $role_repo->fakeProcessEmailCommandRole->getUsers()->toArray());
        $this->assertSame($expected_emails, WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
    }

    public function testProcessEmailCommandToOldRole(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_role_permission_by_query['role_email'] = true;
        $mail = new FakeProcessEmailCommandMail(12,
            'somerole-old@staging.olzimmerberg.ch',
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
            'INFO Email forwarded from somerole-old@staging.olzimmerberg.ch to admin-user@staging.olzimmerberg.ch',
            'INFO Email forwarded from somerole-old@staging.olzimmerberg.ch to vorstand-user@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $this->assertSame(['+flagged', '-flagged', '+flagged', '-flagged'], $mail->flag_actions);
        $role_repo = $entity_manager->repositories[Role::class];
        $expected_emails = array_map(function ($user) {
            return [
                'user' => $user,
                'from' => ['from@from-domain.com', 'From Name'],
                'sender' => 'fake@staging.olzimmerberg.ch',
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'headers' => [],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
                'attachments' => [],
            ];
        }, $role_repo->fakeProcessEmailCommandRole->getUsers()->toArray());
        $this->assertSame($expected_emails, WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
    }

    public function testProcessEmailCommandSendingError(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(12,
            'someone@staging.olzimmerberg.ch',
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
            'CRITICAL Error forwarding email from someone@staging.olzimmerberg.ch to someone@gmail.com: Provoked Mailer Error',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame(null, $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $this->assertSame(['+flagged'], $mail->flag_actions);
        $this->assertSame([], WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
    }

    public function testProcessEmailCommandToMultiple(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        WithUtilsCache::get('authUtils')->has_role_permission_by_query['role_email'] = true;
        $mail = new FakeProcessEmailCommandMail(12,
            null,
            new Attribute('to', [
                getAddress('someone@staging.olzimmerberg.ch', ''),
                getAddress('somerole@staging.olzimmerberg.ch', ''),
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
            'INFO Email forwarded from someone@staging.olzimmerberg.ch to someone@gmail.com',
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to admin-user@staging.olzimmerberg.ch',
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to vorstand-user@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $this->assertSame(
            ['+flagged', '-flagged', '+flagged', '-flagged', '+flagged', '-flagged'],
            $mail->flag_actions,
        );
        $user_repo = $entity_manager->repositories[User::class];
        $role_repo = $entity_manager->repositories[Role::class];
        $expected_role_emails = array_map(function ($user) {
            return [
                'user' => $user,
                'from' => ['from@from-domain.com', 'From Name'],
                'sender' => 'fake@staging.olzimmerberg.ch',
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'headers' => [
                    ['To', 'someone@staging.olzimmerberg.ch, somerole@staging.olzimmerberg.ch'],
                ],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
                'attachments' => [],
            ];
        }, $role_repo->fakeProcessEmailCommandRole->getUsers()->toArray());
        $this->assertSame([
            [
                'user' => $user_repo->fakeProcessEmailCommandUser,
                'from' => ['from@from-domain.com', 'From Name'],
                'sender' => 'fake@staging.olzimmerberg.ch',
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'headers' => [
                    ['To', 'someone@staging.olzimmerberg.ch, somerole@staging.olzimmerberg.ch'],
                ],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
                'attachments' => [],
            ],
            ...$expected_role_emails,
        ], WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
    }

    public function testProcessEmailCommandToCcBcc(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        WithUtilsCache::get('authUtils')->has_role_permission_by_query['role_email'] = true;
        $mail = new FakeProcessEmailCommandMail(12,
            null,
            new Attribute('to', []),
            new Attribute('cc', [getAddress('someone@staging.olzimmerberg.ch', 'Some One')]),
            new Attribute('bcc', [getAddress('somerole@staging.olzimmerberg.ch', 'Some Role')]),
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
            'INFO Email forwarded from someone@staging.olzimmerberg.ch to someone@gmail.com',
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to admin-user@staging.olzimmerberg.ch',
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to vorstand-user@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $this->assertSame(
            ['+flagged', '-flagged', '+flagged', '-flagged', '+flagged', '-flagged'],
            $mail->flag_actions,
        );
        $user_repo = $entity_manager->repositories[User::class];
        $role_repo = $entity_manager->repositories[Role::class];
        $expected_role_emails = array_map(function ($user) {
            return [
                'user' => $user,
                'from' => ['from@from-domain.com', 'From Name'],
                'sender' => 'fake@staging.olzimmerberg.ch',
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'headers' => [
                    ['Cc', 'Some One <someone@staging.olzimmerberg.ch>'],
                    ['Bcc', 'Some Role <somerole@staging.olzimmerberg.ch>'],
                ],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
                'attachments' => [],
            ];
        }, $role_repo->fakeProcessEmailCommandRole->getUsers()->toArray());
        $this->assertSame([
            [
                'user' => $user_repo->fakeProcessEmailCommandUser,
                'from' => ['from@from-domain.com', 'From Name'],
                'sender' => 'fake@staging.olzimmerberg.ch',
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'headers' => [
                    ['Cc', 'Some One <someone@staging.olzimmerberg.ch>'],
                    ['Bcc', 'Some Role <somerole@staging.olzimmerberg.ch>'],
                ],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
                'attachments' => [],
            ],
            ...$expected_role_emails,
        ], WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
    }

    public function testProcessEmailCommandMultipleEmails(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        WithUtilsCache::get('authUtils')->has_role_permission_by_query['role_email'] = true;
        $mail1 = new FakeProcessEmailCommandMail(11,
            'someone@staging.olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'Test subject 1'),
            'Test html 1',
            'Test text 1'
        );
        $mail2 = new FakeProcessEmailCommandMail(12,
            'somerole@staging.olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'Test subject 2'),
            'Test html 2',
            'Test text 2'
        );
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail1, $mail2];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'INFO Email forwarded from someone@staging.olzimmerberg.ch to someone@gmail.com',
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to admin-user@staging.olzimmerberg.ch',
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to vorstand-user@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail1->moved_to);
        $this->assertSame('INBOX.Processed', $mail2->moved_to);
        $this->assertSame(true, $mail1->is_body_fetched);
        $this->assertSame(true, $mail2->is_body_fetched);
        $this->assertSame(['+flagged', '-flagged'], $mail1->flag_actions);
        $this->assertSame(['+flagged', '-flagged', '+flagged', '-flagged'], $mail2->flag_actions);
        $user_repo = $entity_manager->repositories[User::class];
        $role_repo = $entity_manager->repositories[Role::class];
        $expected_role_emails = array_map(function ($user) {
            return [
                'user' => $user,
                'from' => ['from@from-domain.com', 'From Name'],
                'sender' => 'fake@staging.olzimmerberg.ch',
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'headers' => [],
                'subject' => 'Test subject 2',
                'body' => 'Test html 2',
                'altBody' => 'Test text 2',
                'attachments' => [],
            ];
        }, $role_repo->fakeProcessEmailCommandRole->getUsers()->toArray());
        $this->assertSame([
            [
                'user' => $user_repo->fakeProcessEmailCommandUser,
                'from' => ['from@from-domain.com', 'From Name'],
                'sender' => 'fake@staging.olzimmerberg.ch',
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'headers' => [],
                'subject' => 'Test subject 1',
                'body' => 'Test html 1',
                'altBody' => 'Test text 1',
                'attachments' => [],
            ],
            ...$expected_role_emails,
        ], WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
    }

    public function testProcessEmailCommandWithAttachments(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(12,
            'someone@staging.olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'Test subject'),
            'Test html',
            'Test text',
        );
        $attachment1 = new FakeProcessEmailCommandAttachment('Attachment1.pdf');
        $attachment2 = new FakeProcessEmailCommandAttachment('Attachment2.docx');
        $mail->attachments = [
            'attachmentId1' => $attachment1,
            'attachmentId2' => $attachment2,
        ];
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'INFO Saving attachment Attachment1.pdf to AAAAAAAAAAAAAAAAAAAAAAAA.pdf...',
            'INFO Saving attachment Attachment2.docx to AAAAAAAAAAAAAAAAAAAAAAAA.docx...',
            'INFO Email forwarded from someone@staging.olzimmerberg.ch to someone@gmail.com',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $this->assertSame(['+flagged', '-flagged'], $mail->flag_actions);
        $user_repo = $entity_manager->repositories[User::class];
        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        $this->assertSame([
            [
                'user' => $user_repo->fakeProcessEmailCommandUser,
                'from' => ['from@from-domain.com', 'From Name'],
                'sender' => 'fake@staging.olzimmerberg.ch',
                'replyTo' => ['from@from-domain.com', 'From Name'],
                'headers' => [],
                'subject' => 'Test subject',
                'body' => 'Test html',
                'altBody' => 'Test text',
                'attachments' => [
                    ["{$data_path}temp/AAAAAAAAAAAAAAAAAAAAAAAA.pdf", 'Attachment1.pdf'],
                    ["{$data_path}temp/AAAAAAAAAAAAAAAAAAAAAAAA.docx", 'Attachment2.docx'],
                ],
            ],
        ], WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
    }

    public function testProcessEmailCommandWithFailingAttachment(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(12,
            'someone@staging.olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'Test subject'),
            'Test html',
            'Test text',
        );
        $attachment = new FakeProcessEmailCommandAttachment(
            'Attachment1.pdf', $should_fail = true);
        $mail->attachments = [
            'attachmentId' => $attachment,
        ];
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'INFO Saving attachment Attachment1.pdf to AAAAAAAAAAAAAAAAAAAAAAAA.pdf...',
            'CRITICAL Error forwarding email from someone@staging.olzimmerberg.ch to someone@gmail.com: Could not save attachment Attachment1.pdf to AAAAAAAAAAAAAAAAAAAAAAAA.pdf.',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame(null, $mail->moved_to);
        $this->assertSame(true, $mail->is_body_fetched);
        $this->assertSame(['+flagged'], $mail->flag_actions);
        $this->assertSame([], WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
    }

    public function testProcessEmailCommandEmailToSmtpFrom(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $mail = new FakeProcessEmailCommandMail(12,
            'fake@staging.olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
        );
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'INFO E-Mail 12 to inexistent user/role username: fake',
            'NOTICE sendBounceEmail: Avoiding email loop for fake@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertSame(true, WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertSame(false, $mail->is_body_fetched);
        $this->assertSame([], $mail->flag_actions);
        $this->assertSame([
            // no bounce email!
        ], WithUtilsCache::get('emailUtils')->testOnlyEmailsSent());
    }
}
