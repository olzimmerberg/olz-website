import $ from 'jquery';

import {olzApi} from '../../../../../src/Api/client';

import './OlzEmailCard.scss';

export function olzEmailNotificationsUpdate(form: Record<string, {value?: string, checked?: boolean}>): boolean {
    const monthlyPreview = form['monthly-preview'].checked ?? false;
    const weeklyPreview = form['weekly-preview'].checked ?? false;
    const deadlineWarning = form['deadline-warning'].checked ?? false;
    const daysString = form['deadline-warning-days'].value;
    const deadlineWarningDays: '1'|'2'|'3'|'7' = (daysString === '1' || daysString === '2' || daysString === '3' || daysString === '7') ? daysString : '3';
    const dailySummary = form['daily-summary'].checked ?? false;
    const dailySummaryAktuell = form['daily-summary-aktuell'].checked ?? false;
    const dailySummaryBlog = form['daily-summary-blog'].checked ?? false;
    const dailySummaryForum = form['daily-summary-forum'].checked ?? false;
    const dailySummaryTermine = form['daily-summary-termine'].checked ?? false;
    const weeklySummary = form['weekly-summary'].checked ?? false;
    const weeklySummaryAktuell = form['weekly-summary-aktuell'].checked ?? false;
    const weeklySummaryBlog = form['weekly-summary-blog'].checked ?? false;
    const weeklySummaryForum = form['weekly-summary-forum'].checked ?? false;
    const weeklySummaryTermine = form['weekly-summary-termine'].checked ?? false;

    olzApi.call(
        'updateNotificationSubscriptions',
        {deliveryType: 'email', monthlyPreview, weeklyPreview, deadlineWarning, deadlineWarningDays, dailySummary, dailySummaryAktuell, dailySummaryBlog, dailySummaryForum, dailySummaryTermine, weeklySummary, weeklySummaryAktuell, weeklySummaryBlog, weeklySummaryForum, weeklySummaryTermine},
    )
        .then((response) => {
            if (response.status === 'OK') {
                $('#email-notifications-success-message').text('Benachrichtigungen erfolgreich aktualisiert.');
                $('#email-notifications-error-message').text('');
            } else {
                $('#email-notifications-success-message').text('');
                $('#email-notifications-error-message').text('Fehler bei der Änderung der Benachrichtigungen.');
            }
        })
        .catch(() => {
            $('#email-notifications-success-message').text('');
            $('#email-notifications-error-message').text('Benachrichtigungen konnten nicht geändert werden.');
        });
    return false;
}
