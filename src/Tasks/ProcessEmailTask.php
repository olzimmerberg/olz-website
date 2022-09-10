<?php

namespace Olz\Tasks;

use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\Tasks\Common\BackgroundTask;
use PhpImap\Exceptions\ConnectionException;

class ProcessEmailTask extends BackgroundTask {
    public function __construct($entityManager, $authUtils, $emailUtils, $dateUtils, $envUtils) {
        parent::__construct($dateUtils, $envUtils);
        $this->entityManager = $entityManager;
        $this->authUtils = $authUtils;
        $this->emailUtils = $emailUtils;
    }

    protected static function getIdent() {
        return "ProcessEmail";
    }

    protected function runSpecificTask() {
        $mailbox = $this->emailUtils->getImapMailbox();
        $mailbox->setAttachmentsIgnore(true);

        try {
            $mail_ids = $mailbox->searchMailbox('ALL');
        } catch (\UnexpectedValueException $uve) {
            $this->logger->critical("UnexpectedValueException in searchMailbox.", [$uve]);
            return;
        } catch (ConnectionException $exc) {
            $this->logger->critical("Could not search IMAP mailbox.", [$exc]);
            return;
        } catch (\Exception $exc) {
            $this->logger->critical("Exception in searchMailbox.", [$exc]);
            return;
        }

        $user_repo = $this->entityManager->getRepository(User::class);
        $role_repo = $this->entityManager->getRepository(Role::class);

        foreach ($mail_ids as $mail_id) {
            $mail = $mailbox->getMail($mail_id, /* do not mark as seen */ false);

            $to_addresses = array_keys($mail->to);
            foreach ($to_addresses as $to_address) {
                $is_match = preg_match('/^([\S]+)@olzimmerberg\.ch$/', $to_address, $matches);
                if (!$is_match) {
                    $this->logger->info("E-Mail to non-olzimmerberg.ch address: {$to_address}");
                    continue;
                }
                $username = $matches[1];
                $user = $user_repo->findFuzzilyByUsername($username);
                $role = $role_repo->findFuzzilyByUsername($username);
                if ($user != null) {
                    $has_user_email_permission = $this->authUtils->hasPermission('user_email', $user);
                    if (!$has_user_email_permission) {
                        $this->logger->info("E-Mail to user with no user_email permission: {$username}");
                        continue;
                    }
                    $this->forwardEmailToUser($mail, $user, $to_address);
                }
                if ($role != null) {
                    $has_role_email_permission = $this->authUtils->hasRolePermission('role_email', $role);
                    if (!$has_role_email_permission) {
                        $this->logger->info("E-Mail to role with no role_email permission: {$username}");
                        continue;
                    }
                    $role_users = $role->getUsers();
                    foreach ($role_users as $role_user) {
                        $this->forwardEmailToUser($mail, $role_user, $to_address);
                    }
                }
                if ($user == null && $role == null) {
                    $this->logger->info("E-Mail to inexistent user/role username: {$username}");
                    continue;
                }
            }
        }

        foreach ($mail_ids as $mail_id) {
            $mailbox->deleteMail($mail_id);
        }
        $mailbox->expungeDeletedMails();
    }

    protected function forwardEmailToUser($mail, $user, $to_address) {
        $forward_address = $user->getEmail();
        $subject = $mail->subject;
        $text = $mail->textPlain;
        try {
            $this->emailUtils->setLogger($this->logger);
            $email = $this->emailUtils->createEmail();
            $email->configure($user, $subject, $text, [
                'no_header' => true,
                'no_unsubscribe' => true,
            ]);
            // This is probably dangerous (Might get us on spamming lists?):
            // $email->setFrom($mail->fromAddress, $mail->fromName);
            $email->addReplyTo($mail->fromAddress, $mail->fromName);
            $email->send();
            $this->logger->info("Email forwarded from {$to_address} to {$forward_address}");
        } catch (\Exception $exc) {
            $message = $exc->getMessage();
            $this->logger->critical("Error forwarding email from {$to_address} to {$forward_address}: {$message}");
        }
    }
}
