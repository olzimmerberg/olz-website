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
        public ?Attribute $message_id = null,
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

    public function getMessageId(): Attribute {
        return $this->message_id ?? new Attribute('message_id');
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

    /** @param array<string>|string $flag_arg */
    public function setFlag(array|string $flag_arg): bool {
        $flags = is_array($flag_arg) ? $flag_arg : [$flag_arg];
        foreach ($flags as $flag) {
            $this->flag_actions[] = "+{$flag}";
        }
        return true;
    }

    /** @param array<string>|string $flag_arg */
    public function unsetFlag(array|string $flag_arg): bool {
        $flags = is_array($flag_arg) ? $flag_arg : [$flag_arg];
        foreach ($flags as $flag) {
            $this->flag_actions[] = "-{$flag}";
        }
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
        return "References: <fake-message-id>\r\n Some spam email content";
    }
}

/**
 * @internal
 *
 * @coversNothing
 */
class ProcessEmailCommandForTest extends ProcessEmailCommand {
    public function testOnlyGetSpamNoticeScore(string $body): int {
        return $this->getSpamNoticeScore($body);
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
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
        WithUtilsCache::get('emailUtils')->client->exception = true;
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $mailer->expects($this->exactly(0))->method('send');

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'ERROR Error connecting to IMAP: Failed at something',
            'NOTICE Failed running command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertFalse(WithUtilsCache::get('emailUtils')->client->is_connected);
    }

