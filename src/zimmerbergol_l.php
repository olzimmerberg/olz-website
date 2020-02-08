<?php

$pref_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
$pref_lang = strtolower($pref_lang);
$lang = !isset($lang) ? $pref_lang : $lang;
$lang = ($lang=='de' OR $lang=='fr') ? $lang : "de" ;

echo "<div>";

/* Weisungen Sprint-Staffel */
if($typ=='weisung_ss'){
    echo "<span style='position:relative; float:right;padding-left:2px;padding-top:5px;text-align:right; color:#000;'><span style='float:left; margin-right:10px;'></span><span style='font-size:150%;font-weight:bold;'>Sonntag, 12. Mai 2019</span></span>
    <h3 style='font-size:150%;font-weight:bold;'>Sprint-Staffel für alle / Relais sprint pour tous</h3>";
    include_once "zimmerbergol_de_weisung_ss.php";
}

/* Weisungen Nat. OL - deutsch */
elseif($lang=='de' or !isset($lang)){
    echo "<span style='position:relative; float:right;padding-left:2px;padding-top:5px;text-align:right; color:#000;'><span style='float:left; margin-right:10px;'></span><span style='font-size:150%;font-weight:bold;'>Sonntag, 12. Mai 2019</span></span>
<h3 style='font-size:150%;font-weight:bold;'>2. Nationaler OL (Dorf Sprint OL) / 12. Zimmerberg OL</h3>
<p style='font-size:120%;'>
Lauf der Jugend OL-Meisterschaft Region ZH/SH+<br>
Testlauf Jugend/Junioren/Elite<br>
Schaffis Stadt OL Cup<br>
World Ranking Event</p>";

    if($typ=='fundgegenstaende'){
        echo "<h3 style='font-size:150%;font-weight:bold;'>Fundgegenstände</h3>";
        include_once "zol_fundgegenstaende.html";
    }
    elseif($typ=='ausschreibung'){
    /* Ausschreibung */
        echo "<h3 style='font-size:150%;font-weight:bold;'>Sprint-Staffel für alle</h3>
    <p style='padding-top:10px;'>
    <table style='border-spacing:1px;'>
    <tr>
        <td class='menu_aus' style='width:25%'><a href='#impressionen' class='linkint'>Impressionen</a></td>
        <td class='menu_aus' style='width:25%'><a href='#ausschreibung' class='linkint'>Ausschreibung Nationaler OL</a></td>
        <td class='menu_aus' style='width:25%'><a href='#ausschreibung_ss' class='linkint'>Ausschreibung Sprint-Staffel</a></td>
        <td class='hide menu_aus' style='width:25%'><a href='#bahndaten' class='linkint'>Bahndaten</a></td>
        <td class='hide menu_aus' style='width:25%'><a href='?page=4&id=808' class='linkint'>Galerie</a></td>
        <td class='hide menu_aus' style='width:25%'><a href='?page=99&event=zol_180527' class='linkint'>Resultate</a></td>
        <td class='hide menu_aus' style='width:25%'><a href='http://www.routegadget.ch/binperl/reitti.cgi?act=map&id=209&kieli=' target='_blank' class='linkext'>RouteGadget</a>
        </td>
        <td class='hide menu_aus' style='width:25%'><a href='#fundsachen' class='linkint'>Fundgegenstände</a></td>
        <td class='hide menu_aus' style='width:25%'><a href='#fotool' class='linkint'>Foto-OL</a></td>
    </tr>
</table>";
    $impressionen = "Impressionen";
    include_once "zimmerbergol_bilder.php";
    include_once "zimmerbergol_de.php";
        }
    /* Weisungen */
    else include_once "zimmerbergol_de_weisung.php";

    }

/* Weisungen Nat. OL - französisch */
elseif($lang=='fr'){
echo "<span style='position:relative; float:right;padding-left:2px;padding-top:5px;text-align:right; color:#000;'><span style='float:left; margin-right:10px;'></span><span style='font-size:150%;font-weight:bold;'>Dimanche, 12 mai 2019</span></span>
<h3 style='font-size:150%;font-weight:bold;'>2ème CO nationale (CO sprint) / 12ème CO Zimmerberg</h3>
<p style='font-size:120%;'>
Course d’orientation juniors, championnat régional ZH / SH+<br>
Course test, cadets / juniors / élite<br>
Schaffis Stadt OL-Cup<br>
World Ranking Event</p>";

    if($typ=='fundgegenstaende'){
        echo "<h3 style='font-size:150%;font-weight:bold;'>Objets trouvés</h3>";
        include_once "zol_fundgegenstaende.html";
    }
    elseif($typ=='ausschreibung'){
    /* Ausschreibung */
        echo "<h3 style='font-size:150%;font-weight:bold;'>Relais Sprint pour tous</h3>
    <p style='padding-top:10px;'>
<table style='border-spacing:1px;'>
    <tr>
        <td class='menu_aus' style='width:25%'><a href='#impressionen' class='linkint'>Impréssions</a></td>
        <td class='menu_aus' style='width:25%'><a href='#ausschreibung' class='linkint'>Annonce CO nationale</a></td>
        <td class='menu_aus' style='width:25%'><a href='#ausschreibung_ss' class='linkint'>Annonce Relais Sprint</a></td>
        <td class='hide menu_aus' style='width:25%'><a href='#bahndaten' class='linkint'>Bahndaten</a></td>
        <td class='hide menu_aus' style='width:25%'><a href='?page=4&id=808' class='linkint'>Galerie</a></td>
        <td class='hide menu_aus' style='width:25%'><a href='?page=99&event=zol_180527' class='linkint'>Resultate</a></td>
        <td class='hide menu_aus' style='width:25%'><a href='http://www.routegadget.ch/binperl/reitti.cgi?act=map&id=209&kieli=' target='_blank' class='linkext'>RouteGadget</a>
        </td>
        <td class='hide menu_aus' style='width:25%'><a href='#fundsachen' class='linkint'>Fundgegenstände</a></td>
        <td class='hide menu_aus' style='width:25%'><a href='#fotool' class='linkint'>Foto-OL</a></td>
    </tr>
</table>";
$impressionen = "Impréssions";
include_once "zimmerbergol_bilder.php";
if($typ=='weisung') include_once "zimmerbergol_fr_weisung.php";
else include_once "zimmerbergol_fr.php";
        }
    /* Weisungen */
    else include_once "zimmerbergol_fr_weisung.php";

    }
?>