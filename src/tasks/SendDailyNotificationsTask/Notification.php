<?php

class Notification {
    public function __construct($title, $text, $config = []) {
        $this->title = $title;
        $this->text = $text;
        $this->config = $config;
    }

    public function getTextForUser($user) {
        $placeholders = [
            '%%userFirstName%%',
            '%%userLastName%%',
        ];
        $replacements = [
            $user->getFirstName(),
            $user->getLastName(),
        ];
        return str_replace($placeholders, $replacements, $this->text);
    }
}
