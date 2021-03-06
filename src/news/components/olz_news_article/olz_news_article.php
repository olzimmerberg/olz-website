<?php

function olz_news_article($args = []): string {
    global $db, $_DATE;

    require_once __DIR__.'/../../../image_tools.php';

    $db_table = 'aktuell';
    $id = $args['id'];
    $arg_row = $args['row'] ?? null;
    $can_edit = $args['can_edit'] ?? false;
    $is_preview = $args['is_preview'] ?? false;
    $out = "";

    $sql = "SELECT * FROM {$db_table} WHERE (id = '{$id}') ORDER BY datum DESC";
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);
    if (mysqli_num_rows($result) > 0) {
        $_SESSION[$db_table.'jahr_'] = date("Y", strtotime($row["datum"]));
    }
    $jahr = $_SESSION[$db_table.'jahr_'];

    $result = $db->query($sql);

    // Aktuelle Nachricht
    while ($row = mysqli_fetch_array($result)) {
        if ($is_preview) {
            $row = $arg_row;
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

        $datum = $_DATE->olzDate("tt.mm.jj", $datum);

        $edit_admin = ($can_edit && !$is_preview) ? "<a href='aktuell.php?id={$id_tmp}&amp;button{$db_table}=start' class='linkedit'>&nbsp;</a>" : "";

        // Bildercode einfügen
        if ($is_preview) {
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
        $out .= "<h2>".$edit_admin.$titel." (".$datum."/".$autor.")</h2>";
        $out .= "<div class='lightgallery'><p><b>".$text."</b><p>".$textlang."</p></div>\n";
    }
    return $out;
}
