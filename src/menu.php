<?php

// =============================================================================
// Das Navigationsmenu der Website.
// =============================================================================

require_once __DIR__.'/config/paths.php';

$fontsize = "16";
$menu = [
    ["Startseite", "1", $fontsize], // Menüpunkt ('Name','Link')
    ["", "", 1],
    ["Aktuell", "2", $fontsize],
    ["Leistungssport", "7", $fontsize],
    ["Termine", "3", $fontsize],
    ["", "", 1],
    ["Galerie", "4", $fontsize],
    ["Forum", "5", $fontsize],
    ["Karten", "12", $fontsize],
    ["", "", 1],
    ["Material & Kleider", "21", $fontsize],
    ["Service", "8", $fontsize],
    //array("Anmeldungen","13",15),
    ["Kontakt", "6", $fontsize],
    ["", "", 1],
];

echo "<div id='menu' class='menu'>";
// LIVE-RESULTATE
$live_json_path = "{$data_path}/results/_live.json";
if (is_file($live_json_path)) {
    $content = file_get_contents($live_json_path);
    if ($content) {
        $live = json_decode($content, true);
        $last_updated_at = strtotime($live['last_updated_at']);
        $now = strtotime(date('Y-m-d H:i:s'));
        if ($live && $last_updated_at > $now - 3600) {
            echo "<div".(preg_match('/test/', $live['file']) ? " style='display:none;'" : "")."><a href='{$code_href}results/?file=".$live['file']."'><div style='color:#550000;background-color:#cc0000;font-weight:bold;font-size:".floor($fontsize)."px;padding:".floor($fontsize / 3)."px 5px ".floor($fontsize / 3)."px 10px;margin:0px;border-top:1px solid #550000;' onmouseover='colorFade(\"menulive\",\"background\",\"cc0000\",\"ee0000\",\"2\",\"10\");' onmouseout='colorFade(\"menulive\",\"background\",\"ee0000\",\"cc0000\",\"10\",\"75\");' id='menulive'>Live-Resultate</div></a></div>";
        }
    }
}
// ADMIN
if (in_array('ftp', preg_split("/ /", $_SESSION['auth'])) or ($_SESSION['auth'] == 'all')) {
    array_push($menu, ["WebFTP", "ftp", 11]);
}
if ($_SESSION['auth'] == 'all') {
    array_push($menu, ["Online-Resultate", "16", 11]);
}
if ($_SESSION['auth'] == 'all') {
    array_push($menu, ["SVG-Editor", "17", 11]);
}
if (isset($_SESSION["auth"])) {
    array_push($menu, ["Logout", "Logout", 11]);
} else {
    array_push($menu, ["Admin", "10", 11]);
}
echomenu($menu, "mainmenu");

