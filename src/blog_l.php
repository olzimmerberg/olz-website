<?php
//echo "<h2></h2><div style='overflow-x:auto;'><table class='galerie'><tr class='thumbs blog'>";
//$kader = array(array("lilly","Lilly Gross"),array("julia","Julia Gross"),array("tanja","Tanja Frey"),array("sara","Sara Würmli"),array("paula","Paula Gross"));
//$kader = array(array("lilly","Lilly Gross"),array("julia","Julia Gross"),array("florian","Florian Attinger"),array("sara","Sara Würmli"),array("paula","Paula Gross"));
//$kader = array(array("lilly","Lilly Gross"),array("julia","<a href='http://juliagross.ch' class='linkext' target='blank'>Julia Gross</a>"),array("florian","Florian Attinger"),array("paula","Paula Gross"),array("michael","Michael Felder"));
//shuffle($kader);
//foreach($kader as $member)
//    {echo "<td><img src='".$root_path."img/".$member[0].".jpg' alt=''><div style='padding-top:5px;text-align:center;'>".$member[1]."</div></td>";
//    }
//echo "</tr></table></div><h2></h2>";

require_once("file_tools.php");
require_once("image_tools.php");
$db_table = "blog";
$def_folder = "downloads";

//-------------------------------------------------------------
// ZUGRIFF
if (($_SESSION['auth']=="all") OR (in_array($db_table ,preg_split("/ /",$_SESSION['auth'])))) $zugriff = "1";
else $zugriff = "0";
$button_name = "button".$db_table;
if(isset($$button_name)) $_SESSION['edit']['db_table'] = $db_table;

//-------------------------------------------------------------
// USERVARIABLEN PRÜFEN
if (isset($id) AND is_ganzzahl($id))
    {$_SESSION[$db_table."id_"] = $id;
    $sql = "UPDATE $db_table SET counter=(counter+1) WHERE (id = '$id')";
    $db->query($sql);
    }
else $id = $_SESSION[$db_table."id_"];

//-------------------------------------------------------------
// BEARBEITEN
if ($zugriff)
    {$functions = array('neu' => 'Neuer Eintrag',
                'edit' => 'Bearbeiten',
                'abbruch' => 'Abbrechen',
                'replace' => 'Überschreiben',
                'vorschau' => 'Vorschau',
                'save' => 'Speichern',
                'delete' => 'Löschen',
                'deletebild1' => 'Bild 1 entfernen',
                'deletebild2' => 'Bild 2 entfernen',
                'deletefile1' => 'Download 1 entfernen',
                'deletefile2' => 'Download 2 entfernen',
                'start' => 'start',
                'undo' => 'undo',
                'zurück' => 'Zurück');
    }
else
    {$functions = array();
    }
$function = array_search($$button_name,$functions);
if ($function!="")
    {include 'admin/admin_db.php';}
if ($_SESSION['edit']['table']==$db_table) $db_edit = "1";
else $db_edit = "0";

//-------------------------------------------------------------
// MENÜ
if ($zugriff AND $db_edit=="0")
    {echo "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Neuer Eintrag","0")),"")."</div>";
    }

//-------------------------------------------------------------
//  VORSCHAU - LISTE
if (($db_edit=="0") OR ($do=="vorschau"))
    {if ($zugriff)
        {if ($do=="vorschau") $sql = "WHERE (id = ".$id.")";
        elseif ($_SESSION['auth']=="all") $sql = "";
        else $sql = " WHERE (autor='".ucwords($_SESSION['user'])."') OR (on_off='1')";
        }
    else
        {$sql = "WHERE (on_off = '1') AND (text != '')";
        }
    $sql = "SELECT * FROM $db_table ".$sql." ORDER BY datum DESC, zeit DESC";

    $result = $db->query($sql);
    while($row = mysqli_fetch_array($result))
        {if ($do=="vorschau") $row = $vorschau;
        $autor = ucwords($row['autor']);
        $titel = $row['titel'];
        $text = $row['text'];
        $bild1 = $row['bild1'];
        $bild2 = $row['bild2'];
        $file1 = $row['file1'];
        $file1_name = $row['file1_name'];
        $file2 = $row['file2'];
        $file2_name = $row['file2_name'];
        $datum = $row['datum'];
        $zeit = $row['zeit'];
        $id_tmp = $row['id'];
        $on_off = $row['on_off'];
        $counter = $row['counter'];
        $linkext = $row['linkext'];

        $text = str_replace(array("<br />","<br>\r\n<br>"),array("<br>","<p/>"),stripslashes(nl2br($text)));
        //$text = stripslashes(nl2br($text));
        $text = olz_find_url($text);
        $zeit = date("G:i",strtotime($zeit));

        if (($do != 'vorschau') AND (($_SESSION['auth']=="all") OR (ucwords($_SESSION['user'])==ucwords($autor)))) $edit_admin = "<a href='index.php?page=7&amp;id=$id_tmp&$button_name=start' class='linkedit'>&nbsp;</a>";
        //if ($zugriff AND ($do != 'vorschau')) $edit_admin = "<a href='index.php?page=7&amp;id=$id_tmp&$button_name=start' class='linkedit'>&nbsp;</a>";
        else $edit_admin = "";

        if ($on_off==0) $class = " class='error'";
        else $class = "";

        // Bildcode einfügen
        $tmp_html = olz_image($db_table, $id_tmp, 1, 240, "gallery[blog".$id_tmp."]", " style='float:left; margin:3px 5px 3px 0px;'");
        $text = str_replace("<BILD1>",$tmp_html,$text);
        $tmp_html = olz_image($db_table, $id_tmp, 2, 240, "gallery[blog".$id_tmp."]", " style='float:left; margin:3px 5px 3px 0px;'");
        $text = str_replace("<BILD2>",$tmp_html,$text);

        // Dateicode einfügen
        preg_match_all("/<datei([0-9]+)(\s+text=(\"|\')([^\"\']+)(\"|\'))?([^>]*)>/i", $text, $matches);
        for ($i=0; $i<count($matches[0]); $i++) {
            $tmptext = $matches[4][$i];
            if (mb_strlen($tmptext)<1) $tmptext = "Datei ".$matches[1][$i];
            $tmp_html = olz_file($db_table, $id_tmp, intval($matches[1][$i]), $tmptext);
            $text = str_replace($matches[0][$i], $tmp_html, $text);
        }

        include_once 'library/phpWebFileManager/icons.inc.php';
        if ($file1!="")
            {$ext = strtolower(end(explode(".",$file1)));
            $icon = $fm_cfg['icons']['ext'][$ext];
            if ($icon!="" AND $ext!=='pdf') $icon = "<img src='icns/".$icon."' class='noborder' style='margin-right:4px;vertical-align:middle;'>";
            else $icon = "";
            if (file_exists($def_folder."/".$file1)) $path = $def_folder;
            else $path = "temp";
            if ($file1_name=="") $file1_name = "Download";
            $text = str_replace("<DL1>",$icon."<a href='$path/$file1' target='_blank'>$file1_name</a>",$text);
            }
        if ($file2!="")
            {$ext = strtolower(end(explode(".",$file2)));
            $icon = $fm_cfg['icons']['ext'][$ext];
            if ($icon!="" AND $ext!=='pdf') $icon = "<img src='icns/".$icon."' class='noborder' style='margin-right:4px;vertical-align:middle;'>";
            else $icon = "";
            if (file_exists($def_folder."/".$file2)) $path = $def_folder;
            else $path = "temp";
            if ($file2_name=="") $file1_name = "Download";
            $text = str_replace("<DL2>",$icon."<a href='$path/$file2' target='_blank'>$file2_name</a>",$text);
            }

        echo "<h2 style='clear:left;padding-top:20px;' id='id$id_tmp'>".$edit_admin.$autor.": ".$titel."</h2>";
        echo "<div class='nobox'><p><b>(".olz_date("t.m.jj",$datum)."/$zeit)</b><br>".$text;
        if ($linkext>'') echo "<br><a href='$linkext' target='_blank' class='linkext'>... weiterlesen</a>";
        echo "</div>";
        }
    }
?>