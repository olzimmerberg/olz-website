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
    "7"=>array("blog_l.php","blog_r.php"),
    "8"=>array("service_l.php","service_r.php"),
    "9"=>array("search_l.php","startseite_r.php"),
    "10"=>array("login_l.php","startseite_r.php"),
    "11"=>array("zimmerbergol_l.php","zimmerbergol_r.php"),
    "12"=>array("karten_l.php","karten_r.php"),
    "13"=>array("anmeldung_l.php","anmeldung_r.php"),
    "14"=>array("anm_felder_l.php","anm_felder_r.php"),
    "15"=>array("termine_tools_DEV.php"),
    "16"=>array("zol/index.php"),
    "18"=>array("fuer_einsteiger_l.php", "fuer_einsteiger_r.php"),
    "19"=>array("zol/karten.php"),
    "20"=>array("trophy2020.php"),
    "21"=>array("material.php"),
    "99"=>array("results.php","startseite_r.php"),
    "mail"=>array("divmail_l.php","divmail_r.php"),
    "ftp"=>array("library/phpWebFileManager/start.php"),
    "tools"=>array("termine_helper.php"),
    // Test
    "organigramm"=>array("vorstand_l.php","vorstand_r.php"),
);

//http://YOUR-SITE.COM/FILERUN/?page=login&action=login&nonajax=1&username=test&password=1234
// Seiten-Titel
$html_titel="";
if(isset($id) AND in_array($page,array("2","3","4","7")))
    {$table_tmp = array("","","aktuell","termine","galerie","","","blog");
    $sql = "SELECT titel FROM ".$table_tmp[$page]." WHERE id='$id'";
    $res = $db->query($sql);
    while ($row = $res->fetch_assoc())
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

//-----------------------------------------
// BEARBEITUNGS-STATUS PRÜFEN
//-----------------------------------------
if (isset($_SESSION["edit"]))
    {$button_name = "button".$_SESSION["edit"]["db_table"];
    if (!isset($$button_name) OR isset($id))
        {$alert = "Bearbeitung muss zuerst abgeschlossen werden.";
        $$button_name = $_SESSION["edit"]["button"];
        $db_table = $_SESSION["edit"]["db_table"];
        $id = $_SESSION[$db_table."id"];
        //$id = $_SESSION["id"];
        $id_edit = $_SESSION["id_text"];
        }
    $page = $_SESSION["page"];
    }
if($unset=='unset') unset($_SESSION["edit"]);
if (($button == "Login") OR ($page == "Logout")) check_nutzer();

if (($page == "14") AND (in_array(array("all","anm_felder") ,explode(" ",$_SESSION["auth"])))) $page = "1";
if ($page == "") $page = $_SESSION["page"];
if ($page == "") $page = "1";
if ($page == "zol" OR $page == 'n2' OR $page == 'wre') $page = "11";
if (($page == "10") AND ($_SESSION["versuch"]>$maxversuche)) $page = $_SESSION["page"]; // Login zu viele Versuche
if (($page == "10") AND isset($_SESSION['auth'])) $page = $_SESSION["page"]; // bereits eingeloggt
if (!is_numeric($page) AND !in_array($page,explode(" ",$_SESSION["auth"])) AND ($_SESSION["auth"] != "all")) // Adminseiten
    {if (is_numeric($_SESSION["page"])) $page = $_SESSION["page"]; // zurück zur letzten Seite
    else $page = 1; // zurück zu Seite 1
    }
if ($page==16 AND $_SESSION['auth']!="all") $page = $_SESSION["page"];
if ($page != "10") $_SESSION["page"] = $page;
if ($pages[$page][0]=='') $page = 0;
// Win-IE Weiche
if (preg_match("/MSIE/",$_SERVER["HTTP_USER_AGENT"]) AND preg_match("/Win/",$_SERVER["HTTP_USER_AGENT"])) {
    $bildart = "gif";
} else {
    $bildart = "png";
}
if($page==19) $refresh = "<meta http-equiv='refresh' content='60'>" ; // Stand Karten/Anmeldungen
else $refresh = "";

//-----------------------------------------
// WebFTP-Zugriff  prüfen (Berechtigung und Root-Verzeichnis)
//-----------------------------------------
if($page=='ftp'){
    if(in_array('ftp' ,explode(' ',$_SESSION['auth'])) OR $_SESSION['auth']=='all'){
        $var = (isset($_POST['fm_dir']) || isset($_GET['fm_dir'])) ? $fm_dir : $_SESSION['root'];
        $var2 = explode('/',$var);
        $var = (substr($var,-3)=='/..') ? implode('/',array_splice($var2,0,count($var2)-2)) : $var; // Übergeordnetes Verzeichnis
        if(isset($_POST['fm_dir'])){
            if(substr($var,0,strlen($_SESSION['root']))!==$_SESSION['root'] AND $_SESSION['auth']!='all'){
                $fm_error = "<div class='error'>Keine Berechtigung für diese Funktion</div>";
                $_POST['fm_dir'] = $_SESSION['root'];
                }
            }
        elseif(isset($_GET['fm_dir'])){
            if(substr($var,0,strlen($_SESSION['root']))!==$_SESSION['root'] AND $_SESSION['auth']!='all'){
                $fm_error = "<div class='error'>Keine Berechtigung für diese Funktion</div>";
                $_GET['fm_dir'] = $_SESSION['root'];
                }
            }
        else $_GET['fm_dir'] = $var;

    // User 'olzkarten' > darf Daten nicht umbenennen/löschen
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
        $pfad = "https://".$ftp_user.":".$ftp_pw."@".substr($_GET['pfad'],8);
        header('Location: '.$pfad);
    }

}
header('Cache-Control: max-age=600');
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"
        \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
<meta http-equiv='cache-control' content='public'>
<meta http-equiv='content-type' content='text/html;charset=utf-8'>
<meta name='Keywords' content='OL Orientierungslauf Zimmerberg'>
<meta name='Description' content='Homepage der OrientierungsläuferInnen Zimmerberg'>
<meta name='Content-Language' content='de'>".$refresh."
".(isset($_GET["archiv"])?"<meta name='robots' content='noindex, nofollow'>":"")."
<title>OL Zimmerberg$html_titel</title>
<link rel='stylesheet' type='text/css' href='styles.css'>
<link rel='stylesheet' type='text/css' href='library/lightview-3.4.0/css/lightview/lightview.css'>
<link rel='stylesheet' type='text/css' href='library/datepicker/datepicker.css'>
<link rel='shortcut icon' href='".$code_href."favicon.ico'>
<script type='text/javascript' src='library/datepicker/datepicker.js'></script>
<script type='text/javascript' src='scripts/jscripts.js'></script>
<script type='text/javascript' src='scripts/fader.js'></script>
<script type='text/javascript' src='scripts/accordion.js'></script>
<script type='text/javascript' src='scripts/xns.js'></script>
<script type='text/javascript' src='https://code.jquery.com/jquery-1.11.0.min.js'></script>
<!--[if lt IE 9]>
  <script type='text/javascript' src='library/lightview-3.4.0/js/excanvas/excanvas.js'></script>
<![endif]-->
<script type='text/javascript' src='library/lightview-3.4.0/js/spinners/spinners.min.js'></script>
<script type='text/javascript' src='library/lightview-3.4.0/js/lightview/lightview.js'></script>
<script type='text/javascript'>
if ( window.self !== window.top ) {
    window.top.location.href=window.location.href;
}
</script>";


echo "</head>";
if (preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT']) OR preg_match('/Win/i',$_SERVER['HTTP_USER_AGENT']))  echo "<body style='font-size:70%'>\n";
else echo "<body style='font-size:100%;'>\n";

//<body class='layout' onload='init()'>
echo "<a name='top'></a>
<div style='background-image:url(icns/headerbg.png); background-repeat:repeat-x;'>
<div style='max-width:1200px; margin-left:auto; margin-right:auto; height:101%;'>
<div style='position:relative; height:160px; padding:0px; background-image:url(icns/headerbg.png); background-repeat:repeat-x; overflow-x:auto; overflow-y:hidden;'><img src='icns/olzschatten.".$bildart."' alt='' style='float:left; margin-top:10px;' class='noborder' id='olzlogo'><div style='position:relative; height:150px; overflow:hidden;'>";
include "header.php";
echo "</div></div>

<!-- Beginn: OLZ 10 Jahre -->
<!--
<script>
var isMobileDevice = !!/android|Android|iPhone|iPad|iPod/.exec(navigator.userAgent);
var y10Init = function () {
    var olzimg = document.getElementById(\"olzlogo\");
    var parent = olzimg.parentElement;
    var y10img = document.createElement(\"img\");
    y10img.src = \"img/10jahre.png\";
    var onBothComplete = function () {
        try {
            var nextStepTimeout = false;
            var swapImagesTimeout = false;
            var olz10cnv = document.createElement(\"canvas\");
            olz10cnv.setAttribute(\"style\", \"float:left; margin:0px;\");
            parent.insertBefore(olz10cnv, olzimg);
            olz10cnv.width = 245;
            olz10cnv.height = 150;
            olz10cnv.style.width = \"245px\";
            olz10cnv.style.height = \"150px\";
            var ctx = olz10cnv.getContext(\"2d\");
            ctx.globalAlpha = 1;
            ctx.drawImage(olzimg, 0, 10);
            parent.removeChild(olzimg);
            var swapImages = function () {
                if (nextStepTimeout) window.clearTimeout(nextStepTimeout);
                if (swapImagesTimeout) window.clearTimeout(swapImagesTimeout);
                var numParticles = 50;
                var sizParticles = 0.8;
                var speedParticles = 1;
                var ps = {};
                for (var i=0; i<numParticles; i++) {
                    ps[i] = {};
                    ps[i].ux = Math.random();
                    ps[i].uy = Math.random();
                    ps[i].uz = Math.random();
                    var absU = Math.sqrt(ps[i].ux*ps[i].ux+ps[i].uy*ps[i].uy+ps[i].uz*ps[i].uz);
                    ps[i].ux = ps[i].ux/absU;
                    ps[i].uy = ps[i].uy/absU;
                    ps[i].uz = ps[i].uz/absU;
                    ps[i].posx = Math.random()*2-1;
                    ps[i].posx = ((Math.pow(Math.E, 8*ps[i].posx)-1)/(Math.pow(Math.E, 8*ps[i].posx)+1)*(Math.pow(Math.E, 8)+1)/(Math.pow(Math.E, 8)-1)+ps[i].posx)/2;
                    ps[i].posx = (ps[i].posx+1)*(olz10cnv.width/2-10)+10;
                    ps[i].posy = Math.random()*2*olz10cnv.height;
                    ps[i].speedy = (Math.random()+1)*olz10cnv.height;
                    ps[i].speedtheta = (Math.random()+1);
                    ps[i].phase = Math.random()*Math.PI*2;
                    ps[i].panini = paninis[Math.floor(Math.random()*numPaninis)];
                }
                var msPerStep = 75;
                var t0 = Date.now();
                var step = 0;
                var nextStep = function () {
                    if (nextStepTimeout) window.clearTimeout(nextStepTimeout);
                    var specialOpacity = 1;
                    if (step<30) specialOpacity = step/30;
                    else if (70<step) specialOpacity = (100-step)/30;
                    if (100<step) {
                        if (!isMobileDevice) swapImagesTimeout = setTimeout(swapImages, 10000);
                        return;
                    }
                    ctx.clearRect(0, 0, olz10cnv.width, olz10cnv.height);
                    ctx.globalAlpha = 0.5+Math.cos(specialOpacity*Math.PI)/2;
                    ctx.drawImage(olzimg, 0, 10);
                    ctx.globalAlpha = 0.5-Math.cos(specialOpacity*Math.PI)/2;
                    ctx.drawImage(y10img, 0, 10);
                    for (var i=0; i<numParticles; i++) {
                        var theta = step*ps[i].speedtheta/12+ps[i].phase;
                        var rMat00 = Math.cos(theta)+ps[i].ux*ps[i].ux*(1-Math.cos(theta));
                        var rMat01 = ps[i].ux*ps[i].uy*(1-Math.cos(theta))-ps[i].uz*Math.sin(theta);
                        var rMat02 = ps[i].ux*ps[i].uz*(1-Math.cos(theta))+ps[i].uy*Math.sin(theta);
                        var rMat10 = ps[i].ux*ps[i].uy*(1-Math.cos(theta))+ps[i].uz*Math.sin(theta);
                        var rMat11 = Math.cos(theta)+ps[i].uy*ps[i].uy*(1-Math.cos(theta));
                        var rMat12 = ps[i].uy*ps[i].uz*(1-Math.cos(theta))-ps[i].ux*Math.sin(theta);
                        //console.log(rMat00, rMat01, rMat02);
                        //console.log(rMat10, rMat11, rMat12);
                        var xPos = rMat02+ps[i].posx;
                        var yPos = rMat12+ps[i].posy+step*ps[i].speedy*speedParticles/100-olz10cnv.height;
                        ctx.fillStyle = \"rgb(0,150,0)\";
                        ctx.shadowColor = \"rgba(0,0,0,0.6)\";
                        ctx.shadowBlur = 3;
                        ctx.shadowOffsetX = 0;
                        ctx.shadowOffsetY = 1;
                        ctx.setTransform(rMat00, rMat10, rMat01, rMat11, xPos, yPos);
                        ctx.fillRect(-sizParticles*ps[i].panini.width/2, -sizParticles*ps[i].panini.height/2, sizParticles*ps[i].panini.width, sizParticles*ps[i].panini.height);
                        ctx.drawImage(ps[i].panini, -sizParticles*ps[i].panini.width/2, -sizParticles*ps[i].panini.height/2, sizParticles*ps[i].panini.width, sizParticles*ps[i].panini.height);
                    }
                    ctx.shadowColor = \"rgba(0,0,0,0)\";
                    ctx.setTransform(1, 0, 0, 1, 0, 0);
                    step++;
                    //var measuredMsPerStep = ((Date.now()-t0)/step);
                    var isTooSlow = (msPerStep*1.2<(Date.now()-t0)/(step+10));
                    //ctx.fillStyle = \"rgba(0,0,0,0.1)\";
                    //ctx.fillText(Math.round(measuredMsPerStep)+\"ms/step => \"+(isTooSlow?\" TOO SLOW!!!\":\"\"), 0, 10);
                    if (isTooSlow) step = 100;
                    nextStepTimeout = setTimeout(nextStep, msPerStep);
                };
                nextStep();
            };
            swapImagesTimeout = setTimeout(swapImages, 1000);
        } catch (err) {
            console.error(err);
        }
    };
    var numPaninis = 20;
    var paninis = {};
    for (var i=0; i<numPaninis; i++) {
        var str = \"000\"+Math.floor(Math.random()*125+1);
        while (paninis[str]) str = \"000\"+Math.floor(Math.random()*125+1);
        var panini = document.createElement(\"img\");
        panini.src = \"olz_mitglieder/panini2016_mini/\"+str.substr(str.length-3)+\".png\";
        panini.onload = (function (i, str) {return function () {
            paninis[i] = paninis[str];
            while (36<paninis[i].width) {
                var cnvtmp = document.createElement(\"canvas\");
                cnvtmp.width = paninis[i].width/2;
                cnvtmp.height = paninis[i].height/2;
                var ctxtmp = cnvtmp.getContext(\"2d\");
                ctxtmp.drawImage(paninis[i], 0, 0, cnvtmp.width, cnvtmp.height);
                paninis[i] = cnvtmp;
            }
            var complete = true;
            if (!olzimg.complete) complete = false;
            if (!y10img.complete) complete = false;
            for (var j=0; j<numPaninis; j++) {
                if (!paninis[j]) complete = false;
            }
            if (complete) onBothComplete();
        };})(i, str);
        paninis[str] = panini;
    }
    olzimg.onload = function () {
        var complete = true;
        if (!y10img.complete) complete = false;
        for (var i=0; i<numPaninis; i++) {
            if (!paninis[i]) complete = false;
        }
        if (complete) onBothComplete();
    };
    y10img.onload = function () {
        var complete = true;
        if (!olzimg.complete) complete = false;
        for (var i=0; i<numPaninis; i++) {
            if (!paninis[i]) complete = false;
        }
        if (complete) onBothComplete();
    };
};
if (window.addEventListener && !isMobileDevice) {
    window.addEventListener('load', y10Init);
}
</script> -->
<!-- Ende: OLZ 10 Jahre -->

<div id='content_wrapper'>
<div id='content_menu'>";
include "menu.php";
echo "</div>";

// 2-spaltiges Layout
if (count($pages[$page])==2) {
    echo "
<div id='content_rechts'>
<form name='Formularr' method='post' action='index.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data'>
<div>";
    include $pages[$page][1];
    echo "</div>
</form>
</div>
<div id='content_mitte'>
<form name='Formularl' method='post' action='index.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data'>";
    include $pages[$page][0];
    echo "</form>
</div>
";
}

// 1-spaltiges Layout
else {
    echo "<div id='content_double'>
<form name='Formularl' method='post' action='index.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data'>
<div>";
    include $pages[$page][0];
    echo "</div>
</form>
</div>";
}

echo "<div style='clear:both;'>&nbsp;</div></div>
</div>
</div>
</body>
</html>";

include "admin/counter.php";

?>
