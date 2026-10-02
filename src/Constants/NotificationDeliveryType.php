<?php

namespace Olz\Constants;

enum NotificationDeliveryType: string {
    case EMAIL = 'email';
    case TELEGRAM = 'telegram';
}
