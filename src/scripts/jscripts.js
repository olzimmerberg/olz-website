export function olz_toggle_vorstand(ident) {
    var pelem = document.getElementById("popup"+ident);
    var selem = document.getElementById("source"+ident);
     if (pelem.style.display=="none") {
         pelem.style.display = "block";
         pelem.style.marginTop = selem.offsetHeight+"px";
     } else {
         pelem.style.display = "none";
     }
 }


export function trim(stringToTrim) {
     return stringToTrim.replace(/^\s+|\s+$/g,"");
}


/* EMAILADRESSE MASKIEREN (global)*/
export function MailTo(name, domain, text, subject) {
    var mytext = "";
    var linktext = text;
    var email1 = name;
    var email2 = domain;
    var email3 = subject;
    mytext = ("<a href=\"" + "mail" + "to:" + email1 + "@" + email2 + "?subject=" + email3 + "\" class=\"linkmail\">" + linktext + "</a>");
    return mytext;
}

/* MENÜ UNTERMENÜS ZEIGEN (menu.php)*/
export function menu(menuid) {
    var div;
    div = document.getElementById(menuid);
    (div.style.display=="none") ?
    div.style.display="block" :
    div.style.display="none";
}

/* JAHREÜBERSICHT ZEIGEN (menu.php)*/
export function show_year(year1,year2) {
    var div;
    div1 = document.getElementById(year1);
    (div1.style.display=="none") ?
    div1.style.display="block" :
    div1.style.display="none";
    div2 = document.getElementById(year2);
    (div2.style.display=="none") ?
    div2.style.display="block" :
    div2.style.display="none";
}

/* DOPPELBILD (global)*/

export function expand(menuid) {
	var div = document.getElementById(menuid);
	div.style.display="block";
};
export function collapse(menuid) {
	var div = document.getElementById(menuid);
	div.style.display="none";
};

export function open_link(adresse) {
    var url = adresse;
    window.open(url,'_newtab');
}

export function open_link2(adresse) {
    var url = adresse;
    window.open(url,'');
}
