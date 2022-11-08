<?php

namespace Olz\Tasks;

use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\Tasks\Common\BackgroundTask;
use PhpImap\Exceptions\ConnectionException;

class ProcessEmailTask extends BackgroundTask {
    public const MAX_LOOP = 100;

    protected static function getIdent() {
        return "ProcessEmail";
    }

    protected function runSpecificTask() {
        $mailbox = $this->emailUtils()->getImapMailbox();
        try {
            $mailbox->createMailbox('Processed');
        } catch (\Exception $exc) {
            // ignore
        }
        $mailbox->setAttachmentsIgnore(false);

        // TODO: Test coverage!
        $mailbox->switchMailbox('INBOX.Processed');
        try {
            $mail_ids = $mailbox->searchMailbox('ALL');
        } catch (\UnexpectedValueException $uve) {
            $this->log()->critical("UnexpectedValueException in searchMailbox.", [$uve]);
            return;
        } catch (ConnectionException $exc) {
            $this->log()->critical("Could not search IMAP mailbox.", [$exc]);
            return;
        } catch (\Exception $exc) {
            $this->log()->critical("Exception in searchMailbox.", [$exc]);
            return;
        }
        $mails_headers = $mailbox->getMailsInfo($mail_ids);
        $is_message_id_processed = [];
        foreach ($mails_headers as $mail_headers) {
            $message_id = $mail_headers->message_id;
            $is_message_id_processed[$message_id] = true;
        }

        $mailbox->switchMailbox('INBOX');
        try {
            $mail_ids = $mailbox->searchMailbox('ALL');
        } catch (\UnexpectedValueException $uve) {
            $this->log()->critical("UnexpectedValueException in searchMailbox.", [$uve]);
            return;
        } catch (ConnectionException $exc) {
            $this->log()->critical("Could not search IMAP mailbox.", [$exc]);
            return;
        } catch (\Exception $exc) {
            $this->log()->critical("Exception in searchMailbox.", [$exc]);
            return;
        }

        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);

        $mails_headers = $mailbox->getMailsInfo($mail_ids);
        $processed_mails_headers = [];
        foreach ($mails_headers as $mail_headers) {
            $mail_uid = $mail_headers->uid;
            $message_id = $mail_headers->message_id;
            if ($is_message_id_processed[$message_id] ?? false) {
                $this->log()->info("E-Mail {$mail_uid} already processed.");
                continue;
            }
            $mail = $mailbox->getMail($mail_uid, /* do not mark as seen */ false);

            $to_addresses = array_keys($mail->to);
            foreach ($to_addresses as $to_address) {
                $is_match = preg_match('/^([\S]+)@(test\.)?olzimmerberg\.ch$/', $to_address, $matches);
                if (!$is_match) {
                    $processed_mails_headers[] = $mail_headers;
                    $this->log()->info("E-Mail {$mail_uid} to non-olzimmerberg.ch address: {$to_address}");
                    continue;
                }
                $username = $matches[1];
                $user = $user_repo->findFuzzilyByUsername($username);
                if (!$user) {
                    $user = $user_repo->findFuzzilyByOldUsername($username);
                }
                $role = $role_repo->findFuzzilyByUsername($username);
                if (!$role) {
                    $role = $role_repo->findFuzzilyByOldUsername($username);
                }
                if ($user != null) {
                    $has_user_email_permission = $this->authUtils()->hasPermission('user_email', $user);
                    if (!$has_user_email_permission) {
                        $this->log()->info("E-Mail {$mail_uid} to user with no user_email permission: {$username}");
                        $processed_mails_headers[] = $mail_headers;
                        continue;
                    }
                    $was_successful = $this->forwardEmailToUser($mail, $user, $to_address);
                    if ($was_successful) {
                        $processed_mails_headers[] = $mail_headers;
                    }
                }
                if ($role != null) {
                    $has_role_email_permission = $this->authUtils()->hasRolePermission('role_email', $role);
                    if (!$has_role_email_permission) {
                        $this->log()->info("E-Mail {$mail_uid} to role with no role_email permission: {$username}");
                        $processed_mails_headers[] = $mail_headers;
                        continue;
                    }
                    $role_users = $role->getUsers();
                    $was_successful = true;
                    foreach ($role_users as $role_user) {
                        if (!$this->forwardEmailToUser($mail, $role_user, $to_address)) {
                            $was_successful = false;
                        }
                    }
                    if ($was_successful) {
                        $processed_mails_headers[] = $mail_headers;
                    }
                }
                if ($user == null && $role == null) {
                    $this->log()->info("E-Mail {$mail_uid} to inexistent user/role username: {$username}");
                    $processed_mails_headers[] = $mail_headers;
                    continue;
                }
            }
            $is_message_id_processed[$message_id] = true;
        }

        foreach ($processed_mails_headers as $mail_headers) {
            $mail_uid = $mail_headers->uid;
            $mailbox->moveMail("{$mail_uid}", 'INBOX.Processed');
            // $mailbox->deleteMail($mail_uid);
        }
        $mailbox->expungeDeletedMails();
    }

    protected function forwardEmailToUser($mail, $user, $to_address): bool {
        $forward_address = $user->getEmail();
        $subject = $mail->subject;
        $text = $mail->textPlain;
        try {
            $this->emailUtils()->setLogger($this->log());
            $email = $this->emailUtils()->createEmail();
            $email->configure($user, $subject, $text, [
                'no_header' => true,
                'no_unsubscribe' => true,
            ]);
            // This is probably dangerous (Might get us on spamming lists?):
            // $email->setFrom($mail->fromAddress, $mail->fromName);
            $email->addReplyTo($mail->fromAddress, $mail->fromName);

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
                        $ext = strrchr($attachment->name, '.');
                        $upload_id = $this->uploadUtils()->getRandomUploadId($ext);
                        $upload_path = "{$temp_path}{$upload_id}";
                        if (!is_file($upload_path)) {
                            $continue = false;
                        }
                    }
                    $this->log()->info("Saving attachment {$attachment->name} to {$upload_id}...");
                    $attachment->setFilePath($upload_path);
                    if ($attachment->saveToDisk()) {
                        $email->addAttachment($upload_path, $attachment->name);
                    } else {
                        $this->log()->error("Could not save attachment {$attachment->name} to {$upload_id}.");
                    }
                    $upload_paths[] = $upload_path;
                }
            }

            $email->send();
            $this->log()->info("Email forwarded from {$to_address} to {$forward_address}");

            foreach ($upload_paths as $upload_path) {
                if (is_file($upload_path)) {
                    unlink($upload_path);
                }
            }
            return true;
        } catch (\Exception $exc) {
            $message = $exc->getMessage();
            $this->log()->critical("Error forwarding email from {$to_address} to {$forward_address}: {$message}");
            return false;
        }
    }
}
