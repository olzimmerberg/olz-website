<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Carbon\Carbon;
use Olz\Command\ProcessEmailCommand;
use Olz\Entity\Throttling;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Webklex\PHPIMAP\Address as ImapAddress;
use Webklex\PHPIMAP\Attribute;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Support\AttachmentCollection;
use Webklex\PHPIMAP\Support\FlagCollection;

class FakeProcessEmailAddress {
    public function __construct(
        public string $mail,
        public ?string $personal,
    ) {
    }
}

function getAddress(string $address, ?string $name = null): ImapAddress {
    return new ImapAddress(new FakeProcessEmailAddress($address, $name));
}

class FakeProcessEmailCommandMail extends Message {
    public AttachmentCollection $attachments;

    public bool $is_body_fetched = false;
    /** @var array<string> */
    public array $flag_actions = [];
    public ?string $moved_to = null;
    /** @var ?array{0: bool, 1: ?string, 2: bool} */
    public ?array $deleted = null;

    public ?Attribute $date = null;

    public function __construct(
        public int|string $uid,
        public ?string $x_original_to = null,
        public ?Attribute $to = null,
        public ?Attribute $cc = null,
        public ?Attribute $bcc = null,
        public ?Attribute $from = null,
        public ?Attribute $subject = null,
        protected ?string $textHtml = null,
        protected ?string $textPlain = null,
        public int|string|null $message_id = null,
    ) {
        $this->attachments = new AttachmentCollection([]);
    }

    public function getUid(): int|string {
        return $this->uid;
    }

    public function getTo(): Attribute {
        return $this->to;
    }

    public function getCc(): Attribute {
        return $this->cc;
    }

    public function getBcc(): Attribute {
        return $this->bcc;
    }

    public function getFrom(): Attribute {
        return $this->from;
    }

    public function getSubject(): Attribute {
        return $this->subject;
    }

    // @phpstan-ignore-next-line
    public function get($key): mixed {
        return $this->{$key};
    }

    public function parseBody(): FakeProcessEmailCommandMail {
        $this->is_body_fetched = true;
        return $this;
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

    /** @return array<Attribute> */
    public function getAttributes(): array {
        return [
            new Attribute('fake_attribute', 'fake_attribute value'),
        ];
    }

    public function getFlags(): FlagCollection {
        $is_set_by_flag = [];
        foreach ($this->flag_actions as $action) {
            if ($action[0] === '+') {
                $is_set_by_flag[substr($action, 1)] = true;
            }
            if ($action[0] === '-') {
                unset($is_set_by_flag[substr($action, 1)]);
            }
        }
        return new FlagCollection($is_set_by_flag);
    }

    /** @param array<string>|string $flag */
    public function setFlag(array|string $flag): bool {
        $this->flag_actions[] = "+{$flag}";
        return true;
    }

    /** @param array<string>|string $flag */
    public function unsetFlag(array|string $flag): bool {
        $this->flag_actions[] = "-{$flag}";
        return true;
    }

    public function hasAttachments(): bool {
        return count($this->attachments) > 0;
    }

    public function getAttachments(): AttachmentCollection {
        return $this->attachments;
    }

    public function move(string $folder_path, bool $expunge = false): ?FakeProcessEmailCommandMail {
        $this->moved_to = $folder_path;
        return $this;
    }

    public function delete(bool $expunge = true, ?string $trash_path = null, bool $force_move = false): bool {
        $this->deleted = [$expunge, $trash_path, $force_move];
        return true;
    }
}

class FakeProcessEmailCommandAttachment {
    /** @var array<array{0: string, 1: string}> */
    public array $saved = [];

    public function __construct(
        public string $name,
        protected bool $should_fail = false,
    ) {
    }

    public function save(string $path, string $filename): bool {
        $this->saved[] = [$path, $filename];
        return $this->should_fail ? false : true;
    }

    public function getContent(): string {
        // Used for spam notification emails
        return "Subject: Fake subject\r\n Some spam email content";
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\ProcessEmailCommand
 */
final class ProcessEmailCommandTest extends UnitTestCase {
    public function testProcessEmailCommandWithError(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->client->exception = true;
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $mailer->expects($this->exactly(0))->method('send');

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'ERROR Error running command Olz\Command\ProcessEmailCommand: Failed at something.',
        ], $this->getLogs());
        $this->assertFalse(WithUtilsCache::get('emailUtils')->client->is_connected);
    }

