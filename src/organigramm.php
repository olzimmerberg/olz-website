<?php

$db_table = "vorstand";

$organigramm = array(
    array(
        "<h6>Anlässe,<br>Vizepräsi</h6>".olz_funktion_insert(2, 1),
        "<h6>5er- und Pfingststaffel</h6><div class='section'>".olz_funktion_insert(11, 0, "<br>")."</div>",
        "<h6>Weekends</h6><div class='section'>".olz_funktion_insert(51, 0, "<br>")."</div>",
        "<h6>Papiersammlung</h6><div class='section'><i>Langnau</i><br>".olz_funktion_insert(9, 0, "<br>")."</div><div class='section'><i>Thalwil</i><br>".olz_funktion_insert(10, 0, "<br>")."</div>",
        "<h6>Flohmarkt</h6><div class='section'>".olz_funktion_insert(48, 0, "<br>")."</div>",
    ),
    array(
        "<h6>Material<br>& Karten</h6>".olz_funktion_insert(8, 1),
        "<h6>Kartenteam</h6><div class='section'><i>Chef</i><br>".olz_funktion_insert(13, 0, "<br>")."</div><div class='section'><i>Mit dabei</i><br>".olz_funktion_insert(14, 0, "<br>")."</div>",
        "<h6 id='link-kartenverkauf'>Kartenverkauf</h6><div class='section'>".olz_funktion_insert(15, 0, "<br>")."</div>",
        "<h6 id='link-kleiderverkauf'>Kleiderverkauf</h6><div class='section'>".olz_funktion_insert(16, 0, "<br>")."</div>",
        "<h6>Material</h6><div class='section'><i>Lager Thalwil</i><br>".olz_funktion_insert(17, 0, "<br>")."</div><div class='section'><i>SportIdent</i><br>".olz_funktion_insert(50, 0, "<br>")."</div>",
        "<h6>OLZ-Büssli</h6><div class='section'>".olz_funktion_insert(12, 0, "<br>")."</div>",
    ),
    array(
        "<h6>Finanzen<br>&nbsp;</h6>".olz_funktion_insert(5, 1),
        "<h6>Revisoren</h6><div class='section'>".olz_funktion_insert(18, 0, "<br>")."</div><h6>Ersatzrevisor</h6><div class='section'>".olz_funktion_insert(19, 0, "<br>")."</div>",
    ),
    array(
        "<h6 id='link-praesident'>Präsident<br>&nbsp;</h6>".olz_funktion_insert(1, 1),
        "<h6>Sektionen</h6><div class='section'><i>Adliswil</i><br>".olz_funktion_insert(20, 0, "<br>")."</div><div class='section'><i>Horgen</i><br>".olz_funktion_insert(21, 0, "<br>")."</div><div class='section'><i>Langnau</i><br>".olz_funktion_insert(22, 0, "<br>")."</div><div class='section'><i>Richterswil</i><br>".olz_funktion_insert(23, 0, "<br>")."</div><div class='section'><i>Thalwil</i><br>".olz_funktion_insert(24, 0, "<br>")."</div><div class='section'><i>Wädenswil</i><br>".olz_funktion_insert(25, 0, "<br>")."</div>",
        "<h6>OL und Umwelt</h6><div class='section'>".olz_funktion_insert(26, 0, "<br>")."</div>",
        "<h6>Mira</h6><div class='section'>".olz_funktion_insert(27, 0, "<br>")."</div>",
    ),
    array(
        "<h6>&Ouml;ffentlich-<br>keitsarbeit</h6>".olz_funktion_insert(6, 1),
        "<h6>Presse</h6><div class='section'>".olz_funktion_insert(30, 0, "<br>")."</div>",
        "<h6>Homepage</h6><div class='section'>".olz_funktion_insert(29, 0, "<br>")."</div>",
        "<h6>Heftli \"HOLZ\"</h6><div class='section'>".olz_funktion_insert(31, 0, "<br>")."</div>",
    ),
    array(
        "<h6 id='link-mitgliederverwaltung'>Aktuariat<br>&nbsp;</h6>".olz_funktion_insert(4, 1),
        "<h6>Chronik & Archiv </h6><div class='section'>".olz_funktion_insert(28, 0, "<br>")."</div>",
    ),
    array(
        "<h6>Nachwuchs &<br>Ausbildung</h6>".olz_funktion_insert(3, 1),
        "<h6>J&S Coach</h6><div class='section'>".olz_funktion_insert(32, 0, "<br>")."</div>",
        //"<h6>J&S Expertin</h6><div class='section'>".olz_funktion_insert(33, 0, "<br>")."</div>",
        "<h6>J&S Leiter</h6><div class='section'>".olz_funktion_insert(34, 0, "<br>")."</div>",
        //"<h6>Regionale Nachwuchs Kontaktpersonen</h6><div class='section'><i>Adliswil</i><br>".olz_funktion_insert(35, 0, "<br>")."</div><div class='section'><i>Horgen</i><br>".olz_funktion_insert(36, 0, "<br>")."</div><div class='section'><i>Langnau</i><br>".olz_funktion_insert(37, 0, "<br>")."</div><div class='section'><i>Richterswil</i><br>".olz_funktion_insert(38, 0, "<br>")."</div><div class='section'><i>Schönenberg, Hirzel, Samstagern</i><br>".olz_funktion_insert(39, 0, "<br>")."</div><div class='section'><i>Thalwil</i><br>".olz_funktion_insert(40, 0, "<br>")."</div><div class='section'><i>Wädenswil</i><br>".olz_funktion_insert(41, 0, "<br>")."</div><div class='section'><i>Zürich</i><br>".olz_funktion_insert(42, 0, "<br>")."</div>",
        "<h6>J&S Kids</h6><div class='section'>".olz_funktion_insert(43, 0, "<br>")."</div>",
        "<h6>sCOOL</h6><div class='section'>".olz_funktion_insert(44, 0, "<br>")."</div>",
    ),
    array(
        "<h6>Nachwuchs &<br>Leistungssport</h6>".olz_funktion_insert(49, 1),
    	"<h6>Trainer Leistungssport</h6><div class='section'>".olz_funktion_insert(52, 0, "<br>")."</div>",
    	"<h6>Team Gold</h6><div class='section'><i>Hauptleitung</i><br>".olz_funktion_insert(53, 0, "<br>")."<br><i>Leiterteam</i><br>".olz_funktion_insert(54, 0, "<br>")."</div>",
    ),
    array(
        "<h6>Training &<br>Technik</h6>".olz_funktion_insert(7, 1),
        "<h6>Kartentraining</h6><div class='section'>".olz_funktion_insert(45, 0, "<br>")."</div>",
        "<h6>Hallentraining</h6><div class='section'>".olz_funktion_insert(46, 0, "<br>")."</div>",
        "<h6>Lauftraining</h6><div class='section'>".olz_funktion_insert(47, 0, "<br>")."</div>",
    ),
);

