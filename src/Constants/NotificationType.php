<?php

namespace Olz\Constants;

enum NotificationType: string {
    case DAILY_SUMMARY = 'daily_summary';
    case DEADLINE_WARNING = 'deadline_warning';
    case EMAIL_CONFIG_REMINDER = 'email_config_reminder';
    case IMMEDIATE = 'immediate';
    case MONTHLY_PREVIEW = 'monthly_preview';
    case ROLE_REMINDER = 'role_reminder';
    case TELEGRAM_CONFIG_REMINDER = 'telegram_config_reminder';
    case TERMIN_NOTIFICATION = 'termin_notification';
    case WEEKLY_PREVIEW = 'weekly_preview';
    case WEEKLY_SUMMARY = 'weekly_summary';
}
