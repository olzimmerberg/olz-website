export function olzJomCounterToggle(ident: string): boolean {
    const elem = document.getElementById(ident);
    const kidsPreviousElem = document.getElementById('ranking-kids-previous');
    const kidsCurrentElem = document.getElementById('ranking-kids-current');
    const jundsPreviousElem = document.getElementById('ranking-junds-previous');
    const jundsCurrentElem = document.getElementById('ranking-junds-current');
    if (!elem || !kidsPreviousElem || !kidsCurrentElem || !jundsPreviousElem || !jundsCurrentElem) {
        return false;
    }
    const isShown = (elem.style.display === 'block');
    kidsPreviousElem.style.display = 'none';
    kidsCurrentElem.style.display = 'none';
    jundsPreviousElem.style.display = 'none';
    jundsCurrentElem.style.display = 'none';
    elem.style.display = (isShown ? 'none' : 'block');
    return false;
}
