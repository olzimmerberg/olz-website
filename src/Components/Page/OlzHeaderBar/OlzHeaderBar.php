<?php

// =============================================================================
// Die Kopfzeile der Website.
// =============================================================================

namespace Olz\Components\Page\OlzHeaderBar;

use Olz\Components\Auth\OlzAccountMenu\OlzAccountMenu;
use Olz\Components\Page\OlzMenu\OlzMenu;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\FileUtils;

class OlzHeaderBar {
    public static function render($args = []) {
        global $zugriff, $button_name;
        $out = '';

        require_once __DIR__.'/../../../../_/admin/olz_functions.php';

        $file_utils = FileUtils::fromEnv();
        $env_utils = EnvUtils::fromEnv();
        $code_href = $env_utils->getCodeHref();
        $data_href = $env_utils->getDataHref();

        $out .= "<div id='header-bar' class='header-bar menu-closed'>";

        $out .= "<div class='above-header'>";
        $out .= "<div class='account-menu-container'>";

        if (!($args['skip_auth_menu'] ?? false)) {
            $out .= OlzAccountMenu::render();
        }

        $out .= "</div>";
        $out .= "</div>";

        $out .= "<div class='below-header'>";
        $out .= "<div id='menu-container' class='menu-container'>";

        $out .= OlzMenu::render();

        $out .= "</div>"; // menu-container
        $out .= "</div>"; // below-header

        $out .= "<div id='menu-switch' onclick='toggleMenu()' />";
        $out .= "<img src='{$code_href}icns/menu_hamburger.svg' alt='' class='menu-hamburger noborder' />";
        $out .= "<img src='{$code_href}icns/menu_close.svg' alt='' class='menu-close noborder' />";
        $out .= "</div>";

        $out .= "<div class='header-content-container'>";
        $out .= "<div class='header-content-scroller'>";
        $out .= "<div class='header-content'>";

        // TODO: Remove switch as soon as Safari properly supports SVGs.
        if (preg_match('/Safari/i', $_SERVER['HTTP_USER_AGENT'] ?? '')) {
            $out .= "<img srcset='{$code_href}icns/olz_logo@2x.png 2x, {$code_href}icns/olz_logo.png 1x' src='{$code_href}icns/olz_logo.png' alt='' class='noborder' id='olz-logo' />";
        } else {
            $out .= "<img src='{$code_href}icns/olz_logo.svg' alt='' class='noborder' id='olz-logo' />";
        }
        $out .= "<div style='flex-grow:1;'></div>";

        if (!($args['skip_top_boxes'] ?? false)) {
            $db = DbUtils::fromEnv()->getDb();

            $header_spalten = 2;

            $db_table = "aktuell";
            $zugriff = ((($_SESSION['auth'] ?? null) == 'all') or in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? ''))) ? true : false;
            $button_name = 'button'.$db_table;
            if (isset($_GET[$button_name])) {
                $_POST[$button_name] = $_GET[$button_name];
            }
            if (isset($_POST[$button_name])) {
                $_SESSION['edit']['db_table'] = $db_table;
            }

            $sql = "SELECT * FROM {$db_table} WHERE (on_off != '0') AND (typ LIKE '%box%') ORDER BY typ ASC";
            $result = $db->query($sql);

            $ganze = [];
            while ($row = mysqli_fetch_array($result)) {
                $wichtig = substr($row["typ"], 3 + strpos(strtolower($row["typ"]), "box"));
                if ($wichtig == "" || !in_array($wichtig, [0, 1, 2])) {
                    $wichtig = 2;
                }

                // Dateicode einfügen
                $textlang = $row["textlang"];
                preg_match_all("/<datei([0-9]+)(\\s+text=(\"|\\')([^\"\\']+)(\"|\\'))?([^>]*)>/i", $textlang, $matches);
                // preg_match_all("/<datei([0-9]+)[^>]*(\s+file=(\"|\')([^\"\']+)(\"|\'))[^>]*>/i", $textlang, $matches_file);

                for ($i = 0; $i < count($matches[0]); $i++) {
                    $tmptext = $matches[4][$i];
                    $tmpfile = $matches_file[4][$i];
                    // if($_SESSION['auth']=='all') $out .= $i."***2".$matches_file[4][$i]."<br>";
                    if (mb_strlen($tmptext) < 1) {
                        $tmptext = "Datei ".$matches[1][$i];
                    }
                    $tmp_html = $file_utils->olzFile($db_table, $row["id"], intval($matches[1][$i]), $tmptext);
                    $textlang = str_replace($matches[0][$i], $tmp_html, $textlang);
                }

                $tmp = ["id" => $row["id"], "wichtig" => $wichtig, "titel" => $row["titel"], "textlang" => $textlang];
                array_push($ganze, $tmp);
            }

            $html_first_row = "";
            for ($i = 0; $i < $header_spalten; $i++) {
                $html_first_row = self::htmlbox($ganze[0], 1, $zugriff, $button_name).$html_first_row;
                array_splice($ganze, 0, 1);
            }

            $out .= $html_first_row;
        }

        // $out .= OlzHeaderJomCounter::render();

        // Nat. OL Weekend Davos Klosters
        $out .= "<div class='header-box'><a href='{$code_href}zimmerberg_ol/' target='_blank' id='weekend-link'><img src='{$data_href}img/zol_2022/logo_260.png' alt='Nationales OL-Weekend Davos Klosters 2022' /></a></div>";

        // OLZ Trophy 2017
        $out .= "<div class='header-box'><a href='trophy.php' id='trophy-link'><img src='{$data_href}img/trophy.png' alt='trophy' /></a></div>";

        $out .= "</div>"; // header-content
        $out .= "</div>"; // header-content-scroller
        $out .= "</div>"; // header-content-container
        $out .= "</div>"; // header-bar

        return $out;
    }

    protected static function htmlbox($entry, $typ, $zugriff, $button_name): string {
        $colors = ["dd0000", "00cc00", "005500"]; // Farbe Randbalken

        $edit_admin = ($zugriff) ? "<a href='aktuell.php?id=".$entry["id"]."&amp;".$button_name."=start' class='linkedit'>&nbsp;</a>" : "";
        if (!$entry) {
            return "<div class='box-ganz'>&nbsp;</div>";
        }
        $titel = ($entry["titel"] != "") ? $edit_admin.$entry["titel"] : ""; // Wieso???
        return "<div class='header-box box-ganz'><div style='display: flow-root; border-color:#".$colors[$entry["wichtig"]].";'><h3 style='margin-top: 0;'>".$titel."</h3><div style='padding:0px 5px;' class='box-content'>".olz_br($entry["textlang"])."</div></div></div>";
    }
}
