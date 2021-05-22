
export function olzProfileFormOnUsernameFocus(form: HTMLFormElement): void {
    const firstName = form['first-name'].value;
    const lastName = form['last-name'].value;
    if (form.username.value !== '' || !firstName || !lastName) {
        return;
    }
    const usernameSuggestion = getUsernameSuggestion(firstName, lastName);
    form.username.value = usernameSuggestion;
}

export function getUsernameSuggestion(firstName: string, lastName: string): string {
    return `${firstName} ${lastName}`.toLowerCase()
        .replace(/ä/g, 'ae').replace(/ö/g, 'oe').replace(/ü/g, 'ue')
        .replace(/ /g, '.').replace(/[^a-z0-9.-]/g, '');
}