echo "<script type='text/javascript'>
var highlighttimer = false;
function highlight_organigramm(id) {
    highlight_organigramm_scroll(id);
}
function highlight_organigramm_scroll(id) {
    var elem = document.getElementById(id);
    if (/box\-[0-9]+\-[0-9]+/.exec(elem.parentElement.id)) elem = elem.parentElement;
    elem.style.backgroundColor = \"rgba(0,0,0,0)\";
    var rect = elem.getBoundingClientRect();
    var optimalPageYOffset = window.pageYOffset+rect.top+rect.height/2-window.innerHeight/2;
    var nextPageYOffset = window.pageYOffset+(optimalPageYOffset-window.pageYOffset)/4;
    if (nextPageYOffset<=0) {
        window.scrollTo(0, 0);
        highlight_organigramm_color(id)
    } else if (document.getElementsByTagName(\"body\")[0].offsetHeight-window.innerHeight<=nextPageYOffset) {
        window.scrollTo(0, document.getElementsByTagName(\"body\")[0].offsetHeight-window.innerHeight);
        highlight_organigramm_color(id)
    } else if (Math.abs(nextPageYOffset-optimalPageYOffset)<=3) {
        window.scrollTo(0, optimalPageYOffset);
        highlight_organigramm_color(id)
    } else {
        window.scrollTo(0, Math.round(nextPageYOffset));
        window.setTimeout(function () {highlight_organigramm_scroll(id);}, 50);
    }
}
function highlight_organigramm_color(id) {
    var elem = document.getElementById(id);
    if (/box\-[0-9]+\-[0-9]+/.exec(elem.parentElement.id)) elem = elem.parentElement;
    for (var i=0; i<20; i++) {
        window.setTimeout((function (i) {return function () {
            elem.style.backgroundColor = \"rgba(0,220,0,\"+Math.pow(Math.sin(i*Math.PI/12), 2)+\")\";
        };})(i), i*100);
    }
}
</script>";

$colwid = 120;
$org = "<div style='width:100%; overflow-x:scroll;'><table style='table-layout:fixed; width:".($colwid*count($organigramm))."px;'>";
for ($i=0; $i<count($organigramm); $i++) {
    $ressort = $organigramm[$i];
    $org .= "<td style='width:".$colwid."px; vertical-align:top;'>";
    for ($j=0; $j<count($ressort); $j++) {
        if (0<$j) $org .= "<div style='text-align:center; height:20px; overflow:hidden;'><span style='border-left:1px solid #000000; font-size:20px;'></span></div>";
        $org .= "<div style='margin:0px 0px 0px 1px; padding:0px; border:1px solid #000000; text-align:center;' id='box-".$i."-".$j."'>".$ressort[$j]."</div>";
    }
    $org .= "</td>";
}
$org .= "</table></div>";

echo "<div id='organigramm'><h2>Häufig gesucht</h2>
<div><b><a href='javascript:highlight_organigramm(&quot;link-praesident&quot;)' class='linkint'>Präsident</a></b></div>
<div><b><a href='javascript:highlight_organigramm(&quot;link-mitgliederverwaltung&quot;)' class='linkint'>Mitgliederverwaltung</a></b></div>
<div><b><a href='javascript:highlight_organigramm(&quot;link-kartenverkauf&quot;)' class='linkint'>Kartenverkauf</a></b></div>
<div><b><a href='javascript:highlight_organigramm(&quot;link-kleiderverkauf&quot;)' class='linkint'>Kleiderverkauf</a></b></div>
<div><b>PC-Konto: 85-256448-8</b></div>
<h2>Organigramm OL Zimmerberg</h2>".$org."</div>";

echo "<script type='text/javascript'>
function olz_marquee(elem) {
    var om = elem.getAttribute(\"olzmarquee\");
    if (om) {
        var subdiv = document.createElement(\"div\");
        var subspan = document.createElement(\"span\");
        subspan.innerHTML = elem.innerHTML;
        elem.innerHTML = \"\";
        elem.appendChild(subdiv);
        subdiv.style.textAlign = \"inherit\";
        subdiv.style.width = subdiv.offsetWidth+\"px\";
        subdiv.style.overflowX = \"hidden\";
        subdiv.style.whiteSpace = \"nowrap\";
        subdiv.appendChild(subspan);
        if (subdiv.offsetWidth<subspan.offsetWidth) {
            var sw = subspan.offsetWidth-subdiv.offsetWidth;
            window.setTimeout((function (subdiv, sw) {return function () {
                if (subdiv.scrollLeft<sw) {
                    subdiv.scrollLeft += 1;
                    window.setTimeout(arguments.callee, 75);
                } else {
                    window.setTimeout((function (subdiv, cl) {return function () {
                        subdiv.scrollLeft = 0;
                        window.setTimeout(cl, 1500);
                    };})(subdiv, arguments.callee), 1500);
                }
            };})(subdiv, sw), 100);
        }
    }
    var cld = elem.children;
    for (var i=0; i<cld.length; i++) {
        olz_marquee(cld[i]);
    }
}
//olz_marquee(document.getElementById(\"organigramm\"));
</script>";

?>
