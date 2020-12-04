<?php

// =============================================================================
// Aktuelle Berichte von offiziellen Vereinsorganen.
// =============================================================================

//-------------------------------------------------------------
// DATENSATZ EDITIEREN
if ($zugriff) {
    $functions = ['neu' => 'Neuer Eintrag',
        'edit' => 'Bearbeiten',
        'abbruch' => 'Abbrechen',
        'vorschau' => 'Vorschau',
        'save' => 'Speichern',
        'delete' => 'Löschen',
        'start' => 'start',
        'upload' => 'Upload',
        'deletebild1' => 'BILD 1 entfernen',
        'deletebild2' => 'BILD 2 entfernen',
        'deletebild3' => 'BILD 3 entfernen',
        'undo' => 'undo', ];
} else {
    $functions = [];
}
$function = array_search(${$button_name}, $functions);
if ($function != "") {
    include __DIR__.'/admin/admin_db.php';
}
if ($_SESSION['edit']['table'] == $db_table) {
    $db_edit = "1";
} else {
    $db_edit = "0";
}

//-------------------------------------------------------------
// MENÜ
if ($zugriff and ($db_edit == '0')) {
    echo "<div class='buttonbar'>\n".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")."</div>";
}

//-------------------------------------------------------------
// AKTUELL - VORSCHAU
if (($db_edit == "0") or ($do == "vorschau")) {
    if (in_array($_SESSION[$db_table.'id_'], $aktuell_special)) {// Spezial
        $sql = "SELECT * FROM {$db_table} WHERE (typ LIKE '{$id}') ORDER BY datum DESC";
        $titel_special = array_search($id, $aktuell_special);
        echo "<h2>".$titel_special."</h2>";
        $_SESSION[$db_table.'jahr_'] = "special";
        $jahr = $_SESSION[$db_table.'jahr_'];
    } else {// Nachricht nach ID
        $sql = "SELECT * FROM {$db_table} WHERE (id = '{$id}') ORDER BY datum DESC";
        $result = $db->query($sql);
        $row = mysqli_fetch_array($result);
        if (mysqli_num_rows($result) > 0) {
            $_SESSION[$db_table.'jahr_'] = date("Y", strtotime($row["datum"]));
        }
        $jahr = $_SESSION[$db_table.'jahr_'];
    }

    $result = $db->query($sql);

    // Aktuelle Nachricht
    while ($row = mysqli_fetch_array($result)) {
        if ($do == "vorschau") {
            $row = $vorschau;
        } else {
            $id_tmp = intval($row['id']);
            $db->query("UPDATE `aktuell` SET `counter`=`counter` + 1 WHERE `id`='{$id_tmp}'");
        }
        $id_tmp = $row['id'];
        $titel = $row['titel'];
        $text = olz_amp($row['text']);
        $textlang = olz_br($row['textlang']);
        //$textlang = str_replace(array("\n\n","\n"),array("<p>","<br>"),$row['textlang']);
        $autor = ($row['autor'] > '') ? $row['autor'] : "..";
        $datum = $row['datum'];

        $datum = olz_date("tt.mm.jj", $datum);

        $edit_admin = ($zugriff and ($do != 'vorschau')) ? "<a href='aktuell.php?id={$id_tmp}&amp;button{$db_table}=start' class='linkedit'>&nbsp;</a>" : "";

        // Bildercode einfügen
        if ($do == 'vorschau') {
            preg_match_all("/<bild([0-9]+)(\\s+size=([0-9]+))?([^>]*)>/i", $text, $matches);
            for ($i = 0; $i < count($matches[0]); $i++) {
                $size = intval($matches[3][$i]);
                if ($size < 1) {
                    $size = 110;
                }
                $tmp_html = olz_image($db_table, $id, intval($matches[1][$i]), $size, "gallery[myset]", " class='box' style='float:left;clear:left;margin:3px 5px 3px 0px;'");
                $text = str_replace($matches[0][$i], $tmp_html, $text);
            }
        }
        preg_match_all("/<bild([0-9]+)(\\s+size=([0-9]+))?([^>]*)>/i", $textlang, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $size = intval($matches[3][$i]);
            if ($size < 1) {
                $size = 240;
            }
            $tmp_html = olz_image($db_table, $id, intval($matches[1][$i]), $size, "gallery[myset]", " class='box' style='float:left;clear:left;margin:3px 5px 3px 0px;'");
            $textlang = str_replace($matches[0][$i], $tmp_html, $textlang);
        }

        // Dateicode einfügen
        preg_match_all("/<datei([0-9]+)(\\s+text=(\"|\\')([^\"\\']+)(\"|\\'))?([^>]*)>/i", $text, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $tmptext = $matches[4][$i];
            if (mb_strlen($tmptext) < 1) {
                $tmptext = "Datei ".$matches[1][$i];
            }
            $tmp_html = olz_file($db_table, $id, intval($matches[1][$i]), $tmptext);
            $text = str_replace($matches[0][$i], $tmp_html, $text);
        }
        preg_match_all("/<datei([0-9]+)(\\s+text=(\"|\\')([^\"\\']+)(\"|\\'))?([^>]*)>/i", $textlang, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $tmptext = $matches[4][$i];
            if (mb_strlen($tmptext) < 1) {
                $tmptext = "Datei ".$matches[1][$i];
            }
            $tmp_html = olz_file($db_table, $id, intval($matches[1][$i]), $tmptext);
            $textlang = str_replace($matches[0][$i], $tmp_html, $textlang);
        }
        /*
        $bilder = array(
            array("<BILD1>",$row['bild1'],$row['bild1_breite']), // Bild in Tabelle eingebettet, inkl. clear:left
            array("<BILD2>",$row['bild2'],$row['bild2_breite']),
            array("<BILD3>",$row['bild3'],$row['bild3_breite']),
            array("!BILD1!",$row['bild1'],$row['bild1_breite']), // Bild mit lightview
            array("!BILD2!",$row['bild2'],$row['bild2_breite']),
            array("!BILD3!",$row['bild3'],$row['bild3_breite']),
            array("/BILD1/",$row['bild1'],$row['bild1_breite']), // Bild roh
            array("/BILD2/",$row['bild2'],$row['bild2_breite']),
            array("/BILD3/",$row['bild3'],$row['bild3_breite']));

        foreach ($bilder as $tmp_bild)
            {//echo $tmp_bild[0]."*";
            if (substr($tmp_bild[0],0,1) == '/')
                {$tmp_html = "<img src='".$data_href."img/".$db_table."/".$id."/img/".str_pad(substr($tmp_bild[0],5,1), 3, "0", STR_PAD_LEFT).".jpg' width='".$tmp_bild[2]."px' alt='' class='box'>";
                }
            else
                {$tmp_html = olz_image($db_table, $id, substr($tmp_bild[0],5,1), $tmp_bild[2], true, " class='box' style='float:left;clear:left;margin:3px 5px 3px 0px;'");
                }
            $textlang = str_replace($tmp_bild[0],$tmp_html,$textlang);
            $text = ($do=='vorschau') ? str_replace($tmp_bild[0],$tmp_html,$text) : str_replace($tmp_bild[0],'',$text);
            $tmp_html = "";
            }
            */
        echo "<h2 class='test-flaky'>".$edit_admin.$titel." (".$datum."/".$autor.")</h2>";
        echo "<div class='lightgallery'><p><b>".$text."</b><p>".$textlang."</p></div>\n";
    }
}
