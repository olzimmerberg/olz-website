<?php

namespace Olz\Repository\Faq;

enum PredefinedQuestion: string {
    // The string value is the ident.
    case Telegram = 'weshalb_telegram_push';
    case RecoverUsernameEmail = 'benutzername_email_herausfinden';
    case ForumRules = 'forumsregeln';
}
