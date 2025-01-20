<?php

namespace Olz\Repository\Roles;

enum PredefinedRole: string {
    // The string value is the username.
    case FanOlzElite = 'fan-olz-elite';
    case Buessli = 'buessli';
    case Aktuariat = 'aktuariat';
    case Nachwuchs = 'nachwuchs-kontakt';
    case Sysadmin = 'website';
    case SportIdent = 'sportident';
}
