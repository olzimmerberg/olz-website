import {OlzApiEndpoint, callOlzApi} from '../../../api/client';

export function olzTelegramNotificationsUpdate(form: Record<string, {value?: string, checked?: boolean}>): boolean {
    const monthlyPreview = form['monthly-preview'].checked;
    const weeklyPreview = form['weekly-preview'].checked;
    const deadlineWarning = form['deadline-warning'].checked;
    const daysString = form['deadline-warning-days'].value;
    const deadlineWarningDays: '1'|'2'|'3'|'7'|undefined = (daysString === '1' || daysString === '2' || daysString === '3' || daysString === '7') ? daysString : undefined;
    const dailySummary = form['daily-summary'].checked;
    const dailySummaryAktuell = form['daily-summary-aktuell'].checked;
    const dailySummaryBlog = form['daily-summary-blog'].checked;
    const dailySummaryForum = form['daily-summary-forum'].checked;
    const dailySummaryGalerie = form['daily-summary-galerie'].checked;
    const weeklySummary = form['weekly-summary'].checked;
    const weeklySummaryAktuell = form['weekly-summary-aktuell'].checked;
    const weeklySummaryBlog = form['weekly-summary-blog'].checked;
    const weeklySummaryForum = form['weekly-summary-forum'].checked;
    const weeklySummaryGalerie = form['weekly-summary-galerie'].checked;

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
