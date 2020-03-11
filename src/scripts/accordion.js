var OpeningHeight = 0;
var ClosingHeight = 0;
var TimeToSlide = 200;
var openAccordion='';

export function init_accordion() {
    var links = document.getElementsByName("accordionlink");
    for (var i=0; i<links.length; i++) {
        links[i].removeAttribute("href");
    }
}

export function runAccordion(jahr)
{
    var nID = "Accordion" + jahr+ "Content";
    var openElem = document.getElementById(openAccordion+"_");
    if (openElem) ClosingHeight = openElem.offsetHeight;
    OpeningHeight = document.getElementById(nID+"_").offsetHeight;
    setTimeout("animateAccordion(" + new Date().getTime() + "," + TimeToSlide + ",'" + openAccordion + "','" + nID + "')", 33);

    if(openAccordion == nID) nID = '';
    openAccordion = nID;
}

export function animateAccordion(lastTick, timeLeft, closingId, openingId)
{
    var curTick = new Date().getTime();
    var elapsedTicks = curTick - lastTick;

    var opening = (openingId == '') ? null : document.getElementById(openingId);
    var closing = (closingId == '') ? null : document.getElementById(closingId);


    if(timeLeft <= elapsedTicks)
    {
        if(opening != null) opening.style.height = 'auto';
        if(closing != null)
        {
            //closing.style.display = 'none';
            closing.style.height = '1px';
        }
        return;
    }

    timeLeft -= elapsedTicks;
    var newOpeningHeight = OpeningHeight-Math.round((timeLeft/TimeToSlide) * OpeningHeight);
    var newClosingHeight = Math.round((timeLeft/TimeToSlide) * ClosingHeight);

    if(opening != null)
    {
        //if(opening.style.display != 'block') opening.style.display = 'block';
        opening.style.height = newOpeningHeight + 'px';
    }

    if(closing != null) closing.style.height = newClosingHeight + 'px';

    setTimeout("animateAccordion(" + curTick + "," + timeLeft + ",'" + closingId + "','" + openingId + "')", 33);
}
