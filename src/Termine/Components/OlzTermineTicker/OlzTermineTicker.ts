export function olzTermineTickerMouseIn(id: string): void {
    const elem = document.getElementById(id);
    if (!elem) {
        return;
    }
    elem.style.color = 'rgb(0,0,0)';
}

export function olzTermineTickerMouseOut(id: string): void {
    const elem = document.getElementById(id);
    if (!elem) {
        return;
    }
    elem.style.color = 'rgb(0,110,25)';
}
