<?php
session_start();

include_once "admin/check.php";
include_once "admin/olz_init.php";
include_once "admin/olz_functions.php";
include_once "tickers.php";

$pages = array(
    "0"=>array("error_l.php","error_r.php"),// TO DO
    "1"=>array("startseite_l.php","startseite_r.php"),
    "2"=>array("aktuell_l.php","aktuell_r.php"),
    "3"=>array("termine_l.php","termine_r.php"),
    "4"=>array("galerie_l.php","galerie_r.php"),
    "5"=>array("forum_l.php","forum_r.php"),
    "6"=>array("organigramm.php"),
    "7"=>array("blog_l.php","startseite_r.php"),
    "8"=>array("service_l.php","service_r.php"),
    "9"=>array("search_l.php","startseite_r.php"),
    "10"=>array("login_l.php","startseite_r.php"),
    "11"=>array("zimmerbergol_l.php","startseite_r.php"),
    "12"=>array("karten_l.php","karten_r.php"),
    "13"=>array("anmeldung_l.php","anmeldung_r.php"),
    "14"=>array("anm_felder_l.php","anm_felder_r.php"),
    "15"=>array("termine_tools.php"),
    "16"=>array("zol/index.php"),
    "17"=>array("svgeditor.php"),
    "18"=>array("fuer_einsteiger_l.php", "fuer_einsteiger_r.php"),
    "19"=>array("zol/karten.php"),
    "99"=>array("results.php","startseite_r.php"),
    "mail"=>array("divmail_l.php","divmail_r.php"),
    "ftp"=>array("library/phpWebFileManager/start.php"),
    "tools"=>array("termine_helper.php"),
    // Test
    "organigramm"=>array("vorstand_l.php","vorstand_r.php"),
);


// Seiten-Titel
$html_titel="";
if(isset($id) AND in_array($page,array("2","3","4","7")))
    {$table_tmp = array("","","aktuell","termine","galerie","","","blog");
    $sql = "SELECT titel FROM ".$table_tmp[$page]." WHERE id='$id'";
    $result = mysql_query($sql);
    while ($row = mysql_fetch_array($result))
        $html_titel = " - ".$row['titel'];
    }

//-----------------------------------------
// UPLOAD-GRÖSSE PRÜFEN
//-----------------------------------------
$POST_MAX_SIZE = ini_get("post_max_size");
$mul = substr($POST_MAX_SIZE, -1);
$mul = ($mul == "M" ? 1048576 : ($mul == "K" ? 1024 : ($mul == "G" ? 1073741824 : 1)));
if ($_SERVER["CONTENT_LENGTH"] > $mul*(int)$POST_MAX_SIZE && $POST_MAX_SIZE)
    {$button_name = "button".$_SESSION["edit"]["db_table"];
    $$button_name = $_SESSION["edit"]["button"];
    $alert = "Fehler: Upload-Datei ist zu gross (".round($_SERVER["CONTENT_LENGTH"]/pow(2,20),1)."MB). Maximale Dateigrösse ist ".$POST_MAX_SIZE."B.";
    }

if (isset($_SESSION["edit"]))
    {$button_name = "button".$_SESSION["edit"]["db_table"];
    if (!isset($$button_name) OR isset($id))
        {$alert = "Bearbeitung muss zuerst abgeschlossen werden.";
        $$button_name = $_SESSION["edit"]["button"];
        $id = $_SESSION["id"];
        $id_edit = $_SESSION["id_text"];
        }
    $page = $_SESSION["page"];
    }
if($unset=='unset') unset($_SESSION["edit"]);    
if (($button == "Login") OR ($page == "Logout")) check_nutzer();

