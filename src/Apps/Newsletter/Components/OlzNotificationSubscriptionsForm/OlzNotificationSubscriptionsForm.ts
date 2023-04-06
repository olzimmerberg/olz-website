import $ from 'jquery';

import './OlzNotificationSubscriptionsForm.scss';

export function olzNotificationSubscriptionsFormOnChange(elem: HTMLInputElement): void {
    if (!elem.form) {
        return;
    }
    if (elem.name === 'daily-summary') {
        elem.form['daily-summary-aktuell'].checked = elem.checked;
        elem.form['daily-summary-blog'].checked = elem.checked;
        elem.form['daily-summary-forum'].checked = elem.checked;
        elem.form['daily-summary-galerie'].checked = elem.checked;
        elem.form['daily-summary-termine'].checked = elem.checked;
    }
    if (elem.name === 'weekly-summary') {
        elem.form['weekly-summary-aktuell'].checked = elem.checked;
        elem.form['weekly-summary-blog'].checked = elem.checked;
        elem.form['weekly-summary-forum'].checked = elem.checked;
        elem.form['weekly-summary-galerie'].checked = elem.checked;
        elem.form['weekly-summary-termine'].checked = elem.checked;
    }
    const dailySummaryButNoContent = (
        elem.form['daily-summary'].checked
        && !elem.form['daily-summary-aktuell'].checked
        && !elem.form['daily-summary-blog'].checked
        && !elem.form['daily-summary-forum'].checked
        && !elem.form['daily-summary-galerie'].checked
        && !elem.form['daily-summary-termine'].checked
    );
    const contentButNoDailySummary = (
        !elem.form['daily-summary'].checked
        && (
            elem.form['daily-summary-aktuell'].checked
            || elem.form['daily-summary-blog'].checked
            || elem.form['daily-summary-forum'].checked
            || elem.form['daily-summary-galerie'].checked
            || elem.form['daily-summary-termine'].checked
        )
    );
    const weeklySummaryButNoContent = (
        elem.form['weekly-summary'].checked
        && !elem.form['weekly-summary-aktuell'].checked
        && !elem.form['weekly-summary-blog'].checked
        && !elem.form['weekly-summary-forum'].checked
        && !elem.form['weekly-summary-galerie'].checked
        && !elem.form['weekly-summary-termine'].checked
    );
    const contentButNoWeeklySummary = (
        !elem.form['weekly-summary'].checked
        && (
            elem.form['weekly-summary-aktuell'].checked
            || elem.form['weekly-summary-blog'].checked
            || elem.form['weekly-summary-forum'].checked
            || elem.form['weekly-summary-galerie'].checked
            || elem.form['weekly-summary-termine'].checked
        )
    );
    const dailySummaryWarnMessage = elem.form.querySelector('#olz-notification-subscriptions-form-daily-summary-warn-message');
    if (dailySummaryWarnMessage) {
        if (dailySummaryButNoContent) {
            $(dailySummaryWarnMessage).text('Tageszusammenfassung, aber kein Inhalt angewählt!');
        } else if (contentButNoDailySummary) {
            $(dailySummaryWarnMessage).text('Tageszusammenfassung nicht angewählt!');
        } else {
            $(dailySummaryWarnMessage).text('');
        }
    }
    const weeklySummaryWarnMessage = elem.form.querySelector('#olz-notification-subscriptions-form-weekly-summary-warn-message');
    if (weeklySummaryWarnMessage) {
        if (weeklySummaryButNoContent) {
            $(weeklySummaryWarnMessage).text('Wochenzusammenfassung, aber kein Inhalt angewählt!');
        } else if (contentButNoWeeklySummary) {
            $(weeklySummaryWarnMessage).text('Wochenzusammenfassung nicht angewählt!');
        } else {
            $(weeklySummaryWarnMessage).text('');
        }
    }
}
