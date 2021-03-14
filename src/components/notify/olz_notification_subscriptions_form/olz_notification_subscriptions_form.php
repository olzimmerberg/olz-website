<?php

function olz_notification_subscriptions_form($subscriptions, $options = []) {
    $out = '';

    $daily_summary_checked = '';
    $deadline_warning_checked = '';
    $deadline_warning_1_selected = '';
    $deadline_warning_2_selected = '';
    $deadline_warning_3_selected = ' selected';
    $deadline_warning_7_selected = '';
    $monthly_preview_checked = '';
    $weekly_preview_checked = '';
    $weekly_summary_checked = '';
    $daily_summary_aktuell_checked = '';
    $daily_summary_blog_checked = '';
    $daily_summary_forum_checked = '';
    $daily_summary_galerie_checked = '';
    $weekly_summary_aktuell_checked = '';
    $weekly_summary_blog_checked = '';
    $weekly_summary_forum_checked = '';
    $weekly_summary_galerie_checked = '';
    foreach ($subscriptions as $subscription) {
        $notification_type = $subscription->getNotificationType();
        $args = json_decode($subscription->getNotificationTypeArgs(), true);
        switch ($notification_type) {
            case NotificationSubscription::TYPE_DAILY_SUMMARY:
                $daily_summary_checked = ' checked';
                if ($args['aktuell'] ?? false) {
                    $daily_summary_aktuell_checked = ' checked';
                }
                if ($args['blog'] ?? false) {
                    $daily_summary_blog_checked = ' checked';
                }
                if ($args['forum'] ?? false) {
                    $daily_summary_forum_checked = ' checked';
                }
                if ($args['galerie'] ?? false) {
                    $daily_summary_galerie_checked = ' checked';
                }
                break;
            case NotificationSubscription::TYPE_DEADLINE_WARNING:
                $deadline_warning_checked = ' checked';
                switch (intval($args['days'] ?? 3)) {
                    case 1:
                        $deadline_warning_1_selected = ' selected';
                        $deadline_warning_3_selected = '';
                        break;
                    case 2:
                        $deadline_warning_2_selected = ' selected';
                        $deadline_warning_3_selected = '';
                        break;
                    case 3:
                        $deadline_warning_3_selected = ' selected';
                        break;
                    case 7:
                        $deadline_warning_7_selected = ' selected';
                        $deadline_warning_3_selected = '';
                        break;
                    default:
                        break;
                }
                break;
            case NotificationSubscription::TYPE_MONTHLY_PREVIEW:
                $monthly_preview_checked = ' checked';
                break;
            case NotificationSubscription::TYPE_WEEKLY_PREVIEW:
                $weekly_preview_checked = ' checked';
                break;
            case NotificationSubscription::TYPE_WEEKLY_SUMMARY:
                $weekly_summary_checked = ' checked';
                if ($args['aktuell'] ?? false) {
                    $weekly_summary_aktuell_checked = ' checked';
                }
                if ($args['blog'] ?? false) {
                    $weekly_summary_blog_checked = ' checked';
                }
                if ($args['forum'] ?? false) {
                    $weekly_summary_forum_checked = ' checked';
                }
                if ($args['galerie'] ?? false) {
                    $weekly_summary_galerie_checked = ' checked';
                }
                break;
            default:
                break;
        }
    }

    $is_disabled = $options['disabled'] ?? false;
    $disabled_attribute = $is_disabled ? ' disabled' : '';

    $out .= <<<ZZZZZZZZZZ
        <p class='card-text'>
            <input type='checkbox' name='monthly-preview'{$monthly_preview_checked}{$disabled_attribute} />
            Monatsvorschau
        </p>
        <p class='card-text'>
            <input type='checkbox' name='weekly-preview'{$weekly_preview_checked}{$disabled_attribute} />
            Wochenvorschau
        </p>
        <p class='card-text'>
            <input type='checkbox' name='deadline-warning'{$deadline_warning_checked}{$disabled_attribute} />
            Meldeschluss-Warnung 
            <select name='deadline-warning-days'{$disabled_attribute}>
                <option{$deadline_warning_7_selected}>7</option>
                <option{$deadline_warning_3_selected}>3</option>
                <option{$deadline_warning_2_selected}>2</option>
                <option{$deadline_warning_1_selected}>1</option>
            </select>
            Tage vorher
        </p>
        <p class='card-text'>
            <input type='checkbox' name='daily-summary'{$daily_summary_checked}{$disabled_attribute} />
            Tageszusammenfassung
            <br />
            &nbsp;&nbsp;
            <input type='checkbox' name='daily-summary-aktuell'{$daily_summary_aktuell_checked}{$disabled_attribute} />
            Aktuell-Beiträge
            <br />
            &nbsp;&nbsp;
            <input type='checkbox' name='daily-summary-blog'{$daily_summary_blog_checked}{$disabled_attribute} />
            Kaderblog
            <br />
            &nbsp;&nbsp;
            <input type='checkbox' name='daily-summary-forum'{$daily_summary_forum_checked}{$disabled_attribute} />
            Forumseinträge
            <br />
            &nbsp;&nbsp;
            <input type='checkbox' name='daily-summary-galerie'{$daily_summary_galerie_checked}{$disabled_attribute} />
            Neue Galerien
        </p>
        <p class='card-text'>
            <input type='checkbox' name='weekly-summary'{$weekly_summary_checked}{$disabled_attribute} />
            Wochenzusammenfassung
            <br />
            &nbsp;&nbsp;
            <input type='checkbox' name='weekly-summary-aktuell'{$weekly_summary_aktuell_checked}{$disabled_attribute} />
            Aktuell-Beiträge
            <br />
            &nbsp;&nbsp;
            <input type='checkbox' name='weekly-summary-blog'{$weekly_summary_blog_checked}{$disabled_attribute} />
            Kaderblog
            <br />
            &nbsp;&nbsp;
            <input type='checkbox' name='weekly-summary-forum'{$weekly_summary_forum_checked}{$disabled_attribute} />
            Forumseinträge
            <br />
            &nbsp;&nbsp;
            <input type='checkbox' name='weekly-summary-galerie'{$weekly_summary_galerie_checked}{$disabled_attribute} />
            Neue Galerien
        </p>
    ZZZZZZZZZZ;

    return $out;
}