function color($red, $green, $blue) {
    $redstelle1 = $red % 16;
    $redstelle2 = round(($red - $redstelle1) / 16, 0);
    $greenstelle1 = $green % 16;
    $greenstelle2 = round(($green - $greenstelle1) / 16, 0);
    $bluestelle1 = $blue % 16;
    $bluestelle2 = round(($blue - $bluestelle1) / 16, 0);
    $hexvalues = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f"];
    return $hexvalues[$redstelle2].$hexvalues[$redstelle1].$hexvalues[$greenstelle2].$hexvalues[$greenstelle1].$hexvalues[$bluestelle2].$hexvalues[$bluestelle1];
}
/*
function color ($red,$green,$blue) {
    $redstelle1 = $red%16;
    $redstelle2 = round(($red-$redstelle1)/16,0);
    $greenstelle1 = $green%16;
    $greenstelle2 = round(($green-$greenstelle1)/16,0);
    $bluestelle1 = $blue%16;
    $bluestelle2 = round(($blue-$bluestelle1)/16,0);
    $hexvalues = array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");
    return "rgb(".floor($red).",".floor($green).",".floor($blue).")";
}
*/
echo "<form name='Suche' method='post' action='index.php?page=9' style='white-space:nowrap; width:180px;'>
<input type='text' name='search_key' style='width:172px;color:#888888;padding:3px;background-color:#eeeeee;border:1px solid #aaaaaa;margin-top:2em;' title='Suche auf olzimmerberg.ch' value='Suchen...' onfocus='this.form.search_key.style.color = \"#006516\"; this.form.search_key.value = \"\"; ' onblur='this.form.search_key.style.color = \"#888888\"; this.form.search_key.value = \"Suchen...\"; '>
</form>";
echo "<div style='padding:2em 0.5em 0em 0.5em;'>
<script type='text/javascript'>document.write(MailTo(\"olz_uu_01\", \"olzimmerberg.ch\", \"webmaster\", \"Homepage%20OL%20Zimmerberg\"));</script>
<a href='https://www.facebook.com/olzimmerberg' target='_blank' title='OL Zimmerberg auf Facebook' style='float:right;'><img src='icns/facebook_16.png' alt='f' class='noborder' /></a>
<a href='https://www.youtube.com/channel/UCMhMdPRJOqdXHlmB9kEpmXQ' target='_blank' title='OL Zimmerberg auf YouTube' style='float:right; margin-right: 8px;'><img src='icns/youtube_16.png' alt='Y' class='noborder' /></a>
<a href='https://github.com/olzimmerberg/olz-website' target='_blank' title='OL Zimmerberg auf GitHub' style='float:right; margin-right: 8px;'><img src='icns/github_16.png' alt='g' class='noborder' /></a>
</div>
</div>";
if (preg_match('/(\; MSIE (8|9|10|11))|(\; Trident)/', $_SERVER['HTTP_USER_AGENT'])) {
    echo "<script type='text/javascript'>
    document.getElementById(\"menu\").style.position = \"absolute\";
    var origy = document.getElementById(\"menu\").getBoundingClientRect().top;
    function menuonscroll(e) {
        document.getElementById(\"menu\").style.top = Math.max(window.pageYOffset-origy+20, 10)+\"px\";
    }
    document.onscroll = menuonscroll;
    </script>";
}
echo "<div style='width:180px;'>&nbsp;</div>";

function echomenu($menu, $identifier) {
    for ($i = 0; $i < count($menu); $i++) {
        $menupunkt = $menu[$i];
        $fontsize = $menupunkt[2];
        $green = round((($i + 0.5) / count($menu)) * 75 + 125, 0);
        $bgcolor = color(0, $green, 0);
        $redsel = 255 / 4;
        $greensel = $green + (255 - $green) / 2;
        $bgcolorhover = color($redsel, $greensel, 0);
        //$bgcolorhover = color(255,255,0); // gelb
        $redlin = 255 * 3 / 8;
        $greenlin = $green + (255 - $green) * 2 / 3;
        $bluelin = 255 * 1 / 8;
        $linecolor = color($redlin, $greenlin, $bluelin);
        $tag = "div";
        if ($_SESSION['page'] == $menupunkt[1]) {
            $color = "color:#".color(0, (($i + 0.5) / count($menu)) * 25, 0).";";
            $bgcolor = $bgcolorhover;
            $tag = "h1";
        } else {
            $color = "color:#".color(0, 25 + (($i + 0.5) / count($menu)) * 50, 0).";";
        }
        $border_tmp = "";
        if ($i == 0) {
            $border_tmp = " border-top:1px solid #".$linecolor.";";
        }
        if ($menupunkt[0] != "" && $menupunkt[1] != "") {
            echo "<div><a href='index.php?page=".$menupunkt[1]."' id='menu_a_page".$menupunkt[1]."'><".$tag." style='".$color."background-color:#".$bgcolor.";font-weight:bold;font-size:".floor($fontsize)."px;padding:".floor($fontsize / 3)."px 5px ".floor($fontsize / 3)."px 10px;margin:0px;border-bottom:1px solid #".$linecolor.";".$border_tmp."' onmouseover='colorFade(\"menu".$identifier.$i."\",\"background\",\"".$bgcolor."\",\"".$bgcolorhover."\",\"2\",\"10\");' onmouseout='colorFade(\"menu".$identifier.$i."\",\"background\",\"".$bgcolorhover."\",\"".$bgcolor."\",\"10\",\"75\");' id='menu".$identifier.$i."'>".$menupunkt[0]."</".$tag."></a></div>";
        } else {
            //echo "<div style='border-top:1px solid #".$bgcolor."; border-bottom:1px solid #".$linecolor.";'><div style='padding:".floor($fontsize/3)."px; margin:0px; border-top:1px solid #".$bgcolorhover."; border-bottom:1px solid #".$bgcolor.";'></div></div>";
            echo "<div style='background-color:#".$bgcolorhover.";height:3px;border-bottom:1px solid #".$linecolor.";'></div>";
        }
    }
}
