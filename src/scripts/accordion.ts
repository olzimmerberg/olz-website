let OpeningHeight = 0;
let ClosingHeight = 0;
const TimeToSlide = 200;
let openAccordion = '';

export function setOpenAccordion(newOpenAccordion: string): void {
    openAccordion = newOpenAccordion;
}

export function init_accordion(): void {
    const links = document.getElementsByName('accordionlink');
    for (let i = 0; i < links.length; i++) {
        links[i].removeAttribute('href');
    }
}

export function runAccordion(jahr: string): void {
    let nID = `Accordion${jahr}Content`;
    const openElem = document.getElementById(`${openAccordion}_`);
    if (openElem) { ClosingHeight = openElem.offsetHeight; }
    OpeningHeight = document.getElementById(`${nID}_`).offsetHeight;
    setTimeout(animateAccordion.bind(null, new Date().getTime(), TimeToSlide, openAccordion, nID), 33);

    if (openAccordion === nID) { nID = ''; }
    openAccordion = nID;
}

export function animateAccordion(
    lastTick: number,
    timeLeftArg: number,
    closingId: string,
    openingId: string,
): void {
    let timeLeft = timeLeftArg;
    const curTick = new Date().getTime();
    const elapsedTicks = curTick - lastTick;

    const opening = (openingId === '') ? null : document.getElementById(openingId);
    const closing = (closingId === '') ? null : document.getElementById(closingId);


    if (timeLeft <= elapsedTicks) {
        if (opening !== null) { opening.style.height = 'auto'; }
        if (closing !== null) {
            // closing.style.display = 'none';
            closing.style.height = '1px';
        }
        return;
    }

    timeLeft -= elapsedTicks;
    const newOpeningHeight = OpeningHeight - Math.round((timeLeft / TimeToSlide) * OpeningHeight);
    const newClosingHeight = Math.round((timeLeft / TimeToSlide) * ClosingHeight);

    if (opening !== null) {
        // if(opening.style.display != 'block') opening.style.display = 'block';
        opening.style.height = `${newOpeningHeight}px`;
    }

    if (closing !== null) { closing.style.height = `${newClosingHeight}px`; }

    setTimeout(animateAccordion.bind(null, curTick, timeLeft, closingId, openingId), 33);
}
