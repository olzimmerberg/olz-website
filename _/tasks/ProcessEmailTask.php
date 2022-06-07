<?php

use Olz\Entity\User;
use PhpImap\Exceptions\ConnectionException;

require_once __DIR__.'/common/BackgroundTask.php';

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
            $this->logger->critical("UnexpectedValueException in searchMailbox", [$uve]);
            return;
        } catch (ConnectionException $exc) {
            $this->logger->critical("Could not search IMAP mailbox.", [$exc]);
            return;
        } catch (\Exception $exc) {
            $this->logger->critical("Exception in searchMailbox", [$exc]);
            return;
        }

        $user_repo = $this->entityManager->getRepository(User::class);

        foreach ($mail_ids as $mail_id) {
            $mail = $mailbox->getMail($mail_id, /* do not mark as seen */ false);

            $to_adresses = array_keys($mail->to);
            foreach ($to_adresses as $to_adress) {
                $is_match = preg_match('/^([\S]+)@olzimmerberg\.ch$/', $to_adress, $matches);
                if (!$is_match) {
                    $this->logger->info("E-Mail to non-olzimmerberg.ch address: {$to_adress}");
                    continue;
                }
                $username = $matches[1];
                $user = $user_repo->findFuzzilyByUsername($username);
                if ($user == null) {
                    $this->logger->info("E-Mail to inexistent username: {$username}");
                    continue;
                }
                $has_email_permission = $this->authUtils->hasPermission('email', $user);
                if (!$has_email_permission) {
                    $this->logger->info("E-Mail to username with no email permission: {$username}");
                    continue;
                }
                $forward_email = $user->getEmail();
                $subject = $mail->subject;
                $text = $mail->textPlain;
                try {
                    $this->emailUtils->setLogger($this->logger);
                    $email = $this->emailUtils->createEmail();
                    $email->configure($user, $subject, $text, [
                        'no_header' => true,
                        'no_unsubscribe' => true,
                    ]);
                    // This is probably dangerous (Might get us an spamming lists?):
                    // $email->setFrom($mail->fromAddress, $mail->fromName);
                    $email->addReplyTo($mail->fromAddress, $mail->fromName);
                    $email->send();
                    $this->logger->info("Email forwarded from {$to_adress} to {$forward_email}");
                } catch (\Exception $exc) {
                    $message = $exc->getMessage();
                    $this->logger->critical("Error forwarding email from {$to_adress} to {$forward_email}: {$message}");
                }
            }
        }

        foreach ($mail_ids as $mail_id) {
            $mailbox->deleteMail($mail_id);
        }
        $mailbox->expungeDeletedMails();
    }
}
