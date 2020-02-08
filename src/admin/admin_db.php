<script src="scripts/admin_db.js" type="text/javascript"></script>

<?php
//***************************************************
//Formular zur Verwaltung der Mysql-Datenbanken
//***************************************************
//Datenbankfelder mit Zusatzinformationen defnieren
//Inhalt: [0]Feldname, [1]Bezeichnung, [2]Formularfeldtyp(falls 'checkbox:[2][1]=option,Option/falls 'text/textarea': [2][1]=readonly/falls 'select:[2][1]=option,Option,[2][2]=multiple), [3]Startwert, [4]Kommentar, [5]HTML_Zusatz, [6]Stil, [7]Formatierung,[8]Test,[9]Warnung
//-------------------------------------------------------------
/*
$_SESSION['edit']['db_table']: Tabelle in Bearbeitung
$_SESSION['edit']['modus']: 'neuedit'=neu angelegter Datensatz
$_SESSION['edit']['confirm']: löschen bestätigen
$_SESSION['edit']['replace']: ersetzen bestätigen
$_SESSION['edit']['vorschau']: '1'= speichern aus Vorschau
$_SESSION['edit']['button']: letzter Klick
*/
require_once("image_tools.php");
require_once("file_tools.php");
$layout = "2";
$_SESSION['edit']['table'] = $db_table;
$tmp_folder = "temp";

$markup_notice = (
	"Hinweise:<br>"
	. "<div style='font-weight:normal;'>1. Internet-Link in Text einbauen: Internet-Adresse mit 'http://' beginnen, "
	. "Bsp.: 'http://www.olzimmerberg.ch' wird zu  <a href='http://www.olzimmerberg.ch' class='linkext' target='blank'><b>www.olzimmerberg.ch</b></a><br>"
	. "2. Text mit Fettschrift hervorheben: Fetten Text mit '&lt;b>' beginnen und mit '&lt;/b>' beenden, "
	. "Bsp: '&lt;b>dies ist fetter Text&lt;/b>' wird zu '<b>dies ist fetter Text</b>'<br>"
	. "3. Bilder:<br><table><tr class='tablebar'><td><b>Bildnummer</b></td><td><b>Wie einbinden?</b></td></tr>"
	. "<tr><td>1. Bild</td><td>&lt;BILD1></td></tr><tr><td>2. Bild</td><td>&lt;BILD2></td></tr></table><br>"
	. "4. Dateien:<br><table><tr class='tablebar'><td><b>Dateinummer</b></td><td><b>Wie einbinden?</b></td><td><b>Wie wird's aussehen?</b></td></tr>"
	. "<tr><td>1. Datei</td><td>&lt;DATEI1 text=&quot;OL Karte&quot;></td><td><a style='padding-left:17px; background-image:url(img/fileicons/image-16.png); background-repeat:no-repeat;'>OL Karte</a></td></tr>"
	. "<tr><td>2. Datei</td><td>&lt;DATEI2 text=&quot;Ausschreibung als PDF&quot;></td><td><a style='padding-left:17px; background-image:url(img/fileicons/pdf-16.png); background-repeat:no-repeat;'>Ausschreibung als PDF</a></td></tr></table></div>"
);

if ($db_table == "aktuell")
{// DB AKTUELL
$img_folder = "img";
$img_max_size = 240;
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("datum","Datum","datum","date('Y-m-d');","","","",""),
                array("zeit","text","hidden","date('H:i:s');","","","",""),
                array("on_off","Aktiv","boolean","1","","","",""),
                array("typ","Typ","text","aktuell","","","",""),
                array("titel","Titel","text","''","","","",""),
                array("text","Kurztext","textarea","''","","",""," rows='4'"),
                array("textlang","Haupttext","textarea","''",$markup_notice,"",""," rows='8'"),
                array("autor","Autor","text","''","","","",""),
                array("link","Link","text","''","","","",""),
                array("termin","Termin","hidden","0","","","",""),
                array("newsletter","Newsletter versenden","boolean","1","","","",""),
                array("newsletter_datum","versandt am","datumzeit","","","","","")
                );
}

elseif ($db_table == "anmeldung")
{// DB ANMELDUNG
$send_mail = "on";
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("datum","Datum",array("text"," readonly"),"date('Y-m-d');","","","",""),
                array("event_id","event_id","hidden",$id_event,"","","",""),
                array("name","Name, Vorname","text","''","","",""),
                array("email","Emailadresse","text","''","","","","","olz_is_email","Bitte gültige Emailadresse angegen."),
                array("anzahl","Anzahl Teilnehmer","text","''","","","","","!empty","Bitte Teilnehmeranzahl angegen."),
                array("zeit","text","hidden","date('H:i:s');","","","","")
                );

                $sql = "SELECT * from anm_felder WHERE (event_id=".$_SESSION[$db_table.'id_event_'].") ORDER BY position ASC" ;
                $result = $db->query($sql);
                while($row = $result->fetch_assoc())
                    {$label = $row['label'];
                    $position = $row['position'];
                    $typ = $row['typ'];
                    $info = $row['info'];
                    $standard = $row['standard'];
                    $test = $row['test'];
                    $test_result = $row['test_result'];
                    $var = "feld".$position;
                    if ($typ=='checkbox') $typ = array("checkbox",array(array("","1")));
                    else $typ = "\"".$typ."\"";
                    array_push($db_felder,array("$var","$label",$typ,"'$standard'","$info","","","","$test","$test_result"));
                    }
                array_push($db_felder,    array("on_off","on_off","hidden","'0'","","","","","",""));
                array_push($db_felder,    array("uid","Code",array("text"," readonly"),"olz_create_uid($db_table)","","","",""));
}