if (($page == "14") AND (in_array(array("all","anm_felder") ,split(" ",$_SESSION["auth"])))) $page = "1";
if ($page == "") $page = $_SESSION["page"];
if ($page == "") $page = "1";
if (($page == "10") AND ($_SESSION["versuch"]>$maxversuche)) $page = $_SESSION["page"]; // Login zu viele Versuche
if (($page == "10") AND isset($_SESSION['auth'])) $page = $_SESSION["page"]; // bereits eingeloggt
if (!is_numeric($page) AND !in_array($page,split(" ",$_SESSION["auth"])) AND ($_SESSION["auth"] != "all")) // Adminseiten
    {if (is_numeric($_SESSION["page"])) $page = $_SESSION["page"]; // zurück zur letzten Seite
    else $page = 1; // zurück zu Seite 1
    }
if ($page==16 AND $_SESSION['auth']!="all") $page = $_SESSION["page"];
if ($page != "10") $_SESSION["page"] = $page;
if ($pages[$page][0]=='') $page = 0;
// Win-IE Weiche
if (eregi("MSIE",$_SERVER["HTTP_USER_AGENT"]) AND eregi("Win",$_SERVER["HTTP_USER_AGENT"])) {
    $bildart = "gif";
} else {
    $bildart = "png";
}

//WebFTP-Zugriff  prüfen (Berechtigung und Root-Verzeichnis)
if($page=='ftp'){
    if(in_array('ftp' ,split(' ',$_SESSION['auth'])) OR $_SESSION['auth']=='all'){
        $var = (isset($_POST['fm_dir']) || isset($_GET['fm_dir'])) ? $fm_dir : $_SESSION['root'];
        $var2 = explode('/',$var);
        $var = (substr($var,-3)=='/..') ? implode('/',array_splice($var2,0,count($var2)-2)) : $var; // Übergeordnetes Verzeichnis
        //header('Authorization: Basic '.base64_encode('web276' . ":" . '123456'));
        //include 'library/phpWebFileManager/plugins/auth.php';

        if(isset($_POST['fm_dir'])){
            if(substr($var,0,strlen($_SESSION['root']))!==$_SESSION['root']){
                $fm_error = "<div class='error'>Keine Berechtigung für diese Funktion</div>";
                $_POST['fm_dir'] = $_SESSION['root'];
                }
            }
        elseif(isset($_GET['fm_dir'])){
            if(substr($var,0,strlen($_SESSION['root']))!==$_SESSION['root']){
                $fm_error = "<div class='error'>Keine Berechtigung für diese Funktion</div>";
                $_GET['fm_dir'] = $_SESSION['root'];
                }
            }
        else $_GET['fm_dir'] = $var;
        
    if($_SESSION['user']=='olzkarten'){
        $var = $_GET['fm_action'];

    if(in_array($var,array('confirm_rename_file','confirm_rename_directory','confirm_delete_file','confirm_remove_directory'))){
            $_GET['fm_action'] = "";
            $_GET['fm_filename'] = "";
            $fm_error = "<div class='error'>Keine Berechtigung für diese Funktion</div>";
            }
        }
    }
    // Datei herunterladen
    if($ftp_mode=='get_file'){
        $pfad = "http://".$ftp_user.":".$ftp_pw."@".substr($_GET['pfad'],7);
        header('Location: '.$pfad);
    }

}
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"
        \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
<meta http-equiv='content-type' content='text/html;charset=utf-8'>
<meta name='Keywords' content='OL Orientierungslauf Zimmerberg'>
<meta name='Description' content='Homepage der OrientierungsläuferInnen Zimmerberg'>
<meta name='Content-Language' content='de'>
<title>OL Zimmerberg$html_titel</title>
<link rel='stylesheet' type='text/css' href='styles.css'>
<link rel='stylesheet' type='text/css' href='library/lightview/css/lightview.css'>
<link rel='stylesheet' type='text/css' href='library/datepicker/datepicker.css'>
<link rel='shortcut icon' href='".$root_path."favicon.ico'>
<script type='text/javascript' src='library/datepicker/datepicker.js'></script>
<script type='text/javascript' src='scripts/jscripts.js'></script>
<script type='text/javascript' src='scripts/fader.js'></script>
<script type='text/javascript' src='scripts/accordion.js'></script>
<script type='text/javascript' src='scripts/xns.js'></script>";


