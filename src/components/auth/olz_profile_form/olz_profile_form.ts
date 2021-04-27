
export function olzProfileFormOnUsernameFocus(form: HTMLFormElement): void {
    const firstName = form['first-name'].value;
    const lastName = form['last-name'].value;
    if (form.username.value !== '' || !firstName || !lastName) {
        return;
    }
    const usernameSuggestion = `${firstName} ${lastName}`.toLowerCase()
        .replace('ä', 'ae').replace('ö', 'oe').replace('ü', 'ue')
        .replace(' ', '.').replace(/[^a-z0-9.-]/, '');
    form.username.value = usernameSuggestion;
}
