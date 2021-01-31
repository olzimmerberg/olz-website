<?php

class Notification {
    public function __construct($title, $text) {
        $this->title = $title;
        $this->text = $text;
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