echo "</head>";
if (preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT']) OR preg_match('/Win/i',$_SERVER['HTTP_USER_AGENT']))  echo "<body style='font-size:70%' onload='init()'>\n";
else echo "<body style='font-size:100%;' onload='init()'>\n";

// Muss im Body sein, sonst gibt es Probleme mit Bilderpfaden
echo "<script type='text/javascript' src='library/lightview/js/prototype.js'></script>
<script type='text/javascript' src='library/lightview/js/scriptaculous.js'></script>
<script type='text/javascript' src='library/lightview/js/lightview.js'></script>";

//<body class='layout' onload='init()'>
echo "<a name='top'></a>
<div style='background-image:url(icns/headerbg.png); background-repeat:repeat-x;'>
<div style='max-width:1200px; margin-left:auto; margin-right:auto; height:101%;'>


<table style='border-spacing:9px; background-image:url(icns/headerbg.png); background-repeat:repeat-x;'>
    <tr>
        <td style='height:150px;'>
            <table style='height:130px;'>
                <tr>
                    <td style='width:240px;vertical-align:middle;'><img src='icns/olzschatten.".$bildart."' alt='' class='noborder'></td>
                    <td style='vertical-align:middle;'>";
include "header.php";
echo "                </td>
                </tr>
            </table>
        </td>
    </tr>


    <tr>
        <td>
            <table id='content_wrapper'>
                <tr style='height:600px;'>
                    <td id='content_menu'>";
include "menu.php";
echo "                </td>";

// 2-spaltiges Layout
if (count($pages[$page])==2) {
    echo "<td id='content_mitte' style='position:relative;z-index:1;'>
<form name='Formularl' method='post' action='index.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data' onsubmit='passwort.value = hex_md5(passwort.value+challenge.value)'>";
    //if ($pages[$page][1]) include $pages[$page][0];
    include $pages[$page][0];
    echo "</form>
</td>
<td id='content_rechts' style='position:relative;z-index:2;'>
<form name='Formularr' method='post' action='index.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data' onsubmit='passwort.value = hex_md5(passwort.value+challenge.value)'>
<div>";
    //if ($pages[$page][1]) include $pages[$page][2];
    include $pages[$page][1];
    echo "</div>
</form>
</td>";
} 

// 1-spaltiges Layout
else {
    echo "<td id='content_double'>
<form name='Formularl' method='post' action='index.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data' onsubmit='passwort.value = hex_md5(passwort.value+challenge.value)'>
<div>";
    //if ($pages[$page][1]) include $pages[$page][0];
    include $pages[$page][0];
    echo "$fm_error</div>
</form>
</td>";
}

echo "            </tr>
            </table>
        </td>
    </tr>
</table>
<br>
</div>
</div>
</body>
</html>";

include "admin/counter.php";
/*

//MOMENTAN DEAKTIVIERT

function zufallsbild () {
    global $conn_id,$root_path;
    // ZUFALLSBILD
    $pfad_galerie = "galerie/";
    $gal_table = "galerie";
    mt_srand((double)microtime() * 1000000);
    //Filme, Diashow ausschliessen
    do
        {$result = mysql_query("SELECT * FROM $gal_table ORDER BY RAND() LIMIT 1",$conn_id);
        $row = mysql_fetch_array($result);
        }
    while ($row["typ"] == "movie");
        
    $datum_tmp =  $row["datum"];
    $gal_titel =  $row["titel"];
    $groesse =  $row["groesse"];
    
    $foto_datum = strftime("%y%m%d",strtotime($datum_tmp));
    $rand_pic = mt_rand(1, $groesse);
    $foto_000 = str_pad($rand_pic ,3, "0", STR_PAD_LEFT);
    
    return "<a href='index.php?page=4&amp;datum=" . $datum_tmp . "&amp;foto=" . $rand_pic . "'><img src='".$root_path."".$pfad_galerie."foto" . $foto_datum . "/thumb/".$foto_datum."_th_" . $foto_000 . ".jpg' title='" . $gal_titel . "' alt=''></a>";
}
*/
?>