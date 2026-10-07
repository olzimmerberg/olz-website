<?php

namespace Olz\Command\Notifications\Common;

use Olz\Constants\NotificationType;
use Olz\Entity\Users\User;

/**
 * @phpstan-type NotificationConfig array{
 *   notification_type?: NotificationType,
 *   recipient_user_ids?: array<int, true>,
 * }
 */
class Notification {
    public string $title;
    public string $text;
    /** @var NotificationConfig */
    public array $config;

    /** @param NotificationConfig $config */
    public function __construct(string $title, string $text, array $config = []) {
        $this->title = $title;
        $this->text = $text;
        $this->config = $config;
    }

    public function getTextForUser(User $user): string {
        $placeholders = [
            '%%userFirstName%%',
            '%%userLastName%%',
            '%%userUsername%%',
            '%%userEmail%%',
        ];
        $replacements = [
            $user->getFirstName(),
            $user->getLastName(),
            $user->getUsername(),
            $user->getEmail() ?? '',
        ];
        return str_replace($placeholders, $replacements, $this->text);
    }
}
