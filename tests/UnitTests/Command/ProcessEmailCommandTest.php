<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\ProcessEmailCommand;
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
}

/**
 * @internal
 *
 * @covers \Olz\Command\ProcessEmailCommand
 */
final class ProcessEmailCommandTest extends UnitTestCase {
    public function testProcessEmailCommandWithError(): void {
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
            'INFO E-Mail 12 to non-olzimmerberg.ch address: someone@other-domain.com',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());

        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertFalse($mail->is_body_fetched);
        $this->assertSame([], $mail->flag_actions);
    }

    public function testProcessEmailCommandNoSuchUser(): void {
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
            'NOTICE Email from someone@staging.olzimmerberg.ch to someone@gmail.com is not RFC-compliant: Email "non-rfc-compliant-email" does not comply with addr-spec of RFC 2822.',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertFalse($mail->is_body_fetched);
        $this->assertSame(['+flagged'], $mail->flag_actions);
    }

    public function testProcessEmailCommandToUser(): void {
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
            'CRITICAL Error forwarding email from empty-email@staging.olzimmerberg.ch to : getUserAddress: empty-email (User ID: 1) has no email.',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertNull($mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame(['+flagged'], $mail->flag_actions);
    }

    public function testProcessEmailCommandToOldUser(): void {
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
            'CRITICAL Error forwarding email from someone@staging.olzimmerberg.ch to someone@gmail.com: mocked-error',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertNull($mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame(['+flagged'], $mail->flag_actions);
    }

    public function testProcessEmailCommandToMultiple(): void {
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
            'INFO Saving attachment Attachment1.pdf to AAAAAAAAAAAAAAAAAAAAAAAA.pdf...',
            'CRITICAL Error forwarding email from someone@staging.olzimmerberg.ch to someone@gmail.com: Could not save attachment Attachment1.pdf to AAAAAAAAAAAAAAAAAAAAAAAA.pdf.',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertNull($mail->moved_to);
        $this->assertTrue($mail->is_body_fetched);
        $this->assertSame(['+flagged'], $mail->flag_actions);
    }

    public function testProcessEmailCommandEmailToSmtpFrom(): void {
        $mailer = $this->createMock(MailerInterface::class);
        $mail = new FakeProcessEmailCommandMail(
            12,
            'fake@staging.olzimmerberg.ch',
            new Attribute('to', []),
            new Attribute('cc', []),
            new Attribute('bcc', []),
            new Attribute('from', getAddress('from@from-domain.com', 'From Name')),
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
            'INFO E-Mail 12 to inexistent user/role username: fake',
            'NOTICE sendReportEmail: Avoiding email loop for fake@staging.olzimmerberg.ch',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Processed', $mail->moved_to);
        $this->assertFalse($mail->is_body_fetched);
        $this->assertSame([], $mail->flag_actions);
    }

    public function testProcessEmailCommandToSpamHoneypotEmailAddress(): void {
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
            'INFO Spam E-Mail to: s.p.a.m',
            'INFO Successfully ran command Olz\Command\ProcessEmailCommand.',
        ], $this->getLogs());
        $this->assertTrue(WithUtilsCache::get('emailUtils')->client->is_connected);
        $this->assertSame('INBOX.Spam', $mail->moved_to);
        $this->assertFalse($mail->is_body_fetched);
        $this->assertSame(['+spam'], $mail->flag_actions);
    }

    public function testProcessEmailCommandGet431ReportMessage(): void {
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
