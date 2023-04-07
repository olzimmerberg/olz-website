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

#[AsCommand(name: 'olz:processEmail')]
class ProcessEmailCommand extends OlzCommand {
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

        $is_match = preg_match('/^([\S]+)@(test\.)?olzimmerberg\.ch$/', $address, $matches);
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
                return true;
            }
            return $this->forwardEmailToUser($mail, $user, $address);
        }
        $this->log()->info("E-Mail {$mail_uid} to inexistent user/role username: {$username}");
        return true;
    }

    protected function forwardEmailToUser($mail, $user, $address): bool {
        $forward_address = $user->getEmail();
        $from = $mail->from->first();
        $from_name = $from->personal;
        $from_address = $from->mail;
        $from_label = $from_name ? "{$from_name} <{$from_address}>" : "{$from_address}";
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
            // This is probably dangerous (Might get us on spamming lists?):
            // $email->setFrom($from_address, $from_name);
            $email->setFrom($this->envUtils()->getSmtpFrom(), "{$from_label} (via OLZ)");
            $email->addReplyTo($from_address, $from_name);

            $email->Body = $html ? $html : '(leer)';
            $email->AltBody = $text ? $text : '(leer)';

            $upload_paths = [];
            if ($mail->hasAttachments()) {
                $attachments = $mail->getAttachments();
                $data_path = $this->envUtils()->getDataPath();
                $temp_path = "{$data_path}temp/";
                if (!is_dir($temp_path)) {
                    mkdir($temp_path, 0777, true);
                }
                foreach ($attachments as $attachment_id => $attachment) {
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
                        $this->log()->error("Could not save attachment {$attachment->name} to {$upload_id}.");
                    }
                    $upload_paths[] = $upload_path;
                }
            }

            $email->send();
            $this->log()->info("Email forwarded from {$address} to {$forward_address}");

            foreach ($upload_paths as $upload_path) {
                if (is_file($upload_path)) {
                    unlink($upload_path);
                }
            }
            return true;
        } catch (\Exception $exc) {
            $message = $exc->getMessage();
            $this->log()->critical("Error forwarding email from {$address} to {$forward_address}: {$message}");
            return false;
        }
    }
}