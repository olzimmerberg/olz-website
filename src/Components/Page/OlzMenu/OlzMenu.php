<?php

// =============================================================================
// Das Navigationsmenu der Website.
// =============================================================================

namespace Olz\Components\Page\OlzMenu;

use Olz\Components\Common\OlzComponent;

class OlzMenu extends OlzComponent {
    public function getHtml($args = []): string {
        $out = '';

        $code_href = $this->envUtils()->getCodeHref();
        $data_href = $this->envUtils()->getDataHref();
        $data_path = $this->envUtils()->getDataPath();

        $menu = [
            ["Startseite", "", 'large'], // Menüpunkt ('Name','Link')
            ["", "", ''],
            ["News", "news", 'large'],
            ["Termine", "termine.php", 'large'],
            ["", "", ''],
            ["Karten", "karten.php", 'large'],
            ["Material & Kleider", "material.php", 'large'],
            ["", "", ''],
            ["Service", "service.php", 'large'],
            ["Verein", "verein.php", 'large'],
        ];

        $out .= "<div id='menu' class='menu'>";

        // BACK-BUTTON
        $back_link = $args['back_link'] ?? null;
        if ($back_link !== null) {
            $out .= "<a href='{$back_link}' class='menu-link font-size-large' id='back-link'><div class='menutag' style='color:#000000;background-color:#00c500;border-bottom:1px solid #5feb1f;' onmouseover='document.getElementById(\"menuback\").style.backgroundColor = &quot;#3fe200&quot;;' onmouseout='document.getElementById(\"menuback\").style.backgroundColor = &quot;#00c500&quot;;' id='menuback'><img src='{$data_href}icns/back_16.svg' alt='&lt;' class='noborder back-icon'>Zurück</div></a>";
        }

        // LIVE-RESULTATE
        $live_json_path = "{$data_path}results/_live.json";
        if (is_file($live_json_path)) {
            $content = file_get_contents($live_json_path);
            if ($content) {
                $live = json_decode($content, true);
                $last_updated_at = strtotime($live['last_updated_at']);
                $now = strtotime($this->dateUtils()->getIsoNow());
                if ($live && $last_updated_at > $now - 3600) {
                    $out .= "<a href='{$code_href}apps/resultate/?file=".$live['file']."' ".(preg_match('/test/', $live['file']) ? " style='display:none;'" : "")." class='menu-link font-size-large' id='live-results-link'><div class='menutag' style='color:#550000;background-color:#cc0000;border-top:1px solid #550000;' onmouseover='document.getElementById(\"menulive\").style.backgroundColor = &quot;#ee0000&quot;;' onmouseout='document.getElementById(\"menulive\").style.backgroundColor = &quot;#cc0000&quot;;' id='menulive'>Live-Resultate</div></a>";
                }
            }
        }
        $out .= self::getMenu($menu, "mainmenu", $code_href);

        $out .= "<form name='Suche' method='get' action='suche.php'>
        <input type='text' name='anfrage' id='site-search' title='Suche auf olzimmerberg.ch' value='Suchen...' onfocus='this.form.anfrage.style.color = \"#006516\"; this.form.anfrage.value = \"\"; ' onblur='this.form.anfrage.style.color = \"#888888\"; this.form.anfrage.value = \"Suchen...\"; '>
        </form>";
        $out .= "<div class='sysadmin-mail'>
        <script type='text/javascript'>olz.MailTo(\"website\", \"olzimmerberg.ch\", \"sysadmin\", \"Homepage%20OL%20Zimmerberg\");</script>
        </div>
        <div class='platform-links'>
        <a href='https://github.com/olzimmerberg/olz-website' target='_blank' rel='noreferrer noopener' title='OL Zimmerberg auf GitHub' class='platform-link'>
            <img src='{$code_href}icns/github_16.svg' alt='g' class='noborder' />
        </a>
        <a href='https://www.youtube.com/channel/UCMhMdPRJOqdXHlmB9kEpmXQ' target='_blank' rel='noreferrer noopener' title='OL Zimmerberg auf YouTube' class='platform-link'>
            <img src='{$code_href}icns/youtube_16.svg' alt='Y' class='noborder' />
        </a>
        <a href='https://www.facebook.com/olzimmerberg' target='_blank' rel='noreferrer noopener' title='OL Zimmerberg auf Facebook' class='platform-link'>
            <img src='{$code_href}icns/facebook_16.svg' alt='f' class='noborder' />
        </a>
        <a href='https://www.strava.com/clubs/olzimmerberg' target='_blank' rel='noreferrer noopener' title='OL Zimmerberg auf Strava' class='platform-link'>
            <img src='{$code_href}icns/strava_16.svg' alt='s' class='noborder' />
        </a>
        </div>
        </div>";

        return $out;
    }

    protected static function getMenu($menu, $identifier, $code_href): string {
        $out = '';
        for ($i = 0; $i < count($menu); $i++) {
            $menupunkt = $menu[$i];
            $fontsize = $menupunkt[2];
            $green = (($i + 0.5) / count($menu)) * 75 + 125;
            $bgcolor = self::color(0, $green, 0);
            $redsel = 255 / 4;
            $greensel = $green + (255 - $green) / 2;
            $bgcolorhover = self::color($redsel, $greensel, 0);
            $redlin = 255 * 3 / 8;
            $greenlin = $green + (255 - $green) * 2 / 3;
            $bluelin = 255 * 1 / 8;
            $linecolor = self::color($redlin, $greenlin, $bluelin);
            $tag = "div";
            $script_filename = basename($_SERVER['SCRIPT_FILENAME'] ?? '');
            $is_symfony_route = $script_filename === 'index.php';
            $request_uri = $_SERVER['REQUEST_URI'] ?? '';
            if ($is_symfony_route
                ? preg_match("/^\\/{$menupunkt[1]}(\\/|\\?|#|$)/", $request_uri)
                    || ($menupunkt[1] === '' && $request_uri === '')
                : $script_filename == $menupunkt[1]
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
            if ($menupunkt[0] != "") {
                $out .= "<a href='".$code_href.$menupunkt[1]."' id='menu_a_page_".$menupunkt[1]."' class='menu-link font-size-{$fontsize}'><".$tag." class='menutag' style='".$color."background-color:#".$bgcolor.";border-bottom:1px solid #".$linecolor.";".$border_tmp."' onmouseover='document.getElementById(\"menu".$identifier.$i."\").style.backgroundColor = &quot;#{$bgcolorhover}&quot;;' onmouseout='document.getElementById(\"menu".$identifier.$i."\").style.backgroundColor = &quot;#{$bgcolor}&quot;;' id='menu".$identifier.$i."'>".$menupunkt[0]."</".$tag."></a>";
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
