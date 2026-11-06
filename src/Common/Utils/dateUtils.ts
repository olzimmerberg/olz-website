export function toISO(dateTime: Date): string {
    const utcDate = toUTC(dateTime);
    const datePart = utcDate.toISOString().substring(0, 10);
    const timePart = utcDate.toISOString().substring(11, 19);
    return `${datePart} ${timePart}`;
}

export function toUTC(dateTime: Date): Date {
    return new Date(dateTime.getTime() - dateTime.getTimezoneOffset() * 60 * 1000);
}
