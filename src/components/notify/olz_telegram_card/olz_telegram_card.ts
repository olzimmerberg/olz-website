import {OlzApiEndpoint, callOlzApi} from '../../../api/client';

export function olzTelegramNotificationsUpdate(form: Record<string, {value?: string, checked?: boolean}>): boolean {
    const monthlyPreview = form['monthly-preview'].checked ?? false;
    const weeklyPreview = form['weekly-preview'].checked ?? false;
    const deadlineWarning = form['deadline-warning'].checked ?? false;
    const daysString = form['deadline-warning-days'].value;
    const deadlineWarningDays: '1'|'2'|'3'|'7' = (daysString === '1' || daysString === '2' || daysString === '3' || daysString === '7') ? daysString : '3';
    const dailySummary = form['daily-summary'].checked ?? false;
    const dailySummaryAktuell = form['daily-summary-aktuell'].checked ?? false;
    const dailySummaryBlog = form['daily-summary-blog'].checked ?? false;
    const dailySummaryForum = form['daily-summary-forum'].checked ?? false;
    const dailySummaryGalerie = form['daily-summary-galerie'].checked ?? false;
    const weeklySummary = form['weekly-summary'].checked ?? false;
    const weeklySummaryAktuell = form['weekly-summary-aktuell'].checked ?? false;
    const weeklySummaryBlog = form['weekly-summary-blog'].checked ?? false;
    const weeklySummaryForum = form['weekly-summary-forum'].checked ?? false;
    const weeklySummaryGalerie = form['weekly-summary-galerie'].checked ?? false;

    callOlzApi(
        OlzApiEndpoint.updateNotificationSubscriptions,
        {deliveryType: 'telegram', monthlyPreview, weeklyPreview, deadlineWarning, deadlineWarningDays, dailySummary, dailySummaryAktuell, dailySummaryBlog, dailySummaryForum, dailySummaryGalerie, weeklySummary, weeklySummaryAktuell, weeklySummaryBlog, weeklySummaryForum, weeklySummaryGalerie },
    )
        .then((response) => {
            if (response.status === 'OK') {
                $('#telegram-notifications-success-message').text('Benachrichtigungen erfolgreich aktualisiert.');
                $('#telegram-notifications-error-message').text('');
            } else {
                $('#telegram-notifications-success-message').text('');
                $('#telegram-notifications-error-message').text('Fehler bei der Änderung der Benachrichtigungen.');
            }
        })
        .catch(() => {
            $('#telegram-notifications-success-message').text('');
            $('#telegram-notifications-error-message').text('Benachrichtigungen konnten nicht geändert werden.');
        });
    return false;
}