elseif ($db_table == "anm_felder")
{// DB ANM_FELDER (Zusatzfelder Anmeldung)
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("event_id","event_id","hidden","'$id_anm'","","","",""),
                array("label","Label","text","''","","","",""),
                array("position","Position","text","'$next_pos'","","","",""),
                array("typ","Typ",array("select",array(array("Textfeld","text"),array("Text mehrzeilig","textarea"),array("Checkbox","checkbox"))),"'text'","","","",""),
                array("info","Info","textarea","''","","",""," rows='4'"),
                array("zeigen","Position Liste","text","''","","","",""),
                array("standard","Standardwert","text","''","","","",""),
                array("test","Feldwertprüfung","text","''","","","",""),
                array("test_result","Feldwertprüfung Text","text","''","","","","")
                );
}

elseif ($db_table == "bild_der_woche")
{// DB BILD DER WOCHE
$layout = "1";
$img_max_size = 240; //maximale Bildbreite,-höhe
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("datum","Datum","datum","date('Y-m-d');","","","",""),
                array("on_off","Aktiv","hidden","1","","","",""),
                array("titel","Mouseover-Text","text","''","","","",""),
                array("text","Bildlegende","textarea","''","","",""," rows='4'"),
                );
}

elseif ($db_table == "blog")
{// DB BLOG
$nutzer = $_SESSION['user'];
$img_folder = "img";
$img_max_size = 240; //maximale Bildbreite,-höhe
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("datum","Datum","datum","date('Y-m-d');","","","",""),
                array("newsletter","Newsletter","hidden","1","","","",""),
                array("zeit","Zeit","text","date('H:i:s');","","","",""),
                array("autor","Autor",array("text",$nutzer=="gold" ? "" : " readonly"),"ucwords('$nutzer')","","","",""),
                array("titel","Titel","text","''","","","","","!empty","Bitte Titel angeben."),
                array("on_off","Aktiv","boolean","1","","","",""),
                array("text","Text","textarea","''",$markup_notice,"",""," rows='8'","!empty","Bitte Text angeben."),
                array("newsletter_datum","Newsletter Datum","datumzeit","","","","",""),
                );
}

elseif ($db_table == "downloads")
{// DB DOWNLOADS
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("datum","Datum","hidden","date('Y-m-d');","","","",""),
                array("name","Bezeichnung","text","''","","","",""),
                array("position","Position","hidden","'0'","","","",""),
                array("on_off","Aktiv","boolean","1","","","","")
                );
}

elseif ($db_table == "forum")
{// DB FORUM
$send_mail = "on";
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("datum","Datum","hidden","date('Y-m-d');","","","",""),
                array("name","Titel","text","''","","","","","!empty","Bitte einen Titel angeben."),
                array("name2","Name","text","''","","","","","!empty","Bitte einen Namen angeben."),
                array("email","Email","text","''","","","","","olz_is_email","Bitte gültige Emailadresse angeben."),
                array("eintrag","Text","textarea","''","","",""," rows='8'","!empty","Hast du nichts mitzuteilen ?"),
                array("zeit","text","hidden","date('H:i:s');","","","",""),
                array("on_off","Aktiv","boolean","'1'","","","",""),
                array("uid","Code",array("text"," readonly"),"olz_create_uid($db_table)","","","","")
                );
            if ($_SESSION['auth']=="all")
                {array_push($db_felder,
                    array("newsletter","Newsletter","boolean","'1'"," (Freischalten des Forumeintrages für Newsletter.)","","",""));
                }
}

elseif ($db_table == "galerie")
{// DB GALERIE
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("datum","Datum","datum","date('Y-m-d');","","","",""),
                array("titel","Titel","text","''","","","",""),
                array("autor","Autor","text","''","","","","","",""),
                array("counter","Counter","hidden","'0'","","","","","",""),
                array("typ","Typ",($_SESSION["auth"]=="all"?array("select",array(array("Fotos","foto"),array("Film","movie"))):"hidden"),"'foto'","","","","","",""),
                array("content","Filmangaben",($_SESSION["auth"]=="all"?"text":"hidden"),"''","","","","","",""),
                array("termin","Termin","hidden","0","","","",""),
                );
            if ($_SESSION['auth']=="all")
                {array_push($db_felder,
                    array("on_off","Aktiv","boolean","1","","","",""));
                }

}

elseif ($db_table == "karten")
{// DB KARTEN
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("position","Position","text","''","","","",""),
                array("kartennr","Kartennummer","text","''","","","",""),
                array("name","Kartenname","text","''","","","",""),
                array("vorschau","Dateiname Vorschau","text","''","","","",""),
                array("typ","Kartentyp",array("select",array(array("Normalkarte","ol"),array("Dorfkarte","stadt"),array("Schulhauskarte","scool"))),"'ol'","","","",""),
                array("center_x","X-Koordinate","text","''","","","",""),
                array("center_y","Y-Koordinate","text","''","","","",""),
                array("jahr","Kartenjahr","text","''","","","",""),
                array("ort","Ort","text","''","","","",""),
                array("massstab","Massstab","text","''","","","",""),
                array("zoom","Zoomfaktor",array("select",array(array("1 Pixel = 2m","2"),array("1 Pixel = 8m","8"),array("1 Pixel = 32m","32"))),"''","Zoomfaktor für Anzeige auf map.search.ch","","","")
                );
}

