<?php

namespace Olz\Components\OtherPages\OlzService;

use Olz\Components\Apps\OlzAppsList\OlzAppsList;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Role;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\FileUtils;

class OlzService extends OlzComponent {
    public function getHtml($args = []): string {
        global $_GET, $_POST, $_SESSION, $function, $db_table;
        $entityManager = $this->dbUtils()->getEntityManager();
        $role_repo = $entityManager->getRepository(Role::class);
        $website_role = $role_repo->findOneBy(['username' => 'website']);
        $out = '';

        require_once __DIR__.'/../../../../_/admin/olz_functions.php';

        $out .= OlzHeader::render([
            'title' => "Service",
            'description' => "Diverse Online-Tools rund um OL und die OL Zimmerberg.",
        ]);

        $out .= "<div class='content-full'>";

        $db = DbUtils::fromEnv()->getDb();
        $code_href = EnvUtils::fromEnv()->getCodeHref();
        $file_utils = FileUtils::fromEnv();

        $out .= "<form name='Formularl' method='post' action='{$code_href}service#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";

        $out .= "<h1>Service</h1>";
        $out .= "<h2>Apps</h2>";
        $out .= OlzAppsList::render();
        $out .= "<br /><br />";

        $out .= "<div class='responsive-flex'>";
        $out .= "<div class='responsive-flex-2'>";
        $out .= "<h2>Links</h2>";

        $db_table = 'links';

        // -------------------------------------------------------------
        // ZUGRIFF
        if ((($_SESSION['auth'] ?? null) == 'all') or in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? ''))) {
            $zugriff = "1";
        } else {
            $zugriff = "0";
        }
        $button_name = 'button'.$db_table;
        if (isset($_GET[$button_name])) {
            $_POST[$button_name] = $_GET[$button_name];
            $id = $_GET['id'] ?? null;
        }
        if (isset($_POST[$button_name])) {
            $_SESSION['edit']['db_table'] = $db_table;
        }

        // -------------------------------------------------------------
        // USERVARIABLEN PRÜFEN
        if (isset($id) and is_ganzzahl($id) and ($_SESSION['edit']['db_table'] == $db_table)) {
            $_SESSION[$db_table."id_"] = $id;
        }
        // $id = $_SESSION[$db_table.'id_'] ?? null;

        // -------------------------------------------------------------
        // BEARBEITEN
        if ($zugriff) {
            $functions = ['neu' => 'Neuer Eintrag',
                'edit' => 'Bearbeiten',
                'abbruch' => 'Abbrechen',
                'vorschau' => 'Vorschau',
                'save' => 'Speichern',
                'delete' => 'Löschen',
                'start' => 'start',
                'undo' => 'undo',
                'up' => 'up',
                'down' => 'down',
                'zurück' => 'Zurück', ];
        } else {
            $functions = [];
        }
        $function = array_search($_POST[$button_name] ?? null, $functions);
        if ($function != "") {
            ob_start();
            include __DIR__.'/../../../../_/admin/admin_db.php';
            $out .= ob_get_contents();
            ob_end_clean();
        }
        if (($_SESSION['edit']['table'] ?? null) == $db_table) {
            $db_edit = "1";
        } else {
            $db_edit = "0";
        }
        // $_SESSION[$db_table."id_"] = $id;

        // -------------------------------------------------------------
        // MENÜ
        if ($zugriff and $db_edit == "0") {
            if (($alert ?? '') != '') {
                $out .= "<div class='buttonbar'><span class='error'>".$alert."</span></div>\n";
            }
            $out .= "<div class='buttonbar'>".\olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")."</div>";
        }

        // -------------------------------------------------------------
        //  VORSCHAU - LISTE
        $sql = "SELECT * from {$db_table} ORDER BY position";
        $result = $db->query($sql);
        $next_pos = mysqli_num_rows($result) + 1;

        if ($zugriff) {
            if ($db_edit == "1") {
                $sql = "SELECT * FROM {$db_table} WHERE (id='".$_SESSION[$db_table."id_"]."') ORDER BY position ASC";
            } else {
                $sql = "SELECT * FROM {$db_table} ORDER BY  position ASC";
            }
        } else {
            $sql = "SELECT * FROM {$db_table} WHERE (on_off='1') ORDER BY  position ASC";
        }

        if (($db_edit == "0") or (($do ?? null) == 'vorschau')) {
            $result = $db->query($sql);
            $out .= "<ul class='nobox'>";
            while ($row = mysqli_fetch_array($result)) {
                if (($do ?? null) == 'vorschau') {
                    $row = $vorschau;
                }
                $id_tmp = $row['id'];
                $name = $row['name'];
                $url = $row['url'];
                $on_off = $row['on_off'];
                if ($zugriff and (($do ?? null) != 'vorschau')) {
                    $edit_admin = "<a href='{$code_href}service?id={$id_tmp}&amp;{$button_name}=up' style='margin-right:4px;'><img src='{$code_href}assets/icns/up_16.svg' class='noborder'></a><a href='{$code_href}service?id={$id_tmp}&amp;{$button_name}=down' style='margin-right:4px;'><img src='{$code_href}assets/icns/down_16.svg' class='noborder'></a><a href='{$code_href}service?id={$id_tmp}&{$button_name}=start' class='linkedit'>&nbsp;</a>";
                } else {
                    $edit_admin = "";
                }

                if ($on_off == 0) {
                    $class = " class='error'";
                } else {
                    $class = "";
                }

                if ($name == "dummy") {
                    if ($zugriff) {
                        $out .= $edit_admin."-----Trennlinie-----";
                    } else {
                        $out .= "<br>";
                    }
                } elseif ($db_edit == "0") {
                    $out .= "<li>".$edit_admin."<a href='{$url}' class='linkext' target='_blank'>{$name}</a></li>";
                } else {
                    $out .= "<table class='liste'><tr><td style='font-weight:bold;width:20%;'>Bezeichnung:</td><td>{$name}</td></tr>";
                    $out .= "<tr><td style='font-weight:bold;'>URL:</td><td><a href='{$url}' class='linkext' target='_blank'>{$url}</a></td></tr></table>";
                }
            }
            $out .= "</ul>";
        }

        $out .= "</div>";
        $out .= "<div class='responsive-flex-2'>";
        $out .= "<h2>Downloads</h2>";

        $db_table = 'downloads';
        $def_folder = 'downloads';

        // -------------------------------------------------------------
        // ZUGRIFF
        if ((($_SESSION['auth'] ?? null) == 'all') or in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? ''))) {
            $zugriff = "1";
        } else {
            $zugriff = "0";
        }
        $button_name = 'button'.$db_table;
        if (isset($_GET[$button_name])) {
            $_POST[$button_name] = $_GET[$button_name];
            $id = $_GET['id'] ?? null;
        }
        if (isset($_POST[$button_name])) {
            $_SESSION['edit']['db_table'] = $db_table;
        }

        // -------------------------------------------------------------
        // USERVARIABLEN PRÜFEN
        if (isset($id) and is_ganzzahl($id) and ($_SESSION['edit']['db_table'] == $db_table)) {
            $_SESSION[$db_table."id_"] = $id;
        }
        // $id = $_SESSION[$db_table.'id_'] ?? null;

        // -------------------------------------------------------------
        // BEARBEITEN
        if ($zugriff) {
            $functions = ['neu' => 'Neuer Eintrag',
                'edit' => 'Bearbeiten',
                'abbruch' => 'Abbrechen',
                'vorschau' => 'Vorschau',
                'replace' => 'Überschreiben',
                'save' => 'Speichern',
                'delete' => 'Löschen',
                'deletefile' => 'Datei entfernen',
                'start' => 'start',
                'undo' => 'undo',
                'up' => 'up',
                'down' => 'down',
                'zurück' => 'Zurück', ];
        } else {
            $functions = [];
        }

        $function = array_search($_POST[$button_name] ?? null, $functions);
        if ($function != "") {
            ob_start();
            include __DIR__.'/../../../../_/admin/admin_db.php';
            $out .= ob_get_contents();
            ob_end_clean();
        }
        if (($_SESSION['edit']['table'] ?? null) == $db_table) {
            $db_edit = "1";
        } else {
            $db_edit = "0";
        }
        // $_SESSION[$db_table."id_"] = $id;

        // -------------------------------------------------------------
        // MENÜ
        if ($zugriff and $db_edit == "0") {
            if (($alert ?? '') != '') {
                $out .= "<div class='buttonbar'><span class='error'>".$alert."</span></div>\n";
            }
            $out .= "<div class='buttonbar'>".\olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")."</div>";
        }

        // -------------------------------------------------------------
        //  VORSCHAU - LISTE
        $sql = "SELECT * from {$db_table} ORDER BY position";
        $result = $db->query($sql);
        $next_pos = mysqli_num_rows($result) + 1;

        if ($zugriff) {
            if ($db_edit == "1") {
                $sql = "SELECT * FROM {$db_table} WHERE (id='".$_SESSION[$db_table."id_"]."') ORDER BY position ASC";
            } else {
                $sql = "SELECT * FROM {$db_table} ORDER BY  position ASC";
            }
        } else {
            $sql = "SELECT * FROM {$db_table} WHERE (on_off='1') ORDER BY  position ASC";
        }

        if (($db_edit == "0") or (($do ?? null) == 'vorschau')) {
            $result = $db->query($sql);
            $out .= "<ul class='nobox'>";
            while ($row = mysqli_fetch_array($result)) {
                if (($do ?? null) == 'vorschau') {
                    $row = $vorschau;
                }
                $id_tmp = $row['id'];
                $name = $row['name'];
                $on_off = $row['on_off'];
                $file1 = $row['file1'] ?? '';
                if ($zugriff and (($do ?? null) != 'vorschau')) {
                    $edit_admin = "<a href='{$code_href}service?id={$id_tmp}&amp;{$button_name}=up' style='margin-right:4px;'><img src='{$code_href}assets/icns/up_16.svg' class='noborder'></a><a href='{$code_href}service?id={$id_tmp}&amp;{$button_name}=down' style='margin-right:4px;'><img src='{$code_href}assets/icns/down_16.svg' class='noborder'></a><a href='{$code_href}service?id={$id_tmp}&{$button_name}=start' class='linkedit'>&nbsp;</a>";
                } else {
                    $edit_admin = "";
                }

                if ($on_off == 0) {
                    $class = " class='error'";
                } else {
                    $class = "";
                }

                include __DIR__.'/../../../../_/library/phpWebFileManager/icons.inc.php';
                $var = explode(".", $file1);
                $ext = strtolower(end($var));
                $icon = $fm_cfg['icons']['ext'][$ext] ?? '';
                if ($ext != "" and $ext !== 'pdf') {
                    $icon = "<img src='{$code_href}assets/icns/".$icon."' class='noborder' style='margin-right:6px;vertical-align:middle;'>";
                } else {
                    $icon = "";
                }

                if ($name == "dummy") {
                    if ($zugriff) {
                        $out .= $edit_admin."-----Trennlinie-----";
                    } else {
                        $out .= "<br>";
                    }
                } elseif ($db_edit == "0") {
                    $out .= "<li>".$edit_admin./* $icon."<a href='$def_folder/$file1' target='_blank'>$name</a>". */ $file_utils->olzFile($db_table, $id_tmp, 1, $name)."</li>";
                } else {
                    $out .= "<table class='liste'><tr><td style='font-weight:bold;width:20%;'>Bezeichnung:</td><td>{$name}</td></tr>";
                    $out .= "<tr><td style='font-weight:bold;'>Dateiname:</td><td>{$icon}<a href='{$tmp_folder}/{$file1}' target='_blank'>{$file1}</a></td></tr></table>";
                }
            }
            $out .= "</ul>";
        }
        $out .= "</div></div><br><br>";
        $out .= "</form>";

        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
