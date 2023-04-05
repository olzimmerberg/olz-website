<?php

namespace Olz\Command\SendDailyNotificationsCommand;

class Notification {
    public $title;
    public $text;
    public $config;

    public function __construct($title, $text, $config = []) {
        $this->title = $title;
        $this->text = $text;
        $this->config = $config;
    }

    public function getTextForUser($user) {
        $placeholders = [
            '%%userFirstName%%',
            '%%userLastName%%',
            '%%userEmail%%',
        ];
        $replacements = [
            $user->getFirstName(),
            $user->getLastName(),
            $user->getEmail(),
        ];
        return str_replace($placeholders, $replacements, $this->text);
    }
}