    public function testProcessEmailCommandWithMailToWrongDomain(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
        $mail = new FakeProcessEmailCommandMail(12, 'someone@other-domain.com');
        WithUtilsCache::get('emailUtils')->client->folders['INBOX'] = [$mail];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $mailer->expects($this->exactly(0))->method('send');

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO E-Mail 12 to non-staging.olzimmerberg.ch address: someone@other-domain.com',
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
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
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
        $emails = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$emails) {
                $emails = [...$emails, $email];
                return true;
            }),
            null,
        );

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO E-Mail 12 to inexistent user/role username: no-such-username',
            'INFO Report E-Mail sent to from@from-domain.com',
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
        }, $emails));
    }

    public function testProcessEmailCommandNoUserEmailPermission(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
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
        $emails = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$emails) {
                $emails = [...$emails, $email];
                return true;
            }),
            null,
        );

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'NOTICE E-Mail 12 to user with no user_email permission: no-permission',
            'INFO Report E-Mail sent to from@from-domain.com',
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
        }, $emails));
    }

    public function testProcessEmailCommandEmptyToException(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
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
        $emails = [];
        $envelopes = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$emails) {
                $emails = [...$emails, $email];
                return true;
            }),
            $this->callback(function (Envelope $envelope) use (&$envelopes) {
                $envelopes = [...$envelopes, $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
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
        }, $emails));
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: someone@gmail.com
                ZZZZZZZZZZ,
        ], array_map(function ($envelope) {
            return $this->emailUtils()->getComparableEnvelope($envelope);
        }, $envelopes));
    }

    public function testProcessEmailCommandRfcComplianceException(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
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
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
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
        $emails = [];
        $envelopes = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$emails) {
                $emails = [...$emails, $email];
                return true;
            }),
            $this->callback(function (Envelope $envelope) use (&$envelopes) {
                $envelopes = [...$envelopes, $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
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
        }, $emails));
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: someone@gmail.com
                ZZZZZZZZZZ,
        ], array_map(function ($envelope) {
            return $this->emailUtils()->getComparableEnvelope($envelope);
        }, $envelopes));
    }

    public function testProcessEmailCommandToUserEmptyEmail(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
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
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
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
        $emails = [];
        $envelopes = [];
        $mailer->expects($this->exactly(2))->method('send')->with(
            $this->callback(function (Email $email) use (&$emails) {
                $emails = [...$emails, $email];
                return true;
            }),
            $this->callback(function (?Envelope $envelope) use (&$envelopes) {
                $envelopes = [...$envelopes, $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO Redirect E-Mail sent to from@from-domain.com: someone-old@staging.olzimmerberg.ch -> someone@staging.olzimmerberg.ch',
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
                Die E-Mail-Adresse "someone-old@staging.olzimmerberg.ch" ist neu unter "someone@staging.olzimmerberg.ch" erreichbar.
                
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
        }, $emails));
        $this->assertSame([
            null,
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: someone-old@gmail.com
                ZZZZZZZZZZ,
        ], array_map(function ($envelope) {
            return $this->emailUtils()->getComparableEnvelope($envelope);
        }, $envelopes));
    }

    public function testProcessEmailCommandNoRoleEmailPermission(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
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
        $emails = [];
        $envelopes = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$emails) {
                $emails = [...$emails, $email];
                return true;
            }),
            null,
        );

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'WARNING E-Mail 12 to role with no role_email permission: no-role-permission',
            'INFO Report E-Mail sent to from@from-domain.com',
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
        }, $emails));
    }

    public function testProcessEmailCommandToRole(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
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
        $emails = [];
        $envelopes = [];
        $num_role_users = count(FakeRole::someRole()->getUsers()->toArray());
        $mailer->expects($this->exactly($num_role_users))->method('send')->with(
            $this->callback(function (Email $email) use (&$emails) {
                $emails = [...$emails, $email];
                return true;
            }),
            $this->callback(function (Envelope $envelope) use (&$envelopes) {
                $envelopes = [...$envelopes, $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
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
        }, $emails));
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
        }, $envelopes));
    }

    public function testProcessEmailCommandToOldRole(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
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
        $emails = [];
        $envelopes = [];
        $num_role_users = count(FakeRole::someRole()->getUsers()->toArray());
        $mailer->expects($this->exactly($num_role_users + 1))->method('send')->with(
            $this->callback(function (Email $email) use (&$emails) {
                $emails = [...$emails, $email];
                return true;
            }),
            $this->callback(function (?Envelope $envelope) use (&$envelopes) {
                $envelopes = [...$envelopes, $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\ProcessEmailCommand...',
            'WARNING getMails soft error:',
            'WARNING getMails soft error:',
            'INFO Redirect E-Mail sent to from@from-domain.com: somerole-old@staging.olzimmerberg.ch -> somerole@staging.olzimmerberg.ch',
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
                Die E-Mail-Adresse "somerole-old@staging.olzimmerberg.ch" ist neu unter "somerole@staging.olzimmerberg.ch" erreichbar.
                
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
        }, $emails));
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
        }, $envelopes));
    }

    public function testProcessEmailCommandSendingError(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
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
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
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
        $emails = [];
        $envelopes = [];
        $num_role_users = count(FakeRole::someRole()->getUsers()->toArray());
        $mailer->expects($this->exactly(1 + $num_role_users))->method('send')->with(
            $this->callback(function (Email $email) use (&$emails) {
                $emails = [...$emails, $email];
                return true;
            }),
            $this->callback(function (Envelope $envelope) use (&$envelopes) {
                $envelopes = [...$envelopes, $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
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
        }, $emails));
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
        }, $envelopes));
    }

    public function testProcessEmailCommandToCcBcc(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
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
        $emails = [];
        $envelopes = [];
        $num_role_users = count(FakeRole::someRole()->getUsers()->toArray());
        $mailer->expects($this->exactly(1 + $num_role_users))->method('send')->with(
            $this->callback(function (Email $email) use (&$emails) {
                $emails = [...$emails, $email];
                return true;
            }),
            $this->callback(function (Envelope $envelope) use (&$envelopes) {
                $envelopes = [...$envelopes, $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
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
        }, $emails));
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
        }, $envelopes));
    }

    public function testProcessEmailCommandMultipleEmails(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
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
        $emails = [];
        $envelopes = [];
        $num_role_users = count(FakeRole::someRole()->getUsers()->toArray());
        $mailer->expects($this->exactly(1 + $num_role_users))->method('send')->with(
            $this->callback(function (Email $email) use (&$emails) {
                $emails = [...$emails, $email];
                return true;
            }),
            $this->callback(function (Envelope $envelope) use (&$envelopes) {
                $envelopes = [...$envelopes, $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
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
        }, $emails));
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
        }, $envelopes));
    }

    public function testProcessEmailCommandWithAttachments(): void {
        $throttling_repo = WithUtilsCache::get('entityManager')->repositories[Throttling::class];
        $throttling_repo->expected_event_name = 'email_cleanup';
        $throttling_repo->last_occurrence = '2020-03-13 19:30:00';
        $mailer = $this->createPartialMock(MailerInterface::class, ['send']);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
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
        $emails = [];
        $envelopes = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$emails) {
                $emails = [...$emails, $email];
                return true;
            }),
            $this->callback(function (Envelope $envelope) use (&$envelopes) {
                $envelopes = [...$envelopes, $envelope];
                return true;
            }),
        );

        $job = new ProcessEmailCommand();
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
        }, $emails));
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                Sender: "From Name" <from@from-domain.com>
                Recipients: someone@gmail.com
                ZZZZZZZZZZ,
        ], array_map(function ($envelope) {
            return $this->emailUtils()->getComparableEnvelope($envelope);
        }, $envelopes));
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
            'INFO Spam notice E-Mail from MAILER-DAEMON@219.hosttech.eu to bot: Message-ID "fake-message-id" is spam',
            'WARNING getMails soft error:',
            'NOTICE Spam E-Mail with Message-ID "fake-message-id" not found!',
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
        $old_processed_mail->date = new Attribute('date', [new Carbon('2020-03-13 11:00:00')]);
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

    public function testProcessEmailCommandGetSpamNoticeScore(): void {
        $expected_spam_notice_scores = [
            "\r\n\r\n<monitor@status.olzimmerberg.ch>: mail for status.olzimmerberg.ch loops back to\r\n myself" => 0,
            "\r\n\r\n<anonymous@domain.com>: host mx01.domain.com[123.234.12.23] said: 550 The\r\n content of this message looked like spam. (in reply to end of DATA command)" => 4,
            "\r\n\r\n<anonymous@domain.com>: host domain.com[100.99.98.97]\r\n said: 550 No Such User Here\" (in reply to RCPT TO command)" => 0,
            "\r\n\r\n<anonymous@domain.com>: host\r\n mail.domain.com[2.3.4.5] said: 550 5.1.1\r\n <anonymous@domain.com>: Recipient address rejected:\r\n domain.com (in reply to RCPT TO command)" => 1,
            "\r\n\r\n<anonymous@domain.com>: host mx02.domain.com[194.195.192.193] said: 550 A URL\r\n in this email (focuspower . in) is listed on https://spamrl.com/. Please\r\n resolve and retry (in reply to end of DATA command)" => 7,
            "\r\n\r\n<anonymous@domain.com>: host\r\n mx02.domain.com[90.91.92.93] said: 554 5.2.0 MXIN603\r\n DMARC validation failed.\r\n ;id=DzDctlIXbqGHSDzDctcDSp;sid=DzDctlIXbqGHS;mta=mx1-prd-nl1-sun;dt=2024-11-21T05:55:04+01:00;ipsrc=185.178.193.60;\r\n (in reply to end of DATA command)" => 4,
            "\r\n\r\n<anonymous@domain.com>: host mail.domain.com[160.161.162.163] said: 550 No\r\n Such User Here (in reply to RCPT TO command)" => 0,
            "\r\n\r\n<anonymous@domain.com>: host\r\n mx02.domain.com[30.31.32.33] said: 554 5.2.0 sc999:\r\n reputation ratelimit in effect -\r\n https://support.bluewin.ch/provider/bounce/aXA9MTg1LjE3OC4xOTMuNjA7Yz1zYzk5OTtpZD1FOFJYdDhzYmhNY0d3RThSWHQwQmdkO3RzPTE3MzIyMDAzNjM=\r\n (in reply to end of DATA command)" => 3,
            "\r\n\r\n<anonymous@domain.com>: host\r\n mx02.domain.com[52.53.54.55] said: 550 5.7.509\r\n Access denied, sending domain [DOMAIN.EU] does not pass DMARC\r\n verification and has a DMARC policy of reject.\r\n [ReC8B6sUXqTcV.EUR2.PROD.DOMAIN.COM 2024-11-21T16:31:06.930Z\r\n b3vmJAod8nxQKYRZ] [TEiJtMyoqMx9A.namprd02.prod.domain.com\r\n 2024-11-21T16:31:06.998Z b5TXkQyfj23R9bUU]\r\n [j37b9htJM78iEmh.namprd04.prod.domain.com 2024-11-21T16:31:07.008Z\r\n v8ctKsNv3XUjpei9] (in reply to end of DATA command)" => 6,
            "\r\n\r\n<anonymous@domain.com>: host mx02.mail.domain.com[17.18.19.20] said:\r\n 554 5.7.1 [CS01] Message rejected due to local policy. Please visit\r\n https://support.apple.com/en-us/HT204137 (in reply to end of DATA command)" => 5,
            "\r\n\r\n<anonymous@domain.com>: host mx02.domain.com[123.234.12.23] said: 550 This\r\n message has been reported as spam by other users. (in reply to end of DATA\r\n command)" => 4,
            "\r\n\r\n<anonymous@domain.com>: Host or domain name not found. Name service error for\r\n name=domain.com type=A: Host not found" => 0,
            "\r\n\r\n<anonymous@domain.com>: host mx02.domain.com[191.192.193.194]\r\n said: 554 5.2.0 sc981: Rejected due to policy reasons -\r\n https://support.bluewin.ch/en/provider/bounce/aXA9MTg1LjE3OC4xOTMuNjA7Yz1zYzk4MTtpZD1FTVNhdGx0YXZxNzFDRU1TYXR6cm1qO3RzPTE3MzIyNTQyNDU=\r\n (in reply to end of DATA command)" => 4,
            "\r\n\r\n<anonymous@domain.com>: host mx02.domain.com[199.200.201.202] said: 550 High\r\n probability of spam (in reply to end of DATA command)" => 4,
            "\r\n\r\n<anonymous@domain.com>: host\r\n mx.mail.protection.domain.com[51.52.53.54] said: 550\r\n 5.4.1 Recipient address rejected: Access denied.\r\n [ASkDUMyjW63vx9p.eur06.prod.domain.com 2024-11-22T15:27:37.730Z\r\n HEZKiZPX5xebDqsq] (in reply to RCPT TO command)" => 1,
        ];
        $job = new ProcessEmailCommandForTest();
        $actual_spam_notice_scores = [];
        foreach ($expected_spam_notice_scores as $body => $score) {
            $actual_spam_notice_scores[$body] = $job->testOnlyGetSpamNoticeScore($body);
        }
        $this->assertSame(
            $expected_spam_notice_scores,
            $actual_spam_notice_scores,
        );
    }
}
