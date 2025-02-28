<?php

namespace Olz\Command\SendDailyNotificationsCommand;

use Olz\Entity\Users\User;

class Notification {
    public string $title;
    public string $text;
    /** @var array{notification_type?: string} */
    public array $config;

    /** @param array{notification_type?: string} $config */
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
