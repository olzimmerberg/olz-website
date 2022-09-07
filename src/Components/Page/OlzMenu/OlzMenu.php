<?php

// =============================================================================
// Das Navigationsmenu der Website.
// =============================================================================

namespace Olz\Components\Page\OlzMenu;

class OlzMenu {
    public static function render($args = []) {
        global $_CONFIG;
        $out = '';

        require_once __DIR__.'/../../../../_/config/date.php';
        require_once __DIR__.'/../../../../_/config/server.php';

        $menu = [
            ["Startseite", "startseite.php", 'large'], // Menüpunkt ('Name','Link')
            ["", "", ''],
            ["Aktuell", "aktuell.php", 'large'],
            ["Leistungssport", "blog.php", 'large'],
            ["Termine", "termine.php", 'large'],
            ["", "", ''],
            ["Galerie", "galerie.php", 'large'],
            ["Forum", "forum.php", 'large'],
            ["Karten", "karten.php", 'large'],
            ["", "", ''],
            ["Material & Kleider", "material.php", 'large'],
            ["Service", "service.php", 'large'],
            ["Verein", "verein.php", 'large'],
        ];

        $out .= "<div id='menu' class='menu'>";
        // LIVE-RESULTATE
        $live_json_path = "{$_CONFIG->getDataPath()}results/_live.json";
        if (is_file($live_json_path)) {
            $content = file_get_contents($live_json_path);
            if ($content) {
                $live = json_decode($content, true);
                $last_updated_at = strtotime($live['last_updated_at']);
                $now = strtotime(olz_current_date('Y-m-d H:i:s'));
                if ($live && $last_updated_at > $now - 3600) {
                    $out .= "<a href='{$_CONFIG->getCodeHref()}apps/resultate/?file=".$live['file']."' ".(preg_match('/test/', $live['file']) ? " style='display:none;'" : "")." class='menu-link font-size-large' id='live-results-link'><div style='color:#550000;background-color:#cc0000;border-top:1px solid #550000;' onmouseover='colorFade(\"menulive\",\"background\",\"cc0000\",\"ee0000\",\"2\",\"10\");' onmouseout='colorFade(\"menulive\",\"background\",\"ee0000\",\"cc0000\",\"10\",\"75\");' id='menulive'>Live-Resultate</div></a>";
                }
            }
        }
        $out .= self::getMenu($menu, "mainmenu", $_CONFIG);

        $out .= "<form name='Suche' method='get' action='suche.php'>
        <input type='text' name='anfrage' id='site-search' title='Suche auf olzimmerberg.ch' value='Suchen...' onfocus='this.form.anfrage.style.color = \"#006516\"; this.form.anfrage.value = \"\"; ' onblur='this.form.anfrage.style.color = \"#888888\"; this.form.anfrage.value = \"Suchen...\"; '>
        </form>";
        $out .= "<div class='sysadmin-mail'>
        <script type='text/javascript'>MailTo(\"website\", \"olzimmerberg.ch\", \"sysadmin\", \"Homepage%20OL%20Zimmerberg\");</script>
        </div>
        <div class='platform-links'>
        <a href='https://github.com/olzimmerberg/olz-website' target='_blank' rel='noreferrer noopener' title='OL Zimmerberg auf GitHub' class='platform-link'>
            <img src='{$_CONFIG->getCodeHref()}icns/github_16.svg' alt='g' class='noborder' />
        </a>
        <a href='https://www.youtube.com/channel/UCMhMdPRJOqdXHlmB9kEpmXQ' target='_blank' rel='noreferrer noopener' title='OL Zimmerberg auf YouTube' class='platform-link'>
            <img src='{$_CONFIG->getCodeHref()}icns/youtube_16.svg' alt='Y' class='noborder' />
        </a>
        <a href='https://www.facebook.com/olzimmerberg' target='_blank' rel='noreferrer noopener' title='OL Zimmerberg auf Facebook' class='platform-link'>
            <img src='{$_CONFIG->getCodeHref()}icns/facebook_16.svg' alt='f' class='noborder' />
        </a>
        <a href='https://www.strava.com/clubs/olzimmerberg' target='_blank' rel='noreferrer noopener' title='OL Zimmerberg auf Strava' class='platform-link'>
            <img src='{$_CONFIG->getCodeHref()}icns/strava_16.svg' alt='s' class='noborder' />
        </a>
        </div>
        </div>";

        return $out;
    }

    protected static function getMenu($menu, $identifier, $_CONFIG): string {
        $out = '';
        for ($i = 0; $i < count($menu); $i++) {
            $menupunkt = $menu[$i];
            $fontsize = $menupunkt[2];
            $green = round((($i + 0.5) / count($menu)) * 75 + 125, 0);
            $bgcolor = self::color(0, $green, 0);
            $redsel = 255 / 4;
            $greensel = $green + (255 - $green) / 2;
            $bgcolorhover = self::color($redsel, $greensel, 0);
            // $bgcolorhover = self::color(255,255,0); // gelb
            $redlin = 255 * 3 / 8;
            $greenlin = $green + (255 - $green) * 2 / 3;
            $bluelin = 255 * 1 / 8;
            $linecolor = self::color($redlin, $greenlin, $bluelin);
            $tag = "div";
            if (
                ($_SESSION['page'] ?? null) == $menupunkt[1]
                || basename($_SERVER['SCRIPT_FILENAME'] ?? '') == $menupunkt[1]
            ) {
                $color = "color:#".self::color(0, (($i + 0.5) / count($menu)) * 25, 0).";";
                $bgcolor = $bgcolorhover;
                $tag = "h1";
            } else {
                $color = "color:#".self::color(0, 25 + (($i + 0.5) / count($menu)) * 50, 0).";";
            }
            $border_tmp = "";
            if ($i == 0) {
                $border_tmp = " border-top:1px solid #".$linecolor.";";
            }
            if ($menupunkt[0] != "" && $menupunkt[1] != "") {
                $out .= "<a href='".$_CONFIG->getCodeHref().$menupunkt[1]."' id='menu_a_page_".$menupunkt[1]."' class='menu-link font-size-{$fontsize}'><".$tag." style='".$color."background-color:#".$bgcolor.";border-bottom:1px solid #".$linecolor.";".$border_tmp."' onmouseover='colorFade(\"menu".$identifier.$i."\",\"background\",\"".$bgcolor."\",\"".$bgcolorhover."\",\"2\",\"10\");' onmouseout='colorFade(\"menu".$identifier.$i."\",\"background\",\"".$bgcolorhover."\",\"".$bgcolor."\",\"10\",\"75\");' id='menu".$identifier.$i."'>".$menupunkt[0]."</".$tag."></a>";
            } else {
                // $out .= "<div style='border-top:1px solid #".$bgcolor."; border-bottom:1px solid #".$linecolor.";'><div style='padding:".floor($fontsize/3)."px; margin:0px; border-top:1px solid #".$bgcolorhover."; border-bottom:1px solid #".$bgcolor.";'></div></div>";
                $out .= "<div style='background-color:#".$bgcolorhover.";height:3px;border-bottom:1px solid #".$linecolor.";'></div>";
            }
        }
        return $out;
    }

    protected static function color($red_float, $green_float, $blue_float): string {
        $red = intval($red_float);
        $green = intval($green_float);
        $blue = intval($blue_float);
        $redstelle1 = $red % 16;
        $redstelle2 = round(($red - $redstelle1) / 16, 0);
        $greenstelle1 = $green % 16;
        $greenstelle2 = round(($green - $greenstelle1) / 16, 0);
        $bluestelle1 = $blue % 16;
        $bluestelle2 = round(($blue - $bluestelle1) / 16, 0);
        $hexvalues = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f"];
        return $hexvalues[$redstelle2].$hexvalues[$redstelle1].$hexvalues[$greenstelle2].$hexvalues[$greenstelle1].$hexvalues[$bluestelle2].$hexvalues[$bluestelle1];
    }
}
