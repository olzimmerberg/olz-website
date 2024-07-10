<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Entity\Roles\Role;
use Olz\Entity\Throttling;
use Olz\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Exception\RfcComplianceException;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Webklex\PHPIMAP\Attribute;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\Exceptions\ImapServerErrorException;
use Webklex\PHPIMAP\Exceptions\ResponseException;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Support\MessageCollection;

// 2 = processed
// 1 = spam
// 0 = failed

#[AsCommand(name: 'olz:process-email')]
class ProcessEmailCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    public const MAX_LOOP = 100;
    public int $archiveAfterSeconds = 60 * 60;
    public int $deleteArchivedAfterSeconds = 30 * 24 * 60 * 60;
    public int $deleteSpamAfterSeconds = 365 * 24 * 60 * 60;
    public string $host = 'olzimmerberg.ch';
    public string $processed_mailbox = 'INBOX.Processed';
    public string $archive_mailbox = 'INBOX.Archive';
    public string $failed_mailbox = 'INBOX.Failed';
    public string $spam_mailbox = 'INBOX.Spam';

    protected Client $client;

    protected function handle(InputInterface $input, OutputInterface $output): int {
        ini_set('memory_limit', '500M');

        $this->client = $this->emailUtils()->getImapClient();
        $this->client->connect();

        $mailboxes = [
            $this->processed_mailbox,
            $this->archive_mailbox,
            $this->failed_mailbox,
            $this->spam_mailbox,
        ];
        foreach ($mailboxes as $mailbox) {
            try {
                $this->client->createFolder($mailbox);
            } catch (ImapServerErrorException $exc) {
                // ignore when folder already exists
            }
        }

        if ($this->shouldDoCleanup()) {
            $this->log()->notice("Doing E-Mail cleanup now...");
            $archived_mails = $this->getArchivedMails();
            $this->deleteOldArchivedMails($archived_mails);
            $spam_mails = $this->getSpamMails();
            $this->deleteOldSpamMails($spam_mails);
            $throttling_repo = $this->entityManager()->getRepository(Throttling::class);
            $throttling_repo->recordOccurrenceOf('email_cleanup', $this->dateUtils()->getIsoNow());
        }

        $processed_mails = $this->getProcessedMails();
        $this->archiveOldProcessedMails($processed_mails);
        $is_message_id_processed = $this->getIsMessageIdProcessed($processed_mails);
        $inbox_mails = $this->getInboxMails();
        $newly_processed_mails = [];
        $newly_spam_mails = [];
        foreach ($inbox_mails as $mail) {
            $message_id = $mail->message_id ? $mail->message_id->first() : null;
            $is_processed = ($is_message_id_processed[$message_id] ?? false);
            $result = $this->processMail($mail, $is_processed);
            if ($result === 2) {
                $newly_processed_mails[] = $mail;
            } elseif ($result === 1) {
                $newly_spam_mails[] = $mail;
            }
            if ($message_id !== null) {
                $is_message_id_processed[$message_id] = true;
            }
        }

        foreach ($newly_processed_mails as $mail) {
            $mail->move($folder_path = $this->processed_mailbox);
        }
        foreach ($newly_spam_mails as $mail) {
            $mail->move($folder_path = $this->spam_mailbox);
        }

        return Command::SUCCESS;
    }

    public function shouldDoCleanup(): bool {
        $throttling_repo = $this->entityManager()->getRepository(Throttling::class);
        $last_cleanup = $throttling_repo->getLastOccurrenceOf('email_cleanup');
        if (!$last_cleanup) {
            return true;
        }
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $min_interval = \DateInterval::createFromDateString('+1 week');
        $min_now = $last_cleanup->add($min_interval);
        return $now > $min_now;
    }

    protected function getProcessedMails(): MessageCollection {
        return $this->getMails($this->processed_mailbox);
    }

    protected function getArchivedMails(): MessageCollection {
        return $this->getMails($this->archive_mailbox);
    }

    protected function getSpamMails(): MessageCollection {
        return $this->getMails($this->spam_mailbox);
    }

    protected function getInboxMails(): MessageCollection {
        return $this->getMails('INBOX');
    }

    protected function getMails(string $folder_path): MessageCollection {
        try {
            $folder = $this->client->getFolderByPath($folder_path);
            $query = $folder->messages();
            $query->leaveUnread();
            $query->setFetchBody(false);
            return $query->all()->get();
        } catch (ResponseException $exc) {
            if (!preg_match('/Empty response/i', $exc->getMessage())) {
                $this->log()->critical("ResponseException in getMails: {$exc->getMessage()}", [$exc]);
                throw $exc;
            }
            return new MessageCollection([]);
        } catch (\Exception $exc) {
            $this->log()->critical("Exception in getMails: {$exc->getMessage()}", [$exc]);
            throw $exc;
        }
    }

    protected function archiveOldProcessedMails(MessageCollection $processed_mails): void {
        $now_timestamp = strtotime($this->dateUtils()->getIsoNow());
        foreach ($processed_mails as $mail) {
            $message_timestamp = $mail->date->first()->timestamp;
            $should_archive = $message_timestamp < $now_timestamp - $this->archiveAfterSeconds;
            if ($should_archive) {
                $mail->move($folder_path = $this->archive_mailbox);
            }
        }
    }

    protected function deleteOldArchivedMails(MessageCollection $archived_mails): void {
        $this->log()->info("Removing old archived E-Mails...");
        $this->deleteMailsOlderThan($archived_mails, $this->deleteArchivedAfterSeconds);
    }

    protected function deleteOldSpamMails(MessageCollection $spam_mails): void {
        $this->log()->info("Removing old spam E-Mails...");
        $this->deleteMailsOlderThan($spam_mails, $this->deleteSpamAfterSeconds);
    }

    protected function deleteMailsOlderThan(MessageCollection $mails, int $seconds): void {
        $now_timestamp = strtotime($this->dateUtils()->getIsoNow());
        foreach ($mails as $mail) {
            $message_timestamp = $mail->date->first()->timestamp;
            $should_delete = $message_timestamp < $now_timestamp - $seconds;
            if ($should_delete) {
                $mail->delete($expunge = true);
            }
        }
    }

    /** @return array<int|string, bool> */
    protected function getIsMessageIdProcessed(MessageCollection $processed_mails): array {
        $is_message_id_processed = [];
        foreach ($processed_mails as $mail) {
            $message_id = $mail->message_id ? $mail->message_id->first() : null;
            if ($message_id !== null) {
                $is_message_id_processed[$message_id] = true;
            }
        }
        return $is_message_id_processed;
    }

    protected function processMail(Message $mail, bool $is_processed): int {
        $mail_uid = $mail->getUid();

        if ($mail->getFlags()->has('flagged')) {
            $this->log()->warning("E-Mail {$mail_uid} has failed processing.");
            $mail->move($folder_path = $this->failed_mailbox);
            $mail->unsetFlag('seen');
            $this->sendReportEmail($mail, null, 431);
            return 2;
        }

        $original_to = $mail->get('x_original_to');
        if ($original_to) {
            return $this->processMailToAddress($mail, $original_to);
        }
        if ($is_processed) {
            $this->log()->info("E-Mail {$mail_uid} already processed.");
            return 2;
        }
        $to_addresses = array_map(function ($address) {
            return $address->mail;
        }, $mail->getTo()->toArray());
        $cc_addresses = array_map(function ($address) {
            return $address->mail;
        }, $mail->getCc()->toArray());
        $bcc_addresses = array_map(function ($address) {
            return $address->mail;
        }, $mail->getBcc()->toArray());
        $all_addresses = [
            ...$to_addresses,
            ...$cc_addresses,
            ...$bcc_addresses,
        ];
        $all_successful = 2;
        foreach ($all_addresses as $address) {
            $all_successful = min($all_successful, $this->processMailToAddress($mail, $address));
        }
        return $all_successful;
    }

    protected function processMailToAddress(Message $mail, string $address): int {
        $mail_uid = $mail->getUid();

        $esc_host = preg_quote($this->host);
        $is_match = preg_match("/^([\\S]+)@(staging\\.)?{$esc_host}$/", $address, $matches);
        if (!$is_match) {
            $this->log()->info("E-Mail {$mail_uid} to non-{$this->host} address: {$address}");
            return 2;
        }
        $username = $matches[1];
        if ($this->emailUtils()->isSpamEmailAddress($username)) {
            $this->log()->info("Honeypot Spam E-Mail to: {$username}");
            return 1;
        }

        $role_repo = $this->entityManager()->getRepository(Role::class);
        $role = $role_repo->findFuzzilyByUsername($username);
        if (!$role) {
            $role = $role_repo->findFuzzilyByOldUsername($username);
            if ($role) {
                $this->sendRedirectEmail($mail, $address, "{$role->getUsername()}@{$this->host}");
            }
        }
        if ($role != null) {
            $has_role_email_permission = $this->authUtils()->hasRolePermission('role_email', $role);
            if (!$has_role_email_permission) {
                $this->log()->warning("E-Mail {$mail_uid} to role with no role_email permission: {$username}");
                $this->sendReportEmail($mail, $address, 550);
                return 2;
            }
            $role_users = $role->getUsers();
            $all_successful = 2;
            foreach ($role_users as $role_user) {
                $all_successful = min($all_successful, $this->forwardEmailToUser($mail, $role_user, $address));
            }
            return $all_successful;
        }

        $user_repo = $this->entityManager()->getRepository(User::class);
        $user = $user_repo->findFuzzilyByUsername($username);
        if (!$user) {
            $user = $user_repo->findFuzzilyByOldUsername($username);
            if ($user) {
                $this->sendRedirectEmail($mail, $address, "{$user->getUsername()}@{$this->host}");
            }
        }
        if ($user != null) {
            $has_user_email_permission = $this->authUtils()->hasPermission('user_email', $user);
            if (!$has_user_email_permission) {
                $this->log()->notice("E-Mail {$mail_uid} to user with no user_email permission: {$username}");
                $this->sendReportEmail($mail, $address, 550);
                return 2;
            }
            return $this->forwardEmailToUser($mail, $user, $address);
        }
        $this->log()->info("E-Mail {$mail_uid} to inexistent user/role username: {$username}");
        $this->sendReportEmail($mail, $address, 550);
        return 2;
    }

    protected function forwardEmailToUser(Message $mail, User $user, string $address): int {
        $mail->setFlag('flagged');

        $forward_address = $user->getEmail();
        try {
            $from = $mail->getFrom()->first();
            $from_name = $from->personal;
            $from_address = $from->mail;
            $to = $this->getAddresses($mail->getTo());
            $cc = $this->getAddresses($mail->getCc());
            $bcc = $this->getAddresses($mail->getBcc());
            $subject = $mail->getSubject()->first();
            $mail->parseBody();
            $html = $mail->hasHTMLBody() ? $mail->getHTMLBody() : null;
            $text = $mail->hasTextBody() ? $mail->getTextBody() : null;
            if (!$html) {
                $html = nl2br($text);
            }
            $this->emailUtils()->setLogger($this->log());

            $email = (new Email())
                ->from(new Address($from_address, $from_name))
                ->replyTo(new Address($from_address, $from_name))
                ->to(...$to)
                ->cc(...$cc)
                ->bcc(...$bcc)
                ->subject($subject)
                ->text($text ? $text : '(leer)')
                ->html($html ? $html : '(leer)')
            ;

            if ($mail->hasAttachments()) {
                $attachments = $mail->getAttachments();
                $data_path = $this->envUtils()->getDataPath();
                $temp_path = "{$data_path}temp/";
                if (!is_dir($temp_path)) {
                    mkdir($temp_path, 0o777, true);
                }
                foreach ($attachments as $attachment_id => $attachment) {
                    gc_collect_cycles();
                    $upload_id = '';
                    $upload_path = '';
                    $continue = true;
                    for ($i = 0; $i < self::MAX_LOOP && $continue; $i++) {
                        try {
                            $ext = strrchr($attachment->name, '.');
                            $upload_id = $this->uploadUtils()->getRandomUploadId($ext);
                        } catch (\Throwable $th) {
                            $upload_id = $this->uploadUtils()->getRandomUploadId('.data');
                        }

                        $upload_path = "{$temp_path}{$upload_id}";
                        if (!is_file($upload_path)) {
                            $continue = false;
                        }
                    }
                    $this->log()->info("Saving attachment {$attachment->name} to {$upload_id}...");
                    if ($attachment->save($temp_path, $upload_id)) {
                        $email = $email->addPart(new DataPart(new File($upload_path), $attachment->name));
                    } else {
                        throw new \Exception("Could not save attachment {$attachment->name} to {$upload_id}.");
                    }
                    gc_collect_cycles();
                }
            }

            $default_envelope = Envelope::create($email);
            $sender = $default_envelope->getSender();
            $envelope = new Envelope($sender, [$this->emailUtils()->getUserAddress($user)]);

            $this->mailer->send($email, $envelope);

            $this->log()->info("Email forwarded from {$address} to {$forward_address}");

            $mail->unsetFlag('flagged');
            return 2;
        } catch (RfcComplianceException $exc) {
            $message = $exc->getMessage();
            $this->log()->notice("Email from {$address} to {$forward_address} is not RFC-compliant: {$message}", [$exc]);
            return 2;
        } catch (TransportExceptionInterface $exc) {
            $message = $exc->getMessage();
            $this->log()->error("Error sending email (from {$address}) to {$forward_address}: {$message}", [$exc]);
            return 0;
        } catch (\Exception $exc) {
            $message = $exc->getMessage();
            $this->log()->critical("Error forwarding email from {$address} to {$forward_address}: {$message}", [$exc]);
            return 0;
        }
    }

    /** @return array<Address> */
    protected function getAddresses(Attribute $field): array {
        $addresses = [];
        foreach ($field->toArray() as $item) {
            if (!empty($item->mail)) {
                $addresses[] = $this->getAddress($item);
            }
        }
        return $addresses;
    }

    protected function getAddress(\Webklex\PHPIMAP\Address $item): Address {
        if ($item->personal) {
            return new Address($item->mail, $item->personal);
        }
        return new Address($item->mail);
    }

    protected function sendRedirectEmail(Message $mail, string $old_address, string $new_address): void {
        $smtp_from = $this->envUtils()->getSmtpFrom();
        $from = $mail->getFrom()->first();
        $from_name = $from->personal;
        $from_address = $from->mail;
        if ("{$old_address}" === "{$smtp_from}" || "{$old_address}" === "{$from_address}" || "{$new_address}" === "{$smtp_from}" || "{$new_address}" === "{$from_address}") {
            $this->log()->notice("sendRedirectEmail: Avoiding email loop for redirect {$old_address} => {$new_address}");
            return;
        }

        try {
            $email = (new Email())
                ->from(new Address($smtp_from, 'OLZ Bot'))
                ->to(new Address($from_address, $from_name))
                ->subject("Empfänger hat eine neue E-Mail-Adresse")
                ->text(<<<ZZZZZZZZZZ
                    Hallo {$from_name} ({$from_address}),

                    Dies ist eine Mitteilung der E-Mail-Weiterleitung:
                    Die E-Mail-Adresse "{$old_address}" ist neu unter "{$new_address}" erreichbar.

                    Dies nur zur Information. Ihre E-Mail wurde automatisch weitergeleitet!
                    ZZZZZZZZZZ)
            ;
            $this->mailer->send($email);
        } catch (RfcComplianceException $exc) {
            $message = $exc->getMessage();
            $this->log()->notice("sendRedirectEmail: Email to {$from_address} is not RFC-compliant: {$message}", [$exc]);
            return;
        } catch (\Throwable $th) {
            $this->log()->error("Failed to send redirect email to {$from_address}: {$th->getMessage()}", [$th]);
        }
    }

    protected function sendReportEmail(Message $mail, ?string $address, int $smtp_code): void {
        $smtp_from = $this->envUtils()->getSmtpFrom();
        $from = $mail->getFrom()->first();
        $from_name = $from->personal;
        $from_address = $from->mail;
        if ("{$address}" === "{$smtp_from}" || "{$address}" === "{$from_address}") {
            $this->log()->notice("sendReportEmail: Avoiding email loop for {$address}");
            return;
        }

        try {
            $email = (new Email())
                ->from(new Address($smtp_from, 'OLZ Bot'))
                ->to(new Address($from_address, $from_name))
                ->subject("Undelivered Mail Returned to Sender")
                ->text($this->getReportMessage($smtp_code, $mail, $address))
            ;
            $this->mailer->send($email);
        } catch (RfcComplianceException $exc) {
            $message = $exc->getMessage();
            $this->log()->notice("sendReportEmail: Email to {$from_address} is not RFC-compliant: {$message}", [$exc]);
            return;
        } catch (\Throwable $th) {
            $this->log()->error("Failed to send report email to {$from_address}: {$th->getMessage()}", [$th]);
        }
    }

    public function getReportMessage(int $smtp_code, Message $mail, ?string $address): string {
        $message_by_code = [
            431 => "{$smtp_code} Not enough storage or out of memory",
            550 => "<{$address}>: {$smtp_code} sorry, no mailbox here by that name",
        ];

        $error_message = $message_by_code[$smtp_code] ?? "{$smtp_code} Unknown error";

        $report_message = <<<ZZZZZZZZZZ
            This is the mail system at host {$this->host}.

            I'm sorry to have to inform you that your message could not
            be delivered to one or more recipients.

                            The mail system

            {$error_message}
            ZZZZZZZZZZ;

        if ($smtp_code === 550) {
            $all_attributes = '';
            foreach ($mail->getAttributes() as $key => $value) {
                $padded_name = str_pad($key, 18, ' ', STR_PAD_LEFT);
                $all_attributes .= "{$padded_name}: {$value}\n";
            }

            $mail->parseBody();
            $body = '(no body)';
            if ($mail->hasHTMLBody()) {
                $body = $mail->getHTMLBody();
            } elseif ($mail->hasTextBody()) {
                $body = $mail->getTextBody();
            }

            return <<<ZZZZZZZZZZ
                {$report_message}

                ------ This is a copy of the message, including all the headers. ------

                {$all_attributes}

                {$body}
                ZZZZZZZZZZ;
        }

        return $report_message;
    }
}