    public function testProcessEmailCommandWithMailToWrongDomain(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        $mail = new FakeProcessEmailCommandMail(12, 'someone@other-domain.com');
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $mailer->expects($this->exactly(0))->method('send');

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO E-Mail 12 to non-olzimmerberg.ch address: someone@other-domain.com',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());

        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertFalse($mail->is_body_fetched);
        $this->assertSame([], $mail->flag_actions);
    }

    public function testProcessEmailCommandNoSuchUser(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        $mail = new FakeProcessEmailCommandMail(
            12,
            'no-such-username@staging.olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
        );
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $artifacts = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            null,
        );

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO E-Mail 12 to inexistent user/role username: no-such-username',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame([], $mail->flag_actions);
        $this->assertSame([
            <<<ZZZZZZZZZZ
                From: "OLZ Bot" <fake@staging.olzimmerberg.ch>
                Reply-To: 
                To: "From Name" <from@from-domain.com>
                Cc: 
                Bcc: 
                Subject: Undelivered Mail Returned to Sender

                {$job->getReportMessage(550, $mail, 'no-such-username@staging.olzimmerberg.ch')}

                (no html body)

                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
    }

    public function testProcessEmailCommandNoUserEmailPermission(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        $mail = new FakeProcessEmailCommandMail(
            12,
            'no-permission@staging.olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
        );
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $artifacts = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            null,
        );

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'NOTICE E-Mail 12 to user with no user_email permission: no-permission',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame([], $mail->flag_actions);
        $this->assertSame([
            <<<ZZZZZZZZZZ
                From: "OLZ Bot" <fake@staging.olzimmerberg.ch>
                Reply-To: 
                To: "From Name" <from@from-domain.com>
                Cc: 
                Bcc: 
                Subject: Undelivered Mail Returned to Sender

                {$job->getReportMessage(550, $mail, 'no-permission@staging.olzimmerberg.ch')}

                (no html body)

                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
    }

    public function testProcessEmailCommandEmptyToException(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(
            12,
            'someone@staging.olzimmerberg.ch',
            new Attribute('to', [
                getAddress('', ''), // empty
            ]),
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
        $artifacts = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            $this->callback(function (Envelope $envelope) use (&$artifacts) {
                $artifacts['envelope'] = [...($artifacts['envelope'] ?? []), $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO Email forwarded from someone@staging.olzimmerberg.ch to someone@gmail.com',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame(['+flagged', '-flagged'], $mail->flag_actions);
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: 
                Cc: 
                Bcc: 
                Subject: Test subject

                Test text

                Test html

                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: someone@gmail.com
                ZZZZZZZZZZ,
        ], array_map(function ($envelope) {
            return $this->emailUtils()->getComparableEnvelope($envelope);
        }, $artifacts['envelope']));
    }

    public function testProcessEmailCommandRfcComplianceException(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(
            12,
            'someone@staging.olzimmerberg.ch',
            new Attribute('to', [
                getAddress('non-rfc-compliant-email', ''),
            ]),
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
        $mailer->expects($this->exactly(0))->method('send');

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'NOTICE Email from someone@staging.olzimmerberg.ch to someone@gmail.com is not RFC-compliant: Email "non-rfc-compliant-email" does not comply with addr-spec of RFC 2822.',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertFalse($mail->is_body_fetched);
        $this->assertSame(['+flagged'], $mail->flag_actions);
    }

    public function testProcessEmailCommandToUser(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(
            12,
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
        $artifacts = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            $this->callback(function (Envelope $envelope) use (&$artifacts) {
                $artifacts['envelope'] = [...($artifacts['envelope'] ?? []), $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO Email forwarded from someone@staging.olzimmerberg.ch to someone@gmail.com',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame(['+flagged', '-flagged'], $mail->flag_actions);
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: 
                Cc: 
                Bcc: 
                Subject: Test subject

                Test text

                Test html

                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: someone@gmail.com
                ZZZZZZZZZZ,
        ], array_map(function ($envelope) {
            return $this->emailUtils()->getComparableEnvelope($envelope);
        }, $artifacts['envelope']));
    }

    public function testProcessEmailCommandToUserEmptyEmail(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(
            12,
            'empty-email@staging.olzimmerberg.ch',
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
        $mailer->expects($this->exactly(0))->method('send');

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'CRITICAL Error forwarding email from empty-email@staging.olzimmerberg.ch to : getUserAddress: empty-email (User ID: 1) has no email.',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertNull($mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame(['+flagged'], $mail->flag_actions);
    }

    public function testProcessEmailCommandToOldUser(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(
            12,
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
        $artifacts = [];
        $mailer->expects($this->exactly(2))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            $this->callback(function (?Envelope $envelope) use (&$artifacts) {
                $artifacts['envelope'] = [...($artifacts['envelope'] ?? []), $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO Email forwarded from someone-old@staging.olzimmerberg.ch to someone-old@gmail.com',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame(['+flagged', '-flagged'], $mail->flag_actions);
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: "OLZ Bot" <fake@staging.olzimmerberg.ch>
                Reply-To: 
                To: "From Name" <from@from-domain.com>
                Cc: 
                Bcc: 
                Subject: Empfänger hat eine neue E-Mail-Adresse
                
                Hallo From Name (from@from-domain.com),
                
                Dies ist eine Mitteilung der E-Mail-Weiterleitung:
                Die E-Mail-Adresse "someone-old@staging.olzimmerberg.ch" ist neu unter "someone@olzimmerberg.ch" erreichbar.
                
                Dies nur zur Information. Ihre E-Mail wurde automatisch weitergeleitet!
                
                (no html body)

                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: 
                Cc: 
                Bcc: 
                Subject: Test subject

                Test text

                Test html

                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
        $this->assertSame([
            null,
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: someone-old@gmail.com
                ZZZZZZZZZZ,
        ], array_map(function ($envelope) {
            return $this->emailUtils()->getComparableEnvelope($envelope);
        }, $artifacts['envelope']));
    }

    public function testProcessEmailCommandNoRoleEmailPermission(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        $mail = new FakeProcessEmailCommandMail(
            12,
            'no-role-permission@staging.olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
        );
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $artifacts = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            null,
        );

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'WARNING E-Mail 12 to role with no role_email permission: no-role-permission',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame([], $mail->flag_actions);
        $this->assertSame([
            <<<ZZZZZZZZZZ
                From: "OLZ Bot" <fake@staging.olzimmerberg.ch>
                Reply-To: 
                To: "From Name" <from@from-domain.com>
                Cc: 
                Bcc: 
                Subject: Undelivered Mail Returned to Sender

                {$job->getReportMessage(550, $mail, 'no-role-permission@staging.olzimmerberg.ch')}

                (no html body)

                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
    }

    public function testProcessEmailCommandToRole(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('authUtils')->has_role_permission_by_query['role_email'] = true;
        $mail = new FakeProcessEmailCommandMail(
            12,
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
        $artifacts = [];
        $num_role_users = count(FakeRole::someRole()->getUsers()->toArray());
        $mailer->expects($this->exactly($num_role_users))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            $this->callback(function (Envelope $envelope) use (&$artifacts) {
                $artifacts['envelope'] = [...($artifacts['envelope'] ?? []), $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to admin-user@staging.olzimmerberg.ch',
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to vorstand-user@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame(['+flagged', '-flagged', '+flagged', '-flagged'], $mail->flag_actions);

        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: 
                Cc: 
                Bcc: 
                Subject: Test subject

                Test text

                Test html

                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: 
                Cc: 
                Bcc: 
                Subject: Test subject

                Test text

                Test html

                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: admin-user@staging.olzimmerberg.ch
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: vorstand-user@staging.olzimmerberg.ch
                ZZZZZZZZZZ,
        ], array_map(function ($envelope) {
            return $this->emailUtils()->getComparableEnvelope($envelope);
        }, $artifacts['envelope']));
    }

    public function testProcessEmailCommandToOldRole(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('authUtils')->has_role_permission_by_query['role_email'] = true;
        $mail = new FakeProcessEmailCommandMail(
            12,
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
        $artifacts = [];
        $num_role_users = count(FakeRole::someRole()->getUsers()->toArray());
        $mailer->expects($this->exactly($num_role_users + 1))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            $this->callback(function (?Envelope $envelope) use (&$artifacts) {
                $artifacts['envelope'] = [...($artifacts['envelope'] ?? []), $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO Email forwarded from somerole-old@staging.olzimmerberg.ch to admin-user@staging.olzimmerberg.ch',
            'INFO Email forwarded from somerole-old@staging.olzimmerberg.ch to vorstand-user@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame(['+flagged', '-flagged', '+flagged', '-flagged'], $mail->flag_actions);

        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: "OLZ Bot" <fake@staging.olzimmerberg.ch>
                Reply-To: 
                To: "From Name" <from@from-domain.com>
                Cc: 
                Bcc: 
                Subject: Empfänger hat eine neue E-Mail-Adresse
                
                Hallo From Name (from@from-domain.com),
                
                Dies ist eine Mitteilung der E-Mail-Weiterleitung:
                Die E-Mail-Adresse "somerole-old@staging.olzimmerberg.ch" ist neu unter "somerole@olzimmerberg.ch" erreichbar.
                
                Dies nur zur Information. Ihre E-Mail wurde automatisch weitergeleitet!
                
                (no html body)
                
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: 
                Cc: 
                Bcc: 
                Subject: Test subject

                Test text

                Test html

                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: 
                Cc: 
                Bcc: 
                Subject: Test subject

                Test text

                Test html

                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
        $this->assertSame([
            null,
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: admin-user@staging.olzimmerberg.ch
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: vorstand-user@staging.olzimmerberg.ch
                ZZZZZZZZZZ,
        ], array_map(function ($envelope) {
            return $this->emailUtils()->getComparableEnvelope($envelope);
        }, $artifacts['envelope']));
    }

    public function testProcessEmailCommandSendingError(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(
            12,
            'someone@staging.olzimmerberg.ch',
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
        $mailer
            ->expects($this->exactly(1))
            ->method('send')
            ->will($this->throwException(new \Exception('mocked-error')))
        ;

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'CRITICAL Error forwarding email from someone@staging.olzimmerberg.ch to someone@gmail.com: mocked-error',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertNull($mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame(['+flagged'], $mail->flag_actions);
    }

    public function testProcessEmailCommandToMultiple(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        WithUtilsCache::get('authUtils')->has_role_permission_by_query['role_email'] = true;
        $mail = new FakeProcessEmailCommandMail(
            12,
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
        $artifacts = [];
        $num_role_users = count(FakeRole::someRole()->getUsers()->toArray());
        $mailer->expects($this->exactly(1 + $num_role_users))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            $this->callback(function (Envelope $envelope) use (&$artifacts) {
                $artifacts['envelope'] = [...($artifacts['envelope'] ?? []), $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO Email forwarded from someone@staging.olzimmerberg.ch to someone@gmail.com',
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to admin-user@staging.olzimmerberg.ch',
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to vorstand-user@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame(
            ['+flagged', '-flagged', '+flagged', '-flagged', '+flagged', '-flagged'],
            $mail->flag_actions,
        );

        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: someone@staging.olzimmerberg.ch, somerole@staging.olzimmerberg.ch
                Cc: 
                Bcc: 
                Subject: Test subject

                Test text

                Test html

                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: someone@staging.olzimmerberg.ch, somerole@staging.olzimmerberg.ch
                Cc: 
                Bcc: 
                Subject: Test subject

                Test text

                Test html

                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: someone@staging.olzimmerberg.ch, somerole@staging.olzimmerberg.ch
                Cc: 
                Bcc: 
                Subject: Test subject

                Test text

                Test html

                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: someone@gmail.com
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: admin-user@staging.olzimmerberg.ch
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: vorstand-user@staging.olzimmerberg.ch
                ZZZZZZZZZZ,
        ], array_map(function ($envelope) {
            return $this->emailUtils()->getComparableEnvelope($envelope);
        }, $artifacts['envelope']));
    }

    public function testProcessEmailCommandToCcBcc(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        WithUtilsCache::get('authUtils')->has_role_permission_by_query['role_email'] = true;
        $mail = new FakeProcessEmailCommandMail(
            12,
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
        $artifacts = [];
        $num_role_users = count(FakeRole::someRole()->getUsers()->toArray());
        $mailer->expects($this->exactly(1 + $num_role_users))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            $this->callback(function (Envelope $envelope) use (&$artifacts) {
                $artifacts['envelope'] = [...($artifacts['envelope'] ?? []), $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO Email forwarded from someone@staging.olzimmerberg.ch to someone@gmail.com',
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to admin-user@staging.olzimmerberg.ch',
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to vorstand-user@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame(
            ['+flagged', '-flagged', '+flagged', '-flagged', '+flagged', '-flagged'],
            $mail->flag_actions,
        );

        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: 
                Cc: "Some One" <someone@staging.olzimmerberg.ch>
                Bcc: "Some Role" <somerole@staging.olzimmerberg.ch>
                Subject: Test subject

                Test text

                Test html

                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: 
                Cc: "Some One" <someone@staging.olzimmerberg.ch>
                Bcc: "Some Role" <somerole@staging.olzimmerberg.ch>
                Subject: Test subject

                Test text

                Test html

                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: 
                Cc: "Some One" <someone@staging.olzimmerberg.ch>
                Bcc: "Some Role" <somerole@staging.olzimmerberg.ch>
                Subject: Test subject

                Test text

                Test html

                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: someone@gmail.com
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: admin-user@staging.olzimmerberg.ch
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: vorstand-user@staging.olzimmerberg.ch
                ZZZZZZZZZZ,
        ], array_map(function ($envelope) {
            return $this->emailUtils()->getComparableEnvelope($envelope);
        }, $artifacts['envelope']));
    }

    public function testProcessEmailCommandMultipleEmails(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        WithUtilsCache::get('authUtils')->has_role_permission_by_query['role_email'] = true;
        $mail1 = new FakeProcessEmailCommandMail(
            11,
            'someone@staging.olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
            new Attribute('subject', 'Test subject 1'),
            'Test html 1',
            'Test text 1'
        );
        $mail2 = new FakeProcessEmailCommandMail(
            12,
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
        $artifacts = [];
        $num_role_users = count(FakeRole::someRole()->getUsers()->toArray());
        $mailer->expects($this->exactly(1 + $num_role_users))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            $this->callback(function (Envelope $envelope) use (&$artifacts) {
                $artifacts['envelope'] = [...($artifacts['envelope'] ?? []), $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO Email forwarded from someone@staging.olzimmerberg.ch to someone@gmail.com',
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to admin-user@staging.olzimmerberg.ch',
            'INFO Email forwarded from somerole@staging.olzimmerberg.ch to vorstand-user@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail1->moved_to);
        $this->assertSame('INBOX.Processed', $mail2->moved_to);
        $this->assertTrue($mail1->is_body_fetched);
        $this->assertTrue($mail2->is_body_fetched);
        $this->assertSame(['+flagged', '-flagged'], $mail1->flag_actions);
        $this->assertSame(['+flagged', '-flagged', '+flagged', '-flagged'], $mail2->flag_actions);

        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: 
                Cc: 
                Bcc: 
                Subject: Test subject 1

                Test text 1

                Test html 1

                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: 
                Cc: 
                Bcc: 
                Subject: Test subject 2

                Test text 2

                Test html 2

                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: 
                Cc: 
                Bcc: 
                Subject: Test subject 2

                Test text 2

                Test html 2

                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: someone@gmail.com
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: admin-user@staging.olzimmerberg.ch
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: vorstand-user@staging.olzimmerberg.ch
                ZZZZZZZZZZ,
        ], array_map(function ($envelope) {
            return $this->emailUtils()->getComparableEnvelope($envelope);
        }, $artifacts['envelope']));
    }

    public function testProcessEmailCommandWithAttachments(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createPartialMock(MailerInterface::class, ['send']);
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(
            12,
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
        $mail->attachments = new AttachmentCollection([
            'attachmentId1' => $attachment1,
            'attachmentId2' => $attachment2,
        ]);
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $artifacts = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            $this->callback(function (Envelope $envelope) use (&$artifacts) {
                $artifacts['envelope'] = [...($artifacts['envelope'] ?? []), $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO Saving attachment Attachment1.pdf to AAAAAAAAAAAAAAAAAAAAAAAA.pdf...',
            'INFO Saving attachment Attachment2.docx to AAAAAAAAAAAAAAAAAAAAAAAA.docx...',
            'INFO Email forwarded from someone@staging.olzimmerberg.ch to someone@gmail.com',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame(['+flagged', '-flagged'], $mail->flag_actions);
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: "From Name" <from@from-domain.com>
                Reply-To: "From Name" <from@from-domain.com>
                To: 
                Cc: 
                Bcc: 
                Subject: Test subject

                Test text

                Test html

                Attachment1.pdf
                Attachment2.docx
                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: someone@gmail.com
                ZZZZZZZZZZ,
        ], array_map(function ($envelope) {
            return $this->emailUtils()->getComparableEnvelope($envelope);
        }, $artifacts['envelope']));
    }

    public function testProcessEmailCommandWithFailingAttachment(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(
            12,
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
            'Attachment1.pdf',
            $should_fail = true
        );
        $mail->attachments = new AttachmentCollection([
            'attachmentId' => $attachment,
        ]);
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $mailer->expects($this->exactly(0))->method('send');

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO Saving attachment Attachment1.pdf to AAAAAAAAAAAAAAAAAAAAAAAA.pdf...',
            'CRITICAL Error forwarding email from someone@staging.olzimmerberg.ch to someone@gmail.com: Could not save attachment Attachment1.pdf to AAAAAAAAAAAAAAAAAAAAAAAA.pdf.',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertNull($mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame(['+flagged'], $mail->flag_actions);
    }

    public function testProcessEmailCommandEmailDeliveryNoticeSpam(): void {
        new ClientManager([]);
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        $mail = new FakeProcessEmailCommandMail(
            12,
            'fake@staging.olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('MAILER-DAEMON@219.hosttech.eu', 'Mail Delivery System')),
            new Attribute('subject', 'Undelivered Mail Returned to Sender'),
            'replied: 550 likely spam',
            '',
        );
        $attachment = new FakeProcessEmailCommandAttachment('a8e4cc3b');
        $mail->attachments = new AttachmentCollection([
            'attachmentId' => $attachment,
        ]);
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        // no bounce email!
        $mailer->expects($this->exactly(0))->method('send');

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO E-Mail 12 to bot...',
            'INFO Spam notice score 4 of 3',
            'INFO Spam notice E-Mail from MAILER-DAEMON@219.hosttech.eu to bot: E-Mail "" is spam',
            'WARNING getMails soft error:',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame([], $mail->flag_actions);
    }

    public function testProcessEmailCommandEmailToFrom(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        $mail = new FakeProcessEmailCommandMail(
            12,
            'from@staging.olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@staging.olzimmerberg.ch', 'From Name')),
            new Attribute('subject', 'Test subject'),
        );
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        // no bounce email!
        $mailer->expects($this->exactly(0))->method('send');

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO E-Mail 12 to inexistent user/role username: from',
            'NOTICE sendReportEmail: Avoiding email loop for from@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertFalse($mail->is_body_fetched);
        $this->assertSame([], $mail->flag_actions);
    }

    public function testProcessEmailCommandCleanUp(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-05 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        $old_processed_mail = new FakeProcessEmailCommandMail(12);
        $old_processed_mail->date = new Attribute('date', [new Carbon('2020-03-13 18:00:00')]);
        $processed_mail = new FakeProcessEmailCommandMail(13);
        $processed_mail->date = new Attribute('date', [new Carbon('2020-03-13 19:00:00')]);
        $old_archived_mail = new FakeProcessEmailCommandMail(22);
        $old_archived_mail->date = new Attribute('date', [new Carbon('2020-01-13 18:00:00')]);
        $archived_mail = new FakeProcessEmailCommandMail(23);
        $archived_mail->date = new Attribute('date', [new Carbon('2020-03-12 18:00:00')]);
        $old_spam_mail = new FakeProcessEmailCommandMail(32);
        $old_spam_mail->date = new Attribute('date', [new Carbon('2006-01-13 18:00:00')]);
        $spam_mail = new FakeProcessEmailCommandMail(33);
        $spam_mail->date = new Attribute('date', [new Carbon('2020-01-13 18:00:00')]);
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [];
        WithUtilsCache::get('emailUtils')->client->folders['INBOX.Processed'] = [$processed_mail, $old_processed_mail];
        WithUtilsCache::get('emailUtils')->client->folders['INBOX.Archive'] = [$archived_mail, $old_archived_mail];
        WithUtilsCache::get('emailUtils')->client->folders['INBOX.Spam'] = [$old_spam_mail, $spam_mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        // no bounce email!
        $mailer->expects($this->exactly(0))->method('send');

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'NOTICE Doing E-Mail cleanup now...',
            'WARNING getMails soft error:',
            'INFO Removing old archived E-Mails...',
            'WARNING getMails soft error:',
            'INFO Removing old spam E-Mails...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);

        $this->assertNull($old_processed_mail->deleted);
        $this->assertSame('INBOX.Archive', $old_processed_mail->moved_to);
        $this->assertFalse($old_processed_mail->is_body_fetched);
        $this->assertSame([], $old_processed_mail->flag_actions);

        $this->assertNull($processed_mail->deleted);
        $this->assertNull($processed_mail->moved_to);
        $this->assertFalse($processed_mail->is_body_fetched);
        $this->assertSame([], $processed_mail->flag_actions);

        $this->assertSame([true, null, false], $old_archived_mail->deleted);
        $this->assertNull($old_archived_mail->moved_to);
        $this->assertFalse($old_archived_mail->is_body_fetched);
        $this->assertSame([], $old_archived_mail->flag_actions);

        $this->assertNull($archived_mail->deleted);
        $this->assertNull($archived_mail->moved_to);
        $this->assertFalse($archived_mail->is_body_fetched);
        $this->assertSame([], $archived_mail->flag_actions);

        $this->assertSame([true, null, false], $old_spam_mail->deleted);
        $this->assertNull($old_spam_mail->moved_to);
        $this->assertFalse($old_spam_mail->is_body_fetched);
        $this->assertSame([], $old_spam_mail->flag_actions);

        $this->assertNull($spam_mail->deleted);
        $this->assertNull($spam_mail->moved_to);
        $this->assertFalse($spam_mail->is_body_fetched);
        $this->assertSame([], $spam_mail->flag_actions);
    }

    public function testProcessEmailCommandToSpamHoneypotEmailAddress(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('authUtils')->has_permission_by_query['user_email'] = true;
        $mail = new FakeProcessEmailCommandMail(
            12,
            's.p.a.m@staging.olzimmerberg.ch',
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
        $mailer->expects($this->exactly(0))->method('send');

        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO Received honeypot spam E-Mail to: s.p.a.m',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Spam', $mail->moved_to);
        $this->assertFalse($mail->is_body_fetched);
        $this->assertSame([], $mail->flag_actions);
    }

    public function testProcessEmailCommandGet431ReportMessage(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        $mail = new FakeProcessEmailCommandMail(1);
        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $this->assertStringContainsString(
            '431 Not enough storage or out of memory',
            $job->getReportMessage(431, $mail, 'no-such-username@staging.olzimmerberg.ch'),
        );
    }

    public function testProcessEmailCommandGet550ReportMessage(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        $mail = new FakeProcessEmailCommandMail(1);
        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $this->assertStringContainsString(
            '<no-such-username@staging.olzimmerberg.ch>: 550 sorry, no mailbox here by that name',
            $job->getReportMessage(550, $mail, 'no-such-username@staging.olzimmerberg.ch'),
        );
    }

    public function testProcessEmailCommandGetOtherReportMessage(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        $mail = new FakeProcessEmailCommandMail(1);
        $job = new ProcessEmailCommand();
        $job->setMailer($mailer);
        $this->assertStringContainsString(
            '123456 Unknown error',
            $job->getReportMessage(123456, $mail, 'no-such-username@staging.olzimmerberg.ch'),
        );
    }
}
