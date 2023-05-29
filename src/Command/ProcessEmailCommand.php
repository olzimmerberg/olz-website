<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Entity\Role;
use Olz\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webklex\PHPIMAP\Exceptions\ImapServerErrorException;
use Webklex\PHPIMAP\Exceptions\ResponseException;

#[AsCommand(name: 'olz:process-email')]
class ProcessEmailCommand extends OlzCommand {
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    public const MAX_LOOP = 100;
    public $deleteAfterSeconds = 30 * 24 * 60 * 60;

    protected $client;

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $this->client = $this->emailUtils()->getImapClient();
        $this->client->connect();
        try {
            $this->client->createFolder('INBOX.Processed');
        } catch (ImapServerErrorException $exc) {
            // ignore when folder already exists
        }
        try {
            $this->client->createFolder('INBOX.Failed');
        } catch (ImapServerErrorException $exc) {
            // ignore when folder already exists
        }

        // TODO: Test coverage!
        $processed_mails = $this->getProcessedMails();
        $this->deleteOldProcessedMails($processed_mails);
        $is_message_id_processed = $this->getIsMessageIdProcessed($processed_mails);

        $inbox_mails = $this->getInboxMails();
        $newly_processed_mails = [];
        foreach ($inbox_mails as $mail) {
            $message_id = $mail->message_id ? $mail->message_id->first() : null;
            $is_processed = ($is_message_id_processed[$message_id] ?? false);
            $is_newly_processed = $this->processMail($mail, $is_processed);
            if ($is_newly_processed) {
                $newly_processed_mails[] = $mail;
            }
            if ($message_id !== null) {
                $is_message_id_processed[$message_id] = true;
            }
        }

        foreach ($newly_processed_mails as $mail) {
            $mail->move($folder_path = 'INBOX.Processed');
        }

