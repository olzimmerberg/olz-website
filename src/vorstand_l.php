<?php
echo "<h2>Vorstand OL Zimmerberg</h2><p>";
olz_text_insert(8);

// VORSTAND NEU
$db_table = "vorstand";

//-------------------------------------------------------------
// ZUGRIFF
if (($_SESSION['auth']=="all") OR (in_array($db_table ,split(' ',$_SESSION['auth'])))) $zugriff = "1";
else $zugriff = "0";
$button_name = "button".$db_table;
if(isset($$button_name)) $_SESSION['edit']['db_table'] = $db_table;

//-------------------------------------------------------------
// USERVARIABLEN PRÜFEN
if (isset($id) AND is_ganzzahl($id)) $_SESSION[$db_table."id_"] = $id;
else $id = $_SESSION[$db_table.'id_'];

//-------------------------------------------------------------
// DATENSATZ EDITIEREN
if ($zugriff AND $_SESSION['edit']['db_table']==$db_table)
    {$functions = array('neu' => 'Neuer Eintrag',
                'edit' => 'Bearbeiten',
                'abbruch' => 'Abbrechen',
                'vorschau' => 'Vorschau',
                'save' => 'Speichern',
                'delete' => 'Löschen',
                'start' => 'start',
                'upload' => 'Upload',
                'deletebild1' => 'Bild entfernen',
                'undo' => 'undo');
    }
else
    {$functions = array();}
$function = array_search($$button_name,$functions);
if ($function!="")
    {include 'admin/admin_db.php';}
if ($_SESSION['edit']['table']==$db_table) $db_edit = "1";
else $db_edit = "0";

//-------------------------------------------------------------
// MENÜ
if ($zugriff AND ($db_edit=='0'))
    {echo "<div class='buttonbar'>\n".olz_buttons("button".$db_table,array(array("Neuer Eintrag","0")),"")."</div>";}

//-------------------------------------------------------------
// AKTUELL - VORSCHAU
if (($db_edit=="0") OR ($do=="vorschau"))
    {echo "<table>";
    if ($do=="vorschau") $sql = "SELECT * FROM $db_table WHERE (on_off = '1') AND (id = ".$_SESSION[$db_table.'id'].") ORDER BY position ASC";
    else $sql = "SELECT * FROM $db_table WHERE (on_off = '1') ORDER BY position ASC";
    $result = mysql_query($sql);
    $counter = 0;

    while ($row = mysql_fetch_array($result))
        {if ($do=="vorschau") $row = $vorschau;
        $counter = $counter + 1;
        $id_tmp = $row['id'];
        $name = $row['name'];
        $funktion = $row['funktion'];
        $email = $row['email'];
        $bild = $row['bild'];
        if ($zugriff AND ($do != 'vorschau')) $edit_admin = "<a href='index.php?id=$id_tmp&amp;button$db_table=start' class='linkedit'>&nbsp;</a>";
        else $edit_admin = "";
        if(bcmod($counter-1,4)==0) echo "<tr class='thumbs'>";
        echo "<td style='width:25%;vertical-align:top;'><img src='".$data_href."olz_mitglieder/".$bild."' title='$name' alt=''><br>
        $edit_admin<strong>$name</strong><br>$funktion<br>".olz_mask_email($email,"Email","")."</script></td>";
        if(bcmod($counter,4)==0) echo "</tr>";
        }
    $counter = 4-$counter;
    while ($counter>0)
        {echo "<td style='width:25%;'>&nbsp;</td>";
        $counter = $counter-1;
        }
    echo "</table>";
    }

?>
