export function olzNotificationSubscriptionsFormOnChange(elem: HTMLInputElement): void {
    if (elem.name === 'daily-summary') {
        elem.form['daily-summary-aktuell'].checked = elem.checked;
        elem.form['daily-summary-blog'].checked = elem.checked;
        elem.form['daily-summary-forum'].checked = elem.checked;
        elem.form['daily-summary-galerie'].checked = elem.checked;
    }
    if (elem.name === 'weekly-summary') {
        elem.form['weekly-summary-aktuell'].checked = elem.checked;
        elem.form['weekly-summary-blog'].checked = elem.checked;
        elem.form['weekly-summary-forum'].checked = elem.checked;
        elem.form['weekly-summary-galerie'].checked = elem.checked;
    }
    const dailySummaryButNoContent = (
        elem.form['daily-summary'].checked
        && !elem.form['daily-summary-aktuell'].checked
        && !elem.form['daily-summary-blog'].checked
        && !elem.form['daily-summary-forum'].checked
        && !elem.form['daily-summary-galerie'].checked
    );
    const weeklySummaryButNoContent = (
        elem.form['weekly-summary'].checked
        && !elem.form['weekly-summary-aktuell'].checked
        && !elem.form['weekly-summary-blog'].checked
        && !elem.form['weekly-summary-forum'].checked
        && !elem.form['weekly-summary-galerie'].checked
    );
    if (dailySummaryButNoContent) {
        $('#olz-notification-subscriptions-form-daily-summary-warn-message').text('Tageszusammenfassung, aber kein Inhalt angewählt!');
    } else {
        $('#olz-notification-subscriptions-form-daily-summary-warn-message').text('');
    }
    if (weeklySummaryButNoContent) {
        $('#olz-notification-subscriptions-form-weekly-summary-warn-message').text('Wochenzusammenfassung, aber kein Inhalt angewählt!');
    } else {
        $('#olz-notification-subscriptions-form-weekly-summary-warn-message').text('');
    }
}
