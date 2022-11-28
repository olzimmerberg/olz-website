<?php

namespace Olz\Tasks;

use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\Tasks\Common\BackgroundTask;
use PhpImap\Exceptions\ConnectionException;

class ProcessEmailTask extends BackgroundTask {
    public const MAX_LOOP = 100;
    public $deleteAfterSeconds = 30 * 24 * 60 * 60;

    protected static function getIdent() {
        return "ProcessEmail";
    }

    protected function runSpecificTask() {
        $this->mailbox = $this->emailUtils()->getImapMailbox();
        try {
            $this->mailbox->createMailbox('Processed');
        } catch (\Exception $exc) {
            // ignore
        }
        $this->mailbox->setAttachmentsIgnore(false);

        // TODO: Test coverage!
        $processed_mails_headers = $this->getProcessedMailsHeaders();
        $this->deleteOldProcessedMails($processed_mails_headers);
        $is_message_id_processed = $this->getIsMessageIdProcessed($processed_mails_headers);

        $inbox_mails_headers = $this->getInboxMailsHeaders();
        $newly_processed_mails_headers = [];
        foreach ($inbox_mails_headers as $mail_headers) {
            $message_id = $mail_headers->message_id ?? null;
            $is_processed = ($is_message_id_processed[$message_id] ?? false);
            $is_newly_processed = $this->processMail($mail_headers, $is_processed);
            if ($is_newly_processed) {
                $newly_processed_mails_headers[] = $mail_headers;
            }
            if ($message_id !== null) {
                $is_message_id_processed[$message_id] = true;
            }
        }

        foreach ($newly_processed_mails_headers as $mail_headers) {
            $mail_uid = $mail_headers->uid;
            $this->mailbox->moveMail("{$mail_uid}", 'INBOX.Processed');
        }
    }

    protected function getProcessedMailsHeaders() {
        $this->mailbox->switchMailbox('INBOX.Processed');
        try {
            $mail_ids = $this->mailbox->searchMailbox('ALL');
        } catch (\UnexpectedValueException $uve) {
            $this->log()->critical("UnexpectedValueException in searchMailbox.", [$uve]);
            throw $uve;
        } catch (ConnectionException $exc) {
            $this->log()->critical("Could not search IMAP mailbox.", [$exc]);
            throw $exc;
        } catch (\Exception $exc) {
            $this->log()->critical("Exception in searchMailbox.", [$exc]);
            throw $exc;
        }
        return count($mail_ids) > 0 ? $this->mailbox->getMailsInfo($mail_ids) : [];
    }

    protected function getInboxMailsHeaders() {
        $this->mailbox->switchMailbox('INBOX');
        try {
            $mail_ids = $this->mailbox->searchMailbox('ALL');
        } catch (\UnexpectedValueException $uve) {
            $this->log()->critical("UnexpectedValueException in searchMailbox.", [$uve]);
            throw $uve;
        } catch (ConnectionException $exc) {
            $this->log()->critical("Could not search IMAP mailbox.", [$exc]);
            throw $exc;
        } catch (\Exception $exc) {
            $this->log()->critical("Exception in searchMailbox.", [$exc]);
            throw $exc;
        }
        return count($mail_ids) > 0 ? $this->mailbox->getMailsInfo($mail_ids) : [];
    }

    protected function deleteOldProcessedMails($processed_mails_headers) {
        $now_timestamp = strtotime($this->dateUtils()->getIsoNow());
        foreach ($processed_mails_headers as $mail_headers) {
            $message_timestamp = strtotime($mail_headers->date);
            $should_delete = $message_timestamp < $now_timestamp - $this->deleteAfterSeconds;
            if ($should_delete) {
                $mail_uid = $mail_headers->uid;
                $this->mailbox->deleteMail($mail_uid);
            }
        }
        $this->mailbox->expungeDeletedMails();
    }

    protected function getIsMessageIdProcessed($processed_mails_headers) {
        $is_message_id_processed = [];
        foreach ($processed_mails_headers as $mail_headers) {
            $message_id = $mail_headers->message_id ?? null;
            if ($message_id !== null) {
                $is_message_id_processed[$message_id] = true;
            }
        }
        return $is_message_id_processed;
    }

    protected function processMail($mail_headers, $is_processed): bool {
        $mail_uid = $mail_headers->uid;
        $mail = $this->mailbox->getMail($mail_uid, /* do not mark as seen */ false);

        $original_to = $mail->xOriginalTo;
        if ($original_to) {
            return $this->processMailToAddress($mail, $original_to);
        }
        if ($is_processed) {
            $this->log()->info("E-Mail {$mail_uid} already processed.");
            return true;
        }
        $to_addresses = array_keys($mail->to);
        $all_successful = true;
        foreach ($to_addresses as $to_address) {
            if (!$this->processMailToAddress($mail, $to_address)) {
                $all_successful = false;
            }
        }
        return $all_successful;
    }

    protected function processMailToAddress($mail, $address): bool {
        $mail_uid = $mail->id;

        $is_match = preg_match('/^([\S]+)@(test\.)?olzimmerberg\.ch$/', $address, $matches);
        if (!$is_match) {
            $this->log()->info("E-Mail {$mail_uid} to non-olzimmerberg.ch address: {$address}");
            return true;
        }
        $username = $matches[1];

        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);

        $user = $user_repo->findFuzzilyByUsername($username);
        if (!$user) {
            $user = $user_repo->findFuzzilyByOldUsername($username);
        }
        if ($user != null) {
            $has_user_email_permission = $this->authUtils()->hasPermission('user_email', $user);
            if (!$has_user_email_permission) {
                $this->log()->info("E-Mail {$mail_uid} to user with no user_email permission: {$username}");
                return true;
            }
            return $this->forwardEmailToUser($mail, $user, $address);
        }
        $role = $role_repo->findFuzzilyByUsername($username);
        if (!$role) {
            $role = $role_repo->findFuzzilyByOldUsername($username);
        }
        if ($role != null) {
            $has_role_email_permission = $this->authUtils()->hasRolePermission('role_email', $role);
            if (!$has_role_email_permission) {
                $this->log()->info("E-Mail {$mail_uid} to role with no role_email permission: {$username}");
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
        $this->log()->info("E-Mail {$mail_uid} to inexistent user/role username: {$username}");
        return true;
    }

    protected function forwardEmailToUser($mail, $user, $address): bool {
        $forward_address = $user->getEmail();
        $subject = $mail->subject;
        $html = $mail->textHtml;
        $text = $mail->textPlain;
        if (!$html) {
            $html = $text;
        }
        try {
            $this->emailUtils()->setLogger($this->log());
            $email = $this->emailUtils()->createEmail();
            $email->configure($user, $subject, /* text= */ '', [
                'no_header' => true,
                'no_unsubscribe' => true,
            ]);
            // This is probably dangerous (Might get us on spamming lists?):
            // $email->setFrom($mail->fromAddress, $mail->fromName);
            $email->setFrom($this->envUtils()->getSmtpFrom(), 'OLZ E-Mail Weiterleitung');
            $email->addReplyTo($mail->fromAddress, $mail->fromName);

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