        return Command::SUCCESS;
    }

    protected function getProcessedMails() {
        return $this->getMails('INBOX.Processed');
    }

    protected function getInboxMails() {
        return $this->getMails('INBOX');
    }

    protected function getMails($folder_path) {
        try {
            $folder = $this->client->getFolderByPath($folder_path);
            $query = $folder->messages();
            $query->leaveUnread();
            $query->setFetchBody(false);
            return $query->all()->get();
        } catch (ResponseException $exc) {
            if (!preg_match('/Empty response/i', $exc->getMessage())) {
                $this->log()->critical("ResponseException in getInboxMails.", [$exc]);
                throw $exc;
            }
            return [];
        } catch (\Exception $exc) {
            $this->log()->critical("Exception in getInboxMails.", [$exc]);
            throw $exc;
        }
    }

    protected function deleteOldProcessedMails($processed_mails) {
        $now_timestamp = strtotime($this->dateUtils()->getIsoNow());
        foreach ($processed_mails as $mail) {
            $message_timestamp = $mail->date->first()->timestamp;
            $should_delete = $message_timestamp < $now_timestamp - $this->deleteAfterSeconds;
            if ($should_delete) {
                $mail->delete($expunge = true);
            }
        }
    }

    protected function getIsMessageIdProcessed($processed_mails) {
        $is_message_id_processed = [];
        foreach ($processed_mails as $mail) {
            $message_id = $mail->message_id ? $mail->message_id->first() : null;
            if ($message_id !== null) {
                $is_message_id_processed[$message_id] = true;
            }
        }
        return $is_message_id_processed;
    }

    protected function processMail($mail, $is_processed): bool {
        $mail_uid = $mail->uid;

        if ($mail->getFlags()->has('flagged')) {
            $this->log()->warning("E-Mail {$mail_uid} has failed processing.");
            $mail->move($folder_path = 'INBOX.Failed');
            return true;
        }

        $original_to = $mail->x_original_to;
        if ($original_to) {
            return $this->processMailToAddress($mail, $original_to);
        }
        if ($is_processed) {
            $this->log()->info("E-Mail {$mail_uid} already processed.");
            return true;
        }
        $all_successful = true;
        $to_addresses = array_map(function ($address) {
            return $address->mail;
        }, $mail->to->toArray());
        $cc_addresses = array_map(function ($address) {
            return $address->mail;
        }, $mail->cc->toArray());
        $bcc_addresses = array_map(function ($address) {
            return $address->mail;
        }, $mail->bcc->toArray());
        $all_addresses = [
            ...$to_addresses,
            ...$cc_addresses,
            ...$bcc_addresses,
        ];
        foreach ($all_addresses as $address) {
            if (!$this->processMailToAddress($mail, $address)) {
                $all_successful = false;
            }
        }
        return $all_successful;
    }

    protected function processMailToAddress($mail, $address): bool {
        $mail_uid = $mail->uid;

        $is_match = preg_match('/^([\S]+)@(staging\.)?olzimmerberg\.ch$/', $address, $matches);
        if (!$is_match) {
            $this->log()->info("E-Mail {$mail_uid} to non-olzimmerberg.ch address: {$address}");
            return true;
        }
        $username = $matches[1];

        $role_repo = $this->entityManager()->getRepository(Role::class);
        $role = $role_repo->findFuzzilyByUsername($username);
        if (!$role) {
            $role = $role_repo->findFuzzilyByOldUsername($username);
        }
        if ($role != null) {
            $has_role_email_permission = $this->authUtils()->hasRolePermission('role_email', $role);
            if (!$has_role_email_permission) {
                $this->log()->warning("E-Mail {$mail_uid} to role with no role_email permission: {$username}");
                $this->sendBounceEmail($mail, $address);
                return true;
            }
            $role_users = $role->getUsers();
            $all_successful = true;
            foreach ($role_users as $role_user) {
                if (!$this->forwardEmailToUser($mail, $role_user, $address)) {
                    $all_successful = false;
                }
            }
            return $all_successful;
        }

        $user_repo = $this->entityManager()->getRepository(User::class);
        $user = $user_repo->findFuzzilyByUsername($username);
        if (!$user) {
            $user = $user_repo->findFuzzilyByOldUsername($username);
        }
        if ($user != null) {
            $has_user_email_permission = $this->authUtils()->hasPermission('user_email', $user);
            if (!$has_user_email_permission) {
                $this->log()->warning("E-Mail {$mail_uid} to user with no user_email permission: {$username}");
                $this->sendBounceEmail($mail, $address);
                return true;
            }
            return $this->forwardEmailToUser($mail, $user, $address);
        }
        $this->log()->info("E-Mail {$mail_uid} to inexistent user/role username: {$username}");
        $this->sendBounceEmail($mail, $address);
        return true;
    }

    protected function forwardEmailToUser($mail, $user, $address): bool {
        $mail->setFlag('flagged');

        $forward_address = $user->getEmail();
        $from = $mail->from->first();
        $from_name = $from->personal;
        $from_address = $from->mail;
        $to = $this->getMailList($mail->to->toArray());
        $cc = $this->getMailList($mail->cc->toArray());
        $bcc = $this->getMailList($mail->bcc->toArray());
        $subject = $mail->subject->first();
        $mail->parseBody();
        $html = $mail->hasHTMLBody() ? $mail->getHTMLBody() : null;
        $text = $mail->hasTextBody() ? $mail->getTextBody() : null;
        if (!$html) {
            $html = nl2br($text);
        }
        try {
            $this->emailUtils()->setLogger($this->log());
            $email = $this->emailUtils()->createEmail();
            $email->configure($user, $subject, /* text= */ '', [
                'no_header' => true,
                'no_unsubscribe' => true,
            ]);

            $email->Sender = $this->envUtils()->getSmtpFrom();
            $email->setFrom($from_address, $from_name, false);
            $email->addReplyTo($from_address, $from_name);

            if ($to) {
                $email->addCustomHeader('To', $to);
            }
            if ($cc) {
                $email->addCustomHeader('Cc', $cc);
            }
            if ($bcc) {
                $email->addCustomHeader('Bcc', $bcc);
            }

            $email->Body = $html ? $html : '(leer)';
            $email->AltBody = $text ? $text : '(leer)';

            $upload_paths = [];
            if ($mail->hasAttachments()) {
                ini_set('memory_limit', '500M');
                $attachments = $mail->getAttachments();
                $data_path = $this->envUtils()->getDataPath();
                $temp_path = "{$data_path}temp/";
                if (!is_dir($temp_path)) {
                    mkdir($temp_path, 0777, true);
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
                        $email->addAttachment($upload_path, $attachment->name);
                    } else {
                        throw new \Exception("Could not save attachment {$attachment->name} to {$upload_id}.");
                    }
                    $upload_paths[] = $upload_path;
                    gc_collect_cycles();
                }
            }

            $email->send();
            $this->log()->info("Email forwarded from {$address} to {$forward_address}");

            foreach ($upload_paths as $upload_path) {
                if (is_file($upload_path)) {
                    unlink($upload_path);
                }
            }

            $mail->unsetFlag('flagged');
            return true;
        } catch (\Exception $exc) {
            $message = $exc->getMessage();
            $this->log()->critical("Error forwarding email from {$address} to {$forward_address}: {$message}");
            return false;
        }
    }

    protected function getMailList(array $list): string {
        return array_reduce(
            $list,
            function ($carry, $item) {
                $carry_comma = $carry ? "{$carry}, " : $carry;
                if ($item->personal) {
                    return "{$carry_comma}{$item->personal} <{$item->mail}>";
                }
                return "{$carry_comma}{$item->mail}";
            },
            '',
        );
    }

    protected function sendBounceEmail($mail, $address) {
        $smtp_from = $this->envUtils()->getSmtpFrom();
        $from = $mail->from->first();
        $from_name = $from->personal;
        $from_address = $from->mail;
        if ("{$address}" === "{$smtp_from}" || "{$address}" === "{$from_address}") {
            $this->log()->notice("sendBounceEmail: Avoiding email loop for {$address}");
            return;
        }

        $this->emailUtils()->setLogger($this->log());
        $email = $this->emailUtils()->createEmail();

        $email->Sender = '';
        $email->setFrom($smtp_from, 'OLZ Bot', false);
        $email->addAddress($from_address, $from_name);
        $email->isHTML(false);
        $email->Subject = "Undelivered Mail Returned to Sender";
        $email->Body = $this->getBounceMessage($mail, $address);
        $email->send();
    }

    public function getBounceMessage($mail, $address) {
        $base_href = $this->envUtils()->getBaseHref();

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
        This is the mail system at host {$base_href}.

        I'm sorry to have to inform you that your message could not
        be delivered to one or more recipients. It's attached below.

                        The mail system

        <{$address}>: 550 sorry, no mailbox here by that name

        ------ This is a copy of the message, including all the headers. ------

        {$all_attributes}

        {$body}
        ZZZZZZZZZZ;
    }
}
