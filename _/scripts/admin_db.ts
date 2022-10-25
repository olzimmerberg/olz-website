import {trim} from './jscripts';

/* ADMIN FUNKTIONEN */
/* DATUM 1 ÜBERNEHMEN */
export function End_angleichen(): void {
    const thisForm = getFormularl();
    if (!thisForm) {
        return;
    }
    thisForm.terminedatum_end.value = thisForm.terminedatum.value;
}

/* DATUM 2 ÜBERNEHMEN */
export function Off_angleichen(): void {
    const thisForm = getFormularl();
    if (!thisForm) {
        return;
    }
    thisForm.terminedatum_off.value = thisForm.terminedatum_end.value;
}

/* WEITERE LAEUFERIN */
export function ZusLaeufer(): void {
    const thisForm = getFormularl();
    if (!thisForm) {
        return;
    }
    thisForm.anmeldunginfo1.value = `${thisForm.anmeldunginfo1.value}\n[Name, Vorname],[Wohnort],[Kategorie],[Jahrgang],[BadgeNr],[Etappen(1-6/1-3/4-6)]`;
}

/* TERMINTYP ANFÜGEN */
export function Typ_angleichen(): void {
    const thisForm = getFormularl();
    if (!thisForm) {
        return;
    }
    if (thisForm.set_typ.options[thisForm.set_typ.selectedIndex].value !== '') {
        thisForm.terminetyp.value = trim(`${thisForm.terminetyp.value} ${thisForm.set_typ.options[thisForm.set_typ.selectedIndex].value}`);
        thisForm.set_typ.options.selectedIndex = 0;
    }
}

/* TERMINTITEL EINFÜGEN */
export function Titel_angleichen(): void {
    const thisForm = getFormularl();
    if (!thisForm) {
        return;
    }
    if (thisForm.set_titel.options[thisForm.set_titel.selectedIndex].value !== '') {
        thisForm.terminetitel.value = thisForm.terminetitel.value + thisForm.set_titel.options[thisForm.set_titel.selectedIndex].value;
        thisForm.terminetitel.focus();
    }
}

export function Resultatlink(): void {
    const thisForm = getFormularl();
    if (!thisForm) {
        return;
    }
    const jetzt = new Date();
    const Jahr = jetzt.getFullYear();
    if (thisForm.set_resultat.options[thisForm.set_resultat.selectedIndex].text !== '') {
        thisForm.terminelink.value = `${thisForm.terminelink.value}<div class="linkext"><a href="http://www.o-l.ch/cgi-bin/abfrage?type=rang&year=${Jahr}&event=${thisForm.set_resultat.options[thisForm.set_resultat.selectedIndex].text}&kat=&kind=club&club=zimmerberg" target="_blank">Resultate</a></div>`;
        thisForm.terminelink.focus();
    }
}

export function Linkhilfe(): void {
    const thisForm = getFormularl();
    if (!thisForm) {
        return;
    }
    switch (thisForm.set_link.options[thisForm.set_link.selectedIndex].value) {
        case '1' :
            thisForm.terminelink.value = `${thisForm.terminelink.value}<a href="${thisForm.help_set_link.value}" target="_blank" class="linkext">Ausschreibung</a>\n`;
            break;
        case '2' :
            thisForm.terminelink.value = `${thisForm.terminelink.value}<a href="${thisForm.help_set_link.value}" target="_blank" class="linkext">GO2OL</a>\n`;
            break;
        case '3' : {
            const jahr = thisForm.terminedatum.value.substring(0, 4);
            const monat = thisForm.terminedatum.value.substring(5, 7);
            const tag = thisForm.terminedatum.value.substring(8, 10);
            thisForm.terminelink.value = `${thisForm.terminelink.value}<a href="http://fahrplan.sbb.ch/bin/query.exe/dn?externalCall=yes&REQ0JourneyStopsZID=A=1$O=${thisForm.help_set_link.value}$L=0&REQ0JourneyDate=${tag}.${monat}.${jahr}&REQ0HafasSearchForw=0&REQ0JourneyTime=12:00" target="_blank" class="linkoev">Fahrplan</a>\n`;
            break;
        }
        case '4' :
            thisForm.terminelink.value = `${thisForm.terminelink.value}<script type="text/javascript">olz.MailTo("vorAt", "nachAt", "Bezeichnung", "Betreff");</script>\n`;
            break;
        case '5' :
            thisForm.terminelink.value = `${thisForm.terminelink.value}<a href="${thisForm.help_set_link.value}" class="linkint"></a>\n`;
            break;
        case '6' :
            thisForm.terminelink.value = `${thisForm.terminelink.value}<a href="${thisForm.help_set_link.value}" target="_blank" class="linkext"></a>\n`;
            break;
        case '7' :
            thisForm.terminelink.value = `${thisForm.terminelink.value}<a href="${thisForm.help_set_link.value}" target="_blank" class="linkpdf"></a>`;
            break;
        case '8' :
            thisForm.terminelink.value = `${thisForm.terminelink.value}<a href="${thisForm.help_set_link.value}" target="_blank" class="linkext">Anmeldung</a>\n`;
            break;
        default: break;
    }
}

export function koordinaten(): void {
    const thisForm = getFormularl();
    if (!thisForm) {
        return;
    }
    let koord = thisForm.terminexkoord.value;
    koord = koord.trim();
    koord = koord.replace(/[^0-9/ ]/g, '');
    let x = koord.substring(0, 6);
    let y = koord.substring(koord.length - 6, koord.length);
    if (x === y) { y = thisForm.termineykoord.value; }
    if (y > x) { const x_ = x; x = y; y = x_; }
    thisForm.terminexkoord.value = x;
    thisForm.termineykoord.value = y;
}

function getFormularl(): HTMLFormElement|undefined {
    const doc = window.document as Document&{Formularl:HTMLFormElement|undefined};
    return doc.Formularl;
}
