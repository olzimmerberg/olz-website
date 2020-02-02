/* ADMIN FUNKTIONEN */
/* DATUM 1 ÜBERNEHMEN */
function End_angleichen() {
window.document.Formularl.terminedatum_end.value=window.document.Formularl.terminedatum.value;}

/* DATUM 2 ÜBERNEHMEN */
function Off_angleichen() {
window.document.Formularl.terminedatum_off.value=window.document.Formularl.terminedatum_end.value;}

/* WEITERE LAEUFERIN */
function ZusLaeufer() {
window.document.Formularl.anmeldunginfo1.value=window.document.Formularl.anmeldunginfo1.value + "\n[Name, Vorname],[Wohnort],[Kategorie],[Jahrgang],[BadgeNr],[Etappen(1-6/1-3/4-6)]";
}

/* TERMINTYP ANFÜGEN */
function Typ_angleichen() {
	var thisForm = window.document.Formularl;
	if (thisForm.set_typ.options[thisForm.set_typ.selectedIndex].value != "") {
		thisForm.terminetyp.value = trim(thisForm.terminetyp.value + " " + thisForm.set_typ.options[thisForm.set_typ.selectedIndex].value);
	thisForm.set_typ.options.selectedIndex = 0;
	}}

/* TERMINTITEL EINFÜGEN */
function Titel_angleichen() {
	var thisForm = window.document.Formularl;
	if (thisForm.set_titel.options[thisForm.set_titel.selectedIndex].value != "") {
		thisForm.terminetitel.value = thisForm.terminetitel.value + thisForm.set_titel.options[thisForm.set_titel.selectedIndex].value;
		thisForm.terminetitel.focus()
	}}

function Resultatlink() {
	var thisForm = window.document.Formularl;
	var jetzt = new Date();
	var Jahr = jetzt.getFullYear();
	if (thisForm.set_resultat.options[thisForm.set_resultat.selectedIndex].text != "") {
		thisForm.terminelink.value = thisForm.terminelink.value + "<div class=\"linkext\"><a href=\"http://www.o-l.ch/cgi-bin/abfrage?type=rang&year=" + Jahr + "&event=" + thisForm.set_resultat.options[thisForm.set_resultat.selectedIndex].text + "&kat=&kind=club&club=zimmerberg\" target=\"_blank\">Resultate</a></div>";
		thisForm.terminelink.focus()
	}}


function Linkhilfe() {
var thisForm = window.document.Formularl;
switch (thisForm.set_link.options[thisForm.set_link.selectedIndex].value) {
	case "1" :		
		thisForm.terminelink.value = thisForm.terminelink.value + "<a href=\"" + thisForm.help_set_link.value + "\" target=\"_blank\" class=\"linkext\">Ausschreibung</a>\n";
		break ;
	case "2" :
		thisForm.terminelink.value = thisForm.terminelink.value + "<a href=\"" + thisForm.help_set_link.value + "\" target=\"_blank\" class=\"linkext\">GO2OL</a>\n";
		break ;
	case "3" :
		var jahr = thisForm.terminedatum.value.substring(0,4);
		var monat = thisForm.terminedatum.value.substring(5,7);
		var tag = thisForm.terminedatum.value.substring(8,10);
		thisForm.terminelink.value = thisForm.terminelink.value + "<a href=\"http://fahrplan.sbb.ch/bin/query.exe/dn?externalCall=yes&REQ0JourneyStopsZID=A=1$O=" + thisForm.help_set_link.value + "$L=0&REQ0JourneyDate=" + tag + "." + monat + "." + jahr + "&REQ0HafasSearchForw=0&REQ0JourneyTime=12:00\" target=\"_blank\" class=\"linkoev\">Fahrplan</a>\n";
		break ;
	case "4" :
		thisForm.terminelink.value = thisForm.terminelink.value + "<script type=\"text/javascript\">document.write(MailTo(\"vorAt\", \"nachAt\", \"Bezeichnung\", \"Betreff\"));</script>\n";
		break ;
	case "5" :
		thisForm.terminelink.value = thisForm.terminelink.value + "<a href=\"" + thisForm.help_set_link.value + "\" class=\"linkint\"></a>\n";
		break ;
	case "6" :
		thisForm.terminelink.value = thisForm.terminelink.value + "<a href=\"" + thisForm.help_set_link.value + "\" target=\"_blank\" class=\"linkext\"></a>\n";
		break ;
	case "7" :
		thisForm.terminelink.value = thisForm.terminelink.value + "<a href=\"" + thisForm.help_set_link.value + "\" target=\"_blank\" class=\"linkpdf\"></a>";
		break ;
	case "8" :		
		thisForm.terminelink.value = thisForm.terminelink.value + "<a href=\"" + thisForm.help_set_link.value + "\" target=\"_blank\" class=\"linkext\">Anmeldung</a>\n";
		break ;
	}
}

function koordinaten(){
	var thisForm = window.document.Formularl;
	var koord = thisForm.terminexkoord.value;
	koord = koord.trim();
	koord =  koord.replace(/[^0-9/ ]/g, '');
	x = koord.substring(0,6);
	y = koord.substring(koord.length-6,koord.length);
	if(x==y) y = thisForm.termineykoord.value;
	if(y>x){x_=x;x=y;y=x_};
	thisForm.terminexkoord.value = x;
	thisForm.termineykoord.value = y;
}