elseif ($db_table == "links")
{// DB LINKS
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("datum","Datum","hidden","date('Y-m-d');","","","",""),
                array("position","Position","hidden","'0'","","","",""),
                array("name","Bezeichnung","text","''","","","","","!empty","Bitte Download-Bezeichnung angeben."),
                array("url","URL","text","'http://'","","","","","!empty","Bitte URL angeben."),
                array("on_off","Aktiv","boolean","1","","","","")
                );
}

elseif ($db_table == "newsletter")
{// DB NEWSLETTER
$layout = "1";
$send_mail = "on";
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("reg_date","Datum","hidden","date('Y-m-d');","","","",""),
                array("on_off","Aktiv","hidden","''","","","",""),
                array("name","Vorname, Name","text","''","","","","","!empty","Bitte einen Namen angeben."),
                array("email","Email-Adresse","text","''","","","","","olz_is_email","Bitte gültige Emailadresse angeben."),
                array("kategorie","Benachrichtigung bei",array("checkbox",array(array("Neuen Nachrichten","aktuell"),array("Neuen Forumsbeiträgen","forum"),array("Wichtige Termine (z.B. Meldeschluss)","termine"),array("Vorstandsmitteilungen","vorstand"))),"'vorstand'","","","display:block;",""),
                array("uid","Code",array("text"," readonly"),"olz_create_uid($db_table)","","","","")
                );
}

elseif ($db_table == "rundmail")
{// DB RUNDMAIL
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("datum","Datum","hidden","date('Y-m-d');","","","",""),
                array("betreff","Betreff","text","'Newsletter OLZimmerberg - '","","","","","!empty","Bitte Betreff eingeben."),
                array("mailtext","Bezeichnung","textarea","''","","","","","!empty","Bitte Mailtext eingeben.")
                );
}

elseif ($db_table == "termine")
{// DB TERMINE
//include 'parse_solv_ranglisten.php';
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("datum","Datum (Beginn)","datum","date('Y-m-d')","Format: yyyy-mm-tt (z.B. '2006-01-31')","","",""),
                array("datum_end","Datum (Ende)","datum","","Bei mehrtägigen Anlässen (sonst leer lassen).","<input type='button' name='' onclick='End_angleichen()' value='1. Datum übernehmen' class='dropdown' style='width: 44%;margin-left:10px;'>","",""),
                array("datum_off","Datum (Ausschalten)","datum","","Termin wird ab diesem Datum permanent ausgeblendet.","<input type='button' name='' onclick='Off_angleichen()' value='2. Datum übernehmen' class='dropdown' style='width: 44%;margin-left:10px;'>","width:50%",""),
                array("titel","Titel","text","''","","<select name='set_titel' style='width:33%;margin-left:10px;' size='1'
onchange='Titel_angleichen()' class='dropdown'>
<option value=''>&nbsp;</option>
<option value='. Nationaler OL'>Nationaler</option>
<option value='-OL-Meisterschaft'>Meisterschaft</option>
<option value=' OL-Weekend'>Weekend</option>
<option value='Training: '>Training</option>
<option value='Meldeschluss '>Meldeschluss</option>
</select>","width:60%;font-weight:bold;",""),
                array("text","Text","textarea","''","","",""," rows='4'"),

                array("typ","Typ",array("checkbox",array(array('Klubanlass','club'),array('OL','ol'),array('Training','training'))),"''","","","",""),

                array("link","Link","textarea","''","",
"<p>
<input value='+' style='width:18px;' type='button' onclick='Linkhilfe()' class='dropdown'>
<select name='set_link' style='width: 33%;' size='1' class='dropdown'>
<option value=''>&nbsp;</option>
<option value='1'>Ausschreibung</option>
<option value='8'>Anmeldung</option>
<option value='2'>GO2OL</option>
<option value='3'>Fahrplan</option>
<option value='4'>[Mail]</option>
<option value='5'>[Link intern]</option>
<option value='6'>[Link extern]</option>
<option value='7'>[Link PDF]</option>
</select>
<input name='help_set_link' value='' style='width: 56%;' type='text'>",""," rows='4'"),
                array("teilnehmer","TeilnehmerInnen","number","","","","",""),
                array("xkoord","X-Koordinate","number","","","<input type='button' name='' onclick='koordinaten()' value='Analysieren' title='Versucht automatisch X- und Y-Koordinate aus der Eingabe zu eruieren\nBsp: Eingabe: \"	263925 / 699025\" > Ausgabe: X=\"699025\", Y=\"699025\"' class='dropdown' style='width: 44%;margin-left:10px;'>","width:150px;",""),
                array("ykoord","Y-Koordinate","number","","","","width:150px;",""),
                array("on_off","Aktiv","boolean","1","","","",""),
                array("newsletter","Newsletter","boolean","0","","","",""),
                array("go2ol","GO2OL-Code","text","''","","","",""),
                array("solv_uid","SOLV-ID","number","","","","",""),
                //array("datum_anmeldung","Meldeschluss","text","''","Für interne Online-Anmeldungen. Eine Datumsangabe schaltet die interne Online-Anmeldung frei.","","",""),
                //array("text_anmeldung","Erläuterungen","textarea","''","Erläuterungstext für interne Online-Anmeldungen","",""," rows='4'"),
                //array("email_anmeldung","Emailadresse Anmeldungen","text","''","Anmeldungen an diese Adresse schicken.","","","")
                    );
}
elseif ($db_table == "vorstand")
{// DB VORSTAND
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("name","Bezeichnung","text","''","","","",""),
                array("funktion","Funktion","text","","","","",""),
                array("email","E-Mail","text","","","","",""),
                array("on_off","Aktiv","boolean","1","","","",""),
                );
}
elseif ($db_table == "event")
{// EVENT - Online-Ranglisten
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("datum","Datum","datum","date('Y-m-d');","","","",""),
                array("name","Bezeichnung","text","''","","","",""),
                array("name_kurz","Dateiname","text","''","","<br>Name der Exportdatei aus der Auswertungssoftware","","","",""),
                array("kat_gruppen","Kategorien gruppiert","text","''","","<br>z.B. 'H10 D10;H45 H50' (gruppiert nach gemeinsamen Bahnen)","","","",""),
                array("karten","Karten","text","''","","<br>z.B. Anzahl vorgedruckte Karten '10;15;16;8' (gleiche Reihenfolge wie Kategoriegruppen)","","","","")
                );
}
elseif ($db_table == "olz_text")
{// TEXTE
$db_felder = array(
                array("id","ID","hidden","''","","","",""),
                array("text","Text","textarea","''","","",""," rows='8'")
                );
}

//-------------------------------------------------------------
// Button-Rückgabe modulieren
//-------------------------------------------------------------
$_SESSION['edit']['button'] = $$button_name;

if ($function == "start") $do = "getdata";
elseif ($function == "duplicate") $do = "duplicate";
elseif ($function == "start_user") $do = "getdata";
elseif ($function == "neu") $do = "neu";
elseif ($function == "edit") $do = "edit";
elseif ($function == "replace") $do = "vorschau";
elseif ($function == "vorschau") $do = "vorschau";
elseif ($function == "code") $do = "code";
elseif (($function == "abbruch") AND ($_SESSION['edit']['replace']=="1")) $do = "deletefile";
elseif (($function == "abbruch") AND ($_SESSION['edit']['modus']=="neuedit")) $do = "delete";
elseif ($function == "abbruch") $do = "abbruch";
elseif (($function == "save") AND ($_SESSION['edit']['vorschau']=="0")) $do = "save";
elseif ($function == "save") $do = "submit";
elseif (($function == "delete") AND ($_SESSION['edit']['confirm'] == "1")) $do = "delete";
elseif ($function == "delete") $do = "confirm";
elseif ($function == "undo") $do = "delete";
elseif ($function == "Einblenden") $do = "ein";
elseif ($function == "Ausblenden") $do = "aus";
elseif ($function == "up") $do = "up";
elseif ($function == "down") $do = "down";
elseif ($function == "activate") $do = "activate";

if ($do=="confirm")
    {$alert = "Möchtest du diesen Eintrag wirklich löschen ?";
    $do = "edit";
    $_SESSION['edit']['confirm'] = "1";
    }
else $_SESSION['edit']['confirm'] = "0";

//-------------------------------------------------------------
// USER Code eingeben
//-------------------------------------------------------------
if ($do=="code")
    {echo "<table class='liste'><tr><td style='width:20%;'><span style='font-weight:bold;'>Code:</span></td><td style='width:80%'>
        <input type='text' name='uid'  style='width:100%;'></td></tr></table>";
    echo "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Weiter","1"),array("Abbrechen","2")),"")."</div>";
    unset($_SESSION['edit']['button']);
    }
//-------------------------------------------------------------
// DS duplizieren
//-------------------------------------------------------------
if ($do == "duplicate")
    {$sql = "SELECT * from $db_table WHERE (id = '".$id."') ORDER BY id ASC";
    $result = $db->query($sql);
    if ($result->num_rows==0)
        {$do = "abbruch";
		$alert = "Kein Datensatz gewählt.";
        }
    else
        {$row = $result->fetch_assoc();
		unset($row["id"]); //Remove ID from array
		$row = array_filter($row,'strlen'); // Null-/Leerwerte herausfiltern
		$sql = "INSERT INTO $db_table";
		$sql .= " ( " .implode(", ",array_keys($row)).") ";
		$sql .= " VALUES ('".implode("', '",array_values($row)). "')";
    	$result = $db->query($sql);
    	$id = $db->insert_id;
		$_SESSION[$db_table."id_"] = $id;
		$_SESSION['edit']['modus'] = "neuedit";
        $do = "getdata";
        }
    if ($_SESSION['edit']['modus'] != "neuedit") $_SESSION['edit']['modus'] = "";

    }

//-------------------------------------------------------------
// Neuer Datensatz
//-------------------------------------------------------------
if ($do=="neu")
    {$sql_tmp = array();
    foreach ($db_felder as $tmp_feld)
        {if($tmp_feld[3]>''){
            $start_value="\$start_value = ".$tmp_feld[3];
            eval("$start_value;");
            //echo $start_value;
            if ($tmp_feld[0] != 'id')
            	array_push($sql_tmp,$tmp_feld[0]." = '".$start_value."'");
            }
        }
    if(!isset($_SESSION['edit']['modus'])){
    $sql = "INSERT $db_table SET ".implode(",", $sql_tmp);
    if ($_SESSION['auth'] == 'all') echo "### HOSTSTAR DEBUG ###<br>SQL: ".htmlentities($sql)."<br>";
        $result = $db->query($sql);
        $id = $db->insert_id;
        if ($_SESSION['auth'] == 'all') echo "NEUE ID: ".$id."<br>";
        $_SESSION[$db_table."id_"] = $id;

        $do = "getdata";
        $_SESSION['edit']['modus'] = "neuedit";
        }
    else $do = "getdata";
    }

//-------------------------------------------------------------
// Daten aus DB holen
//-------------------------------------------------------------
if ($do == "getdata")
    {if ($function == "start_user") $sql = "SELECT * from $db_table WHERE (uid = '".$uid."') ORDER BY id ASC";
    else $sql = "SELECT * from $db_table WHERE (id = '".$id."') ORDER BY id ASC";

    $result = $db->query($sql);
    if ($result->num_rows==0)
        {$do = "abbruch";
        if ($function == "start_user") $alert = "Ungültiger Code !";
        else $alert = "Kein Datensatz gewählt.";
        }
    else
        {$row = $result->fetch_assoc();
        foreach ($db_felder as $tmp_feld)
            {$var = $tmp_feld[0];
            if ($var=='id') $id = $row['id'];
            $_SESSION[$db_table.$var] = stripslashes($row[str_replace(array("[","]"),array("",""),$tmp_feld[0])]);
            }
        $do = "edit";
        }
    if ($_SESSION['edit']['modus'] != "neuedit") $_SESSION['edit']['modus'] = "";

    }

//-------------------------------------------------------------
// Eintrag löschen
//-------------------------------------------------------------
if ($do == "delete")
    {

    // Bilder löschen
    if (isset($tables_img_dirs[$db_table])) {
        $db_imgpath = $tables_img_dirs[$db_table];
        if (is_dir($db_imgpath."/".$id."/img")) {
            $imgs = scandir($db_imgpath."/".$id."/img");
            for ($i=0; $i<count($imgs); $i++) {
                if ($imgs[$i]!=".." && $imgs[$i]!=".") @unlink($db_imgpath."/".$id."/img/".$imgs[$i]);
            }
            @rmdir($db_imgpath."/".$id."/img");
        }
        if (is_dir($db_imgpath."/".$id."/thumb")) {
            $imgs = scandir($db_imgpath."/".$id."/thumb");
            for ($i=0; $i<count($imgs); $i++) {
                if ($imgs[$i]!=".." && $imgs[$i]!=".") @unlink($db_imgpath."/".$id."/thumb/".$imgs[$i]);
            }
            @rmdir($db_imgpath."/".$id."/thumb");
        }
        @rmdir($db_imgpath."/".$id);
    }

    $sql ="DELETE FROM $db_table WHERE (id = '".$_SESSION[$db_table."id"]."')";
    $result = $db->query($sql);
    $ds_count = -1;
    $do = "abbruch";
// ical-DATEI AKTUALISIEREN
    if (in_array($db_table,array("termine")))
        {include'ical.php';
        }

    }

//-------------------------------------------------------------
// Position verschieben
//-------------------------------------------------------------
if (($do == "up") OR ($do=="down"))
    {if ($do=="up") $offset = "-1.5";
    else $offset = "1.5";
    $sql = "UPDATE $db_table SET position=(position+$offset) WHERE (id= '$id')";
    $db->query($sql);
    $sql = "SELECT * FROM $db_table ORDER BY position ASC";
    $result = $db->query($sql);
    $counter = 1;
    while ($row = $result->fetch_assoc())
        {$sql = "UPDATE $db_table SET position='$counter' WHERE (id='".$row['id']."')";
        $db->query($sql);
        $counter = $counter + 1;
        }
    $do = "abbruch";
    }
//-------------------------------------------------------------
// Eingabe abbrechen
//-------------------------------------------------------------
if ($do == "abbruch")
    {foreach ($db_felder as $tmp_feld)
        {if (($tmp_feld[2]=="file") AND (isset($_SESSION[$db_table]['name'])) AND file_exists($tmp_folder."/".$_SESSION[$db_table]['name'])) unlink($tmp_folder."/".$_SESSION[$db_table]['name']); // Temp-Datei löschen
        }
    unset($_SESSION['edit']);
    }

//-------------------------------------------------------------
// Galerie aktivieren
//-------------------------------------------------------------
if ($do == "activate")
    {$sql = "UPDATE $db_table SET on_off='1' WHERE id='".$id."'";
    $result = $db->query($sql);
    unset($_SESSION['edit']);
    }

//-------------------------------------------------------------
// Werte in Session-Variablen schreiben
//-------------------------------------------------------------
if ($do == "save")
    {foreach ($db_felder as $tmp_feld)
        {$var = $tmp_feld[0];
        $_SESSION[$db_table.$var] = $_POST[$db_table.$var];
        }
    $do = "submit";
    }

