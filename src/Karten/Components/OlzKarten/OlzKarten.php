<?php

namespace Olz\Karten\Components\OlzKarten;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Components\Schema\OlzMapData\OlzMapData;

class OlzKarten extends OlzComponent {
    public static $title = "Karten";
    public static $description = "Die OL-Karten, die die OL Zimmerberg aufnimmt, unterhält und verkauft.";

    public function getHtml($args = []): string {
        global $_GET, $_POST, $_SESSION, $db_table, $funktion, $id;

        require_once __DIR__.'/../../../../_/admin/olz_functions.php';

        $db = $this->dbUtils()->getDb();
        $env_utils = $this->envUtils();
        $code_href = $env_utils->getCodeHref();
        $data_path = $env_utils->getDataPath();
        $data_href = $env_utils->getDataHref();
        $out = '';

        $out .= OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
        ]);

        $db_table = 'karten';

        $button_name = 'button'.$db_table;
        if (isset($_GET[$button_name])) {
            $_POST[$button_name] = $_GET[$button_name];
            $id = $_GET['id'] ?? null;
        }
        if (isset($_POST[$button_name])) {
            $_SESSION['edit']['db_table'] = $db_table;
        }

        $out .= "
        <div class='content-right'>
        <form name='Formularr' method='post' action='{$code_href}karten#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>
        <div>";

        $karten_typ = [
            'OL-Karten' => 'ol',
            'Dorf-Karten' => 'stadt',
            'sCOOL-Karten' => 'scool', ];

        // -------------------------------------------------------------
        // ZUGRIFF
        if ((($_SESSION['auth'] ?? null) == 'all') or in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? ''))) {
            $zugriff = "1";
        } else {
            $zugriff = "0";
        }

        // -------------------------------------------------------------
        // USERVARIABLEN PRÜFEN
        if (isset($id) and is_ganzzahl($id)) {
            $_SESSION[$db_table."id_"] = $id;
        }
        $id = $_SESSION[$db_table.'id_'] ?? null;

        // -------------------------------------------------------------
        // BEARBEITEN
        if ($zugriff) {
            $functions = ['neu' => 'Neue Karte',
                'edit' => 'Bearbeiten',
                'abbruch' => 'Abbrechen',
                'vorschau' => 'Vorschau',
                'save' => 'Speichern',
                'delete' => 'Löschen',
                'start' => 'start',
                'undo' => 'undo',
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

        // -------------------------------------------------------------
        // MENÜ
        if ($zugriff and $db_edit == "0") {
            if (($alert ?? "") != "") {
                $out .= "<div class='buttonbar'><span class='error'>".$alert."</span></div>\n";
            }
            $out .= "<div class='buttonbar'>".\olz_buttons("button".$db_table, [["Neue Karte", "0"]], "")."</div>";
        }

        // -------------------------------------------------------------
        //  VORSCHAU - LISTE
        if (($db_edit == "0") or (($do ?? null) == 'vorschau')) {
            if (($do ?? null) == 'vorschau') {
                $sql = "SELECT * FROM {$db_table} WHERE (id = ".$_SESSION[$db_table."id"].")";
            } // Proforma-Abfrage
            else {
                $sql = "SELECT * FROM {$db_table} ORDER BY CASE WHEN `typ` = 'ol' THEN 1 WHEN `typ` = 'stadt' THEN 2 WHEN `typ` = 'scool' THEN 3 ELSE 4 END,ort ASC, position ASC";
            }
            // echo $sql;
            $result = $db->query($sql);
            $tmp_typ = "";
            $tmp_tag = "";

            while ($row = mysqli_fetch_array($result)) {
                if (($do ?? null) == 'vorschau') {
                    $row = $vorschau;
                }
                $name = $row['name'];
                $typ = $row['typ'];
                $id = $row['id'];
                $position = $row['position'];
                $massstab = $row['massstab'];
                $jahr = $row['jahr'];
                $kartennr = ($row['kartennr'] > 0) ? $row['kartennr'] : "'---'";
                $center_x = $row['center_x'];
                $center_y = $row['center_y'];
                $zoom = $row['zoom'];
                $ort = $row['ort'];
                $thumb = $row['vorschau'];

                if ($typ == "scool") {
                    $name = $name." (".$ort.")";
                }

                if ($zugriff) {
                    $edit_admin = "<a href='{$code_href}karten?id={$id}&{$button_name}=start' class='linkedit'>&nbsp;</a>";
                } else {
                    $edit_admin = "";
                }

                if ($massstab == "") {
                    $massstab = "&nbsp;";
                }

                // $thumb_name = strtolower(str_replace(array("ä","ö","ü","-"," ","/"),array("ae","oe","ue","_","_","_"),$name)."_".$jahr."_".preg_replace("[^0-9]", "",substr($massstab,2))).".jpg";
                // if (file_exists("img/karten/".$thumb_name)){
                if ($thumb > "") {
                    $img_info_gross = getimagesize("{$data_path}img/karten/{$thumb}");
                    $img_width = $img_info_gross[0];
                    $img_height = $img_info_gross[1];
                    $img_href = "{$data_href}img/karten/{$thumb}";
                    $map = "<span class='lightgallery'><a href='{$img_href}' data-src='{$img_href}'><img src='{$code_href}assets/icns/magnifier_16.svg' style='float:right;border:none;'></a></span>";
                // $map = "<img src='{$code_href}assets/icns/magnifier_16.svg' style='float:right;border:none;' onmouseover=\"olz.trailOn('{$data_href}img/karten/$thumb','$name','$jahr','','','','','$center_x','$center_y','','','$massstab','---');\" onmouseout=\"olz.hidetrail();\">";}
                } else {
                    $map = '';
                }

                if ($typ == 'ol') {
                    $icon = 'orienteering_forest_16.svg';
                } elseif ($typ == 'stadt') {
                    $icon = 'orienteering_village_16.svg';
                } elseif ($typ == 'scool') {
                    $icon = 'orienteering_scool_16.svg';
                }
                if ($typ != $tmp_typ) {
                    $out .= $tmp_tag."<h2><img src='{$code_href}assets/icns/".$icon."' class='noborder' style='margin-right:10px;vertical-align:bottom;'>".array_search($typ, $karten_typ)."</h2><table class='liste'>";
                }
                $out .= OlzMapData::render([
                    'name' => $name,
                    'year' => $jahr,
                    'scale' => $massstab,
                ]);
                if ($center_x > 0) {
                    $out .= <<<ZZZZZZZZZZ
                    <tr>
                        <td>{$edit_admin}<a href='#{$name}' onclick='goto({$center_x},{$center_y},{$zoom},&quot;{$name}&quot;);return false' class='linkmap' itemprop='name'>{$name}</a>{$map}</td>
                        <td>{$massstab}</td>
                        <td>{$jahr}</td>
                    </tr>
                    ZZZZZZZZZZ;
                } else {
                    $out .= <<<ZZZZZZZZZZ
                    <tr>
                        <td>{$edit_admin}<span class='linkmap' itemprop='name'>{$name}</span></td>
                        <td>{$massstab}</td>
                        <td>{$jahr}</td>
                    </tr>
                    ZZZZZZZZZZ;
                }
                $tmp_tag = "</table>";
                $tmp_typ = $typ;
            }
            $out .= "</table>";
        }

        $out .= "</div>
        </form>
        </div>
        <div class='content-middle'>";

        $out .= '<script type="text/javascript" src="https://map.search.ch/api/map.js?lang=en"></script>';

        // Karte von mapserch.ch initialisieren
        $sql = "SELECT * FROM {$db_table} ORDER BY typ ASC";
        $result = $db->query($sql);
        $pois = "";

        while ($row = mysqli_fetch_array($result)) {
            $name = $row['name'];
            $massstab = $row['massstab'];
            $center_x = $row['center_x'];
            $center_y = $row['center_y'];
            $typ = $row['typ'];
            if ($typ == 'ol') {
                $icon = 'orienteering_forest_16.svg';
            } elseif ($typ == 'stadt') {
                $icon = 'orienteering_village_16.svg';
            } elseif ($typ == 'scool') {
                $icon = 'orienteering_scool_16.svg';
            }

            $pois .= "theMap.addPOI(new SearchChPOI({ center:[{$center_x},{$center_y}], title:\"\",html:\"{$name}\", maxzoom:128, icon:\"{$code_href}assets/icns/{$icon}\" }));\n";
        }

        $out .= <<<ZZZZZZZZZZ
        <script type="text/javascript">
            var theMap = new SearchChMap({ center:[687500,237000], controls:"zoom,type", type:'aerial', circle:0, poigroups:"", zoom:32 });
            {$pois}
            function goto(x,y,z,name) {
            var x=x;
            var x0 = Number(window.localStorage.getItem('x0'));
            var y=y;
            var y0 = Number(window.localStorage.getItem('y0'));
            var z=z;
            var name=name;
            window.location.hash="top";
            //theMap.removeAllPOIs();
        
            x0 = (x0>'') ? x0 : 687500 ;
            y0 = (y0>'') ? y0 : 237000 ;
            x1 = Math.round((x+x0)/2);
            y1 = Math.round((y+y0)/2);
            z1 = 32;
            theMap.go({ center:[x1,y1], zoom:z1, animated:true });
        
            window.localStorage.setItem('x0', x);
            window.localStorage.setItem('y0', y);
        
            // Add a custom POI
            //theMap.addPOI(new SearchChPOI({ center:[x,y], title:"",html:name, maxzoom:512, icon:"{$code_href}assets/icns/orienteering_forest_16.svg" }));\n
            setTimeout("theMap.go({center:["+x+","+y+"], zoom:"+z+", animated:true})", 2000);
            }
        </script>

        <div class="box test-flaky" id="mapcontainer" style="position:relative;height:650px;z-index:1;"></div>
        <br>
        ZZZZZZZZZZ;

        $out .= "<h2>Kartenverkauf</h2>";
        $out .= "<div style='margin-top:10px;'>";
        $out .= "<table class='liste'>";
        $out .= "<tr class='tablebar'><td style='width:46%;'>Kartentyp</td><td style='width:18%;'>Format</td><td style='width:18%;'>Karte gedruckt</td><td style='width:18%;'>Karte digital</td></tr>
        <tr><td>Wald-/Dorf-Karte</td><td>A4</td><td>2.50</td><td>1.50</td></tr>
        <tr><td></td><td>A3</td><td>4.00</td><td>2.50</td></tr>
        <tr><td>Schulhauskarte</td><td>A4</td><td>1.50</td><td>1.00</td></tr>
        </table><p>(Kartenpreise gültig ab 1.1.2019)</p></div>";
        $out .= OlzEditableText::render(['olz_text_id' => 12]);

        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