//-------------------------------------------------------------
// DS Speichern
//-------------------------------------------------------------
if ($do == "submit")
    {$sql_tmp = array();
    function user2db($feld_typ, $wert) {
        $default = "'" . DBEsc(trim($wert)) . "'";
        if ($feld_typ=='boolean') {
            return $wert!='' ? '1' : '0';
        }
        if ($feld_typ=='number') {
            return DBEsc(''.intval($wert));
        }
        if ($feld_typ=='datum') {
            if ($wert=='') return 'NULL';
            if ($wert=='0000-00-00') return 'NULL';
            return $default;
        }
        if ($feld_typ=='datumzeit') {
            if ($wert=='') return 'NULL';
            if ($wert=='0000-00-00') return 'NULL';
            if ($wert=='0000-00-00 00:00:00') return 'NULL';
            return $default;
        }
        return $default;
    }
    foreach ($db_felder as $tmp_feld)
        {$var = $tmp_feld[0];
		//uu, 29.12.19 > Checkbox-Felder vom Typ 'boolean' werden als Array behandelt > 1. Wert abfragen
        if (is_array($_SESSION[$db_table.$var]) AND $tmp_feld[2]=='boolean')
            {$_SESSION[$db_table.$var] = $_SESSION[$db_table.$var][0];
            }
        elseif (is_array($_SESSION[$db_table.$var]))
            {$_SESSION[$db_table.$var] = explode(" ",$_SESSION[$db_table.$var]);
            }
        array_push($sql_tmp,$delimiter.$var." = ".user2db($tmp_feld[2], $_SESSION[$db_table.$var]));

        }

    $sql = "UPDATE $db_table SET ".implode(",", $sql_tmp)." WHERE (id = '".$_SESSION[$db_table."id"]."')";
    if ($_SESSION['auth'] == 'all') echo "### HOSTSTAR DEBUG ###<br>SQL: ".htmlentities($sql)."<br>";
    $result = $db->query($sql);

    if ($db_table == "bild_der_woche")
        {$sql = "UPDATE $db_table SET on_off='0' WHERE NOT (id = '".$_SESSION[$db_table."id"]."') AND (on_off = '1')";
        $db->query($sql);
        }

    if (in_array($db_table,array("newsletter","anmeldung","forum"))) // Nach Abschicken aktivieren
        {$sql = "UPDATE $db_table SET on_off='1' WHERE (id = '".$_SESSION[$db_table."id"]."')";
        $db->query($sql);
        }
// ical-DATEI AKTUALISIEREN
    if (in_array($db_table,array("termine")))
        {include'ical.php';
        }
// BESTAETIGUNGSMAIL
    if ($send_mail == "on")
        {$page_link = array_search($db_table,array("","","","","","forum","","newsletter","","","","","","anmeldung"));
        $mail_text = ucfirst($db_table)." OL Zimmerberg\n************************\n";
        $mail_header = "From: OL Zimmerberg <".$db_table."@olzimmerberg.ch>\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8 \nContent-Transfer-Encoding: base64";
        $mail_subject = "OL Zimmerberg - ".ucfirst($db_table);
        $mail_adress = array("u.utzinger@sunrise.ch"); // Kontrollmail
        if (!$local) array_push($mail_adress,$_SESSION[$db_table."email"]); // Usermail

    if ($db_table == "forum")
        {$felder_tmp = array(array("name","Name"),array("email","Email-Adresse"),array("eintrag","Eintrag"));
        $feedback = "Dein Eintrag wurde gespeichert. Du erhältst ein Bestätigungsmail mit einem Code. Damit kannst du deinen Eintrag jederzeit ändern oder löschen.";
        }

    elseif ($db_table == 'anmeldung')
        {$sql = "SELECT * FROM termine WHERE (id='$id_event')";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        $mail_text .= "\n".$row['titel']."\n";

        if (!$local) array_push($mail_adress,$row['email_anmeldung']); // Organisatormail

        $felder_tmp = array(array("name","Name, Vorname"),array("email","Email-Adresse"),array("anzahl","Anzahl Teilnehmer"));
        $sql = "SELECT * FROM anm_felder WHERE (event_id='$id_anm') AND (typ<>'hidden') ORDER BY position ASC";
        $result = $db->query($sql);
        while($row = $result->fetch_assoc())
            {array_push($felder_tmp,array("feld".$row['position'],$row['label']));
            }
        $feedback = "Deine Anmeldung wurde gespeichert. Du erhältst ein Bestätigungsmail mit einem Code. Damit kannst du die Anmeldung bis zum Meldeschluss jederzeit ändern.";
        }

    elseif ($db_table == 'newsletter')
        {$felder_tmp = array(array("name","Name"),array("email","Email-Adresse"),array("kategorie","Benachrichtigung bei"));
        $feedback = "Dein Eintrag wurde gespeichert. Du erhältst ein Bestätigungsmail mit einem Code. Damit kannst du den Newsletter jederzeit ändern.";
        }

    // MAILTEXT
    foreach($felder_tmp as $feld_tmp)
        {$var = $db_table.$feld_tmp[0];
        $label = $feld_tmp[1];
        $mail_text .= $label.": ".$_SESSION[$var]."\n";
        }
    $mail_text = $mail_text."\n\n************************\nDein Eintrag wurde bearbeitet/geändert am: ".date("Y-m-d")."/".date("H:i:s")."\nCode: ".$_SESSION[$db_table."uid"]." (direkter Link: http://www.olzimmerberg.ch/index.php?page=".$page_link."&button$db_table=Weiter&code=".$_SESSION[$db_table."uid"].")";

    // MAIL SENDEN
    foreach($mail_adress as $mailadress_tmp)
        {mail($mailadress_tmp,$mail_subject,base64_encode($mail_text),$mail_header,$mail_from);
		//echo $mail_from;
        }
    }
    unset($_SESSION['edit']);
    }
//-------------------------------------------------------------
// Vorschau
//-------------------------------------------------------------
if ($do == "vorschau")
    {//include 'upload.php';
    $vorschau = array();
    foreach ($db_felder as $tmp_feld)
        {$test = "";
        $var = $tmp_feld[0];
        if (isset($_POST[$db_table."id"]))
            {if (is_array($tmp_feld[2][1]))
                {if ($_POST[$db_table.$var]=="") $_SESSION[$db_table.$var] = "";
                else $_SESSION[$db_table.$var] = implode(" ",$_POST[$db_table.$var]);
                }
            else $_SESSION[$db_table.$var] = $_POST[$db_table.$var];
            if (isset($tmp_feld[8]) AND ($tmp_feld[8]!='')) // Feldwert überprüfen
                {$tmp = "\$test=".$tmp_feld[8]."(\$_SESSION['".$db_table.$var."']);";
                eval("$tmp");
                $var_alert = $db_table.$var."_alert";
                if ($test)
                    {$$var_alert = "";}
                else
                    {$$var_alert = "<span class='error'>".$tmp_feld[9]."</span>";
                    $do = "edit";
                    }
                }
            }

		//uu, 29.12.19 > Checkbox-Felder vom Typ 'boolean' werden als Array behandelt > 1. Wert abfragen
		$wert = ($tmp_feld[2]=='boolean') ? $_SESSION[$db_table.$var][0] : $_SESSION[$db_table.$var];
        $vorschau[$var] = stripslashes($wert);

        }

    if (isset($_SESSION['edit']['replace']))
        {$do = "edit";
        }
    if ($do == "vorschau")
        {echo "<h2>Vorschau</h2>";
        if ($db_table == "rundmail")
            {$html_menu = "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Bearbeiten","1"),array("Abschicken","4")),"")."</div>";}
        else
            {$html_menu = "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Bearbeiten","1"),array("Speichern","4")),"")."</div>";
            }
        $_SESSION['edit']['vorschau'] = "1";
        }
    }
//-------------------------------------------------------------
// DS Eingabe
//-------------------------------------------------------------
if ($do == "edit")
    {// Eingabe-Formular aufbauen
    $html_input = "";
    $html_hidden = "";
	if($function == "duplicate") $html_input = "<h2 style='margin-bottom:15px;'>Duplikat bearbeiten</h2>";
	elseif($function == "neu") $html_input = "<h2 style='margin-bottom:15px;'>Neuer Datensatz bearbeiten</h2>";
	else $html_input = "<h2 style='margin-bottom:15px;'>Datensatz bearbeiten</h2>";
    // Datenbankfelder

    foreach ($db_felder as $tmp_feld)
        {$var = $tmp_feld[0];
        $var2 = $db_table.$var;
        $feld_name = $db_table.$var;
        $feld_wert = $_SESSION[$db_table.$var];
        $feld_bezeichnung = $tmp_feld[1];
        if (is_array($tmp_feld[2])) $feld_typ = $tmp_feld[2][0];
        else $feld_typ = $tmp_feld[2];
        if (is_array($tmp_feld[2]) AND ($tmp_feld[2][0]!='checkbox')) $feld_rw = $tmp_feld[2][1]; // readonly
        else $feld_rw = "";
        $feld_kommentar = $tmp_feld[4];
        $feld_kommentar = ($feld_kommentar>'')?"<br><b>".$tmp_feld[4]."</b>":"";
        $feld_stil = $tmp_feld[6];
        $feld_spezial = $tmp_feld[5];
        $feld_format = $tmp_feld[7];
        $var_alert = $db_table.$var."_alert";
        if ($layout=='2') $bez_style = " style='width:20%;padding-top:4px;'";
        else $bez_style = "";

        if ($layout == "2") $tmp_code = "</td><td style='width:80%'>";
        else $tmp_code = "<p>";

        if ($feld_typ == "text" || $feld_typ == "number" || $feld_typ == "datumzeit") //Input-Typ 'text'
            {$feld_stil = ($feld_stil=="") ? "style='width:95%;'" : "style='".$feld_stil."'";
            $html_input .= "<tr><td$bez_style><b>".$feld_bezeichnung.":</b>".$tmp_code."<input type='text' name='".$feld_name."' value='".htmlspecialchars(stripslashes($feld_wert),ENT_QUOTES)."' ".$feld_stil.$feld_rw.">".$feld_spezial.$$var_alert.$feld_kommentar."</td></tr>\n";}

        elseif ($feld_typ == "datum") //Input-Typ 'text' mit Einbelndkalender
            {$html_input .= "\n<tr><td$bez_style><b>".$feld_bezeichnung.":</b>".$tmp_code."<input type='text' name='".$feld_name."' value='".htmlspecialchars(stripslashes($feld_wert),ENT_QUOTES)."' ".$feld_stil.$feld_rw." class='dateformat-Y-ds-m-ds-d highlight-days-67' size='10'>".$feld_spezial.$$var_alert.$feld_kommentar."</td></tr>\n";}

        elseif ($feld_typ == "textarea") //Input-Typ 'textarea'
            {$html_input .= "<tr><td$bez_style><b>".$feld_bezeichnung.":</b>".$tmp_code."<textarea name='".$feld_name."'".$feld_format." style='width:95%;".$feld_stil."'".$feld_rw.">".stripslashes($feld_wert)."</textarea>".$feld_spezial.$$var_alert.$feld_kommentar."</td></tr>\n";}

        elseif ($feld_typ == "checkbox") //Input-Typ 'checkbox'
            {$html_input .= "<tr><td$bez_style><b>".$feld_bezeichnung.":</b>".$tmp_code;
            $feld_wert = explode(" ",$feld_wert);
            foreach ($tmp_feld[2][1] as $option)
                {$value = $option[1];
                $text = $option[0];
                if (in_array($value,$feld_wert)) $checked = " checked";
                else $checked = "";
                $html_input .= "<span style='padding-right:20px;".$feld_stil."'><input type='checkbox' name='".$feld_name."[]'".$checked." style='margin-top:0.4em;margin-right:0.5em;' value='$value'><span style='vertical-align:bottom;'>$text".$feld_spezial.$feld_kommentar."</span></span>";
                }
            $html_input .= "</td></tr>\n";
            }

        elseif ($feld_typ == "boolean") //Input-Typ 'boolean'
            {$html_input .= "<tr><td$bez_style><b>".$feld_bezeichnung.":</b>".$tmp_code;
            if (intval($feld_wert) != 0) $checked = " checked";
            else $checked = "";
            $html_input .= "<span style='padding-right:20px;".$feld_stil."'><input type='checkbox' name='".$feld_name."[]'".$checked." style='margin-top:0.4em;margin-right:0.5em;' value='1'><span style='vertical-align:bottom;'>".$feld_spezial.$feld_kommentar."</span></span>";
            $html_input .= "</td></tr>\n";
            }

        elseif ($feld_typ == "select") //Input-Typ 'select'
            {$html_input .= "<tr><td$bez_style><b>".$feld_bezeichnung.":</b>".$tmp_code."<select size='1' name='".$feld_name."[]'>";
            $feld_wert = explode(" ",$feld_wert);
            foreach ($tmp_feld[2][1] as $option)
                {$value = $option[1];
                $text = $option[0];
                if (in_array($value,$feld_wert)) $checked = " selected";
                else $checked = "";
                $html_input .= "<p><option".$checked." style='".$feld_stil."' value='$value'>$text</option >".$feld_spezial.$feld_kommentar."";
                }
            $html_input .= "</select></td></tr>\n";
            }

        elseif ($feld_typ == "hidden") //Input-Typ 'hidden'
            {$html_hidden .= "<input type='hidden' name='".$feld_name."' value='".stripslashes($feld_wert)."'>\n";}
        /*
        elseif ($feld_typ == "image") //Input-Typ 'image'
            {if ($feld_stil=="") $feld_stil = "style='width:95%;'";
            $x=0;
            $laenge = $tmp_feld[2][3];
            if ($laenge==0) $laenge = 1;
            for ( $x = 0; $x < $laenge; $x++ )
                {if (is_array($feld_wert)) $feld_wert = $feld_wert[$x];
                $feld_bezeichnung_tmp = $feld_bezeichnung." ".($x+1);
                $html_input .= "<tr><td$bez_style><b>".$feld_bezeichnung.":</b>".$tmp_code;
                if ($feld_wert!="" AND file_exists($img_folder.'/'.$feld_wert))
                    {$html_input .= "<input type='hidden' name='$feld_name' value='$feld_wert'><img src='$img_folder/$feld_wert' width='110px' style='margin-right:10px;'>".olz_buttons("button".$db_table,array(array($feld_bezeichnung." entfernen","")),"");
                    }
                else
                    {if ($feld_wert!="") $html_input .= "<div class='error'>Datei ".$feld_wert." nicht gefunden.</div>";
                    $html_input .= "<input type='file' name='".$feld_name."' class='button'>";
                    }
                $html_input .= $feld_spezial.$$var_alert.$feld_kommentar."</td></tr>\n";
                }
            }
        elseif ($feld_typ == "file") //Input-Typ 'file'
            {include 'library/phpWebFileManager/icons.inc.php';
            $ext = end(explode(".",$feld_wert));
            $ext = $fm_cfg['icons']['ext'][$ext];
            if ($ext!="") $icon = "<img src='icons/".$ext."' class='noborder' style='margin-right:10px;vertical-align:middle;'>";
            else $icon = "";
            if ($feld_stil=="") $feld_stil = "style='width:95%;'";
            if (is_array($feld_wert)) $feld_wert = $feld_wert[$x];
            $feld_bezeichnung_tmp = $feld_bezeichnung." ".($x+1);
            $html_input .= "<tr><td$bez_style><b>".$feld_bezeichnung.":</b>".$tmp_code;
            if (file_exists($def_folder."/".$feld_wert)) $file_folder = $def_folder;
            elseif (file_exists($tmp_folder."/".$feld_wert)) $file_folder = $tmp_folder;
            else $file_folder = "";
            if ($feld_wert!="" AND file_exists($file_folder."/".$feld_wert))
                {$html_input .= "<input type='hidden' name='$feld_name' value='$feld_wert'>$icon<a href='$file_folder/$feld_wert' style='vertical-align:bottom;margin-right:20px;'>$feld_wert</a>".olz_buttons("button".$db_table,array(array($feld_bezeichnung." entfernen","2")),"");
                }
            else
                {if ($feld_wert!="") $html_input .= "<div class='error'>Datei ".$feld_wert." nicht gefunden.</div>";
                $html_input .= "<input type='file' name='".$feld_name."' style='width:100%;'>";
                }
            $html_input .= $feld_spezial.$$var_alert.$feld_kommentar."</td></tr>\n";
            }
        */
        }

    if (isset($tables_img_dirs[$db_table])) $html_input .= "<tr><td colspan='2' style='padding:0px 5px 0px 5px;' class='tablebar'>Bilder</td></tr><tr><td colspan='2'>".olz_images_edit($db_table, $id)."</td></tr>";
    if (isset($tables_file_dirs[$db_table])) $html_input .= "<tr><td colspan='2' style='padding:0px 5px 0px 5px;' class='tablebar'>Dateien</td></tr><tr><td colspan='2'>".olz_files_edit($db_table, $id)."</td></tr>";

    if (isset($_SESSION['edit']['replace']))
        {$html_menu = "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Überschreiben","3"),array("Abbrechen","2")),"")."</div>";}
    elseif ($_SESSION['edit']['confirm']=="1")
        {$html_menu = "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Löschen","5"),array("Abbrechen","2")),"")."</div>";}
    elseif ($_SESSION['edit']['modus']=="neuedit")
        {$html_menu = "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Vorschau","3"),array("Abbrechen","2")),"")."</div>";}
    /*elseif ($db_table=="galerie")
        {$html_menu = "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Abbrechen","2"),array("Speichern","4")),"")."</div>";}*/
    else
        {$html_menu = "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Vorschau","3"),array("Löschen","5"),array("Abbrechen","2")),"")."</div>";}

    $_SESSION['edit']['vorschau'] = "0";
    }
//-------------------------------------------------------------
// Menü
//-------------------------------------------------------------
echo $html_menu;
if ($alert != "" ) echo "<div class='buttonbar'><span class='error'>".$alert."</span></div>";
$alert = "";
if ($html_input.$html_hidden>"") echo "<table class='liste'>".$html_input."</table>".$html_hidden;

?>
