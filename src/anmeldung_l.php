<?php
$db_table = "anmeldung";

//-------------------------------------------------------------
// ZUGRIFF
if (($_SESSION['auth'] == "all") OR (in_array($db_table ,split(' ',$_SESSION['auth'])))) $zugriff = "1";
else $zugriff = "0";
$button_name = "button".$db_table;
if(isset($$button_name)) $_SESSION['edit']['db_table'] = $db_table;

//-------------------------------------------------------------
// USERVARIABLEN PRÜFEN
if (isset($code))
    {$sql = "SELECT * FROM anmeldung WHERE (uid='$code')";
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
        {$row = mysql_fetch_array($result);
        $id_event = $row['event_id'];
        $uid = $code;
        }
    else
        {$id_event = "";
        }
    }
elseif (isset($id_event) AND is_ganzzahl($id_event)) $_SESSION[$db_table."id_event_"] = $id_event;
else $id_event = $_SESSION[$db_table.'id_event_'];
if ($id_event=="")
    {$sql = "SELECT * FROM termine WHERE (datum_anmeldung>'0000-00-00') AND (datum_end>$heute) ORDER BY datum DESC LIMIT 1"; // jüngster Event
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    $_SESSION[$db_table.'id_event_'] = $row['id'];
    }
$id_event = $_SESSION[$db_table.'id_event_'];

//-------------------------------------------------------------
// EVENTINFOS
if ($id_event=="")
    {$alert = "<h2>Keine Anmeldungen</h2>";
    }
else
    {$sql = "SELECT * FROM termine WHERE (id='$id_event') ORDER BY datum ASC LIMIT 0,1";
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    if($row['datum_end']>$heute)
        {$titel = $row['titel'];
        $datum = $row['datum'];
        $datum_end = $row['datum_end'];
        $datum_anm = $row['datum_anmeldung'];
        $infos = $row['text_anmeldung'];
        $titel = $row['titel'];
        $link = $row['link'];
        if ($datum!=$datum_end) $datum = olz_date("t.m.",$datum)." - ". olz_date("t.m.jjjj",$datum_end);
        else $datum = olz_date("t.m.jjjj",$datum);
        $link = str_replace(array("</div><div","<div","</div"),array("</span> | <span","<span","</span"),$link);
        echo "<h2>".$datum.": ".$titel."</h2>";
        echo "<div class='nobox'><b>Meldeschluss: ".olz_date("t. MM jjjj",$datum_anm)."</b><p>".$link."<br>".$infos."</p></div>";
        }
    else
        {echo "<h2>Keine laufenden Anmeldungen</h2>";
        $id_event="";
        }

//-------------------------------------------------------------
// ANMELDUNG BEARBEITEN
if ($zugriff)
    {$functions = array('neu' => 'Neue Anmeldung',
                'code' => 'Anmeldung bearbeiten',
                'edit' => 'Bearbeiten',
                'start_user' => 'Weiter',
                'abbruch' => 'Abbrechen',
                'vorschau' => 'Vorschau',
                'save' => 'Speichern',
                'delete' => 'Löschen',
                'start' => 'start',
                'undo' => 'undo');
    }
else
    {$functions = array('neu' => 'Neue Anmeldung',
                'code' => 'Anmeldung bearbeiten',
                'edit' => 'Bearbeiten',
                'start_user' => 'Weiter',
                'abbruch' => 'Abbrechen',
                'vorschau' => 'Vorschau',
                'save' => 'Speichern',
                'delete' => 'Löschen',
                'undo' => 'undo');
    }
$function = array_search($$button_name,$functions);
if (($function!="") AND (($datum_anm>$heute) OR ($zugriff))) // Bearbeitung zugelassen
    {include 'admin/admin_db.php';
    $alert_tmp = "";
    }
elseif (isset($code))
    {$alert_tmp= "<br>Für Änderungen musst du dich direkt an die verantwortliche Person wenden.";
    unset($_SESSION['edit']);
    }
if ($_SESSION['edit']['table']==$db_table) $db_edit = "1";
else $db_edit = "0";

if (isset($feedback))
    {echo "<div class='buttonbar error'>".$feedback."</div>";
    $feedback = "";
    }

//-------------------------------------------------------------
// ANMELDUNGEN bzw. VORSCHAU
if ((($db_edit=="0") OR ($do=="vorschau")) AND ($id_event!=""))
    {if ($do!="vorschau")
        {$sql = "SELECT SUM(anzahl) FROM anmeldung WHERE (event_id='$id_event')";
        $result_tmp = mysql_query($sql);
        $count = mysql_fetch_array($result_tmp);

        echo "<h2>Anmeldungen Stand ".olz_date("tt.mm.jj","").": Total ".$count[0]." TeilnehmerInnen</h2>";
//-------------------------------------------------------------
// MENÜ
if (($db_edit=="0") AND ($datum_anm>$heute))
    {echo "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Neue Anmeldung","0"),array("Anmeldung bearbeiten","1")),"")."</div>";}
elseif ($db_edit=="0")
    {echo "<div class='buttonbar error'>Die interne Anmeldefrist für diesen Anlass ist abgelaufen.$alert_tmp</div>";
    }

        // Standardspalten
        echo "<table><tr class='liste'><td style='padding-left:10px;font-weight:bold;'>Name</td><td style='padding-left:10px;font-weight:bold;'>Teilnehmer</td>";
        $felder_tmp = array("name","anzahl");
        $sql = "SELECT * FROM anm_felder WHERE (zeigen>'0') AND (event_id='$id_event') ORDER BY zeigen ASC";
        $result = mysql_query($sql);
        // Spezialspalten
        while($row = mysql_fetch_array($result))
            {$label = $row['label'];
            array_push($felder_tmp,"feld".$row['zeigen']);        
            echo "<td style='padding-left:10px;font-weight:bold;'>$label</td>";
            }
        echo "</tr>\n";

        // Liste Anmedlungen
        $sql = "SELECT * FROM anmeldung WHERE (event_id = $id_event) AND (on_off = '1') ORDER BY datum DESC, zeit DESC" ;
        $result = mysql_query($sql);
        while($row = mysql_fetch_array($result))
            {echo "<tr class='nobox'>";
            $id = $row['id'];
            if ($zugriff AND ($do != 'vorschau')) $edit_admin = "<a href='index.php?id=$id&$button_name=start' class='linkedit'>&nbsp;</a>";
            foreach($felder_tmp as $feld_tmp)
                {$$var = $row[$feld_tmp];
                if (($feld_tmp!="name") AND ($feld_tmp!="anzahl")) // Spezialspalten
                    {if ($$var>"0") $$var = "x";
                    else $$var = "";
                    }
                echo "<td style='padding-left:30px;'>".$edit_admin.$$var."</td>";
                $edit_admin = '';
                }
            echo "</tr>";
            }
        echo "</table>";
        }    
    else    
        {// VORSCHAU ANMELDUNG
        echo "<table class='liste'>";
        $felder_tmp = array(array("datum","Datum"),array("name","Name, Vorname"),array("email","Email-Adresse"),array("anzahl","Anzahl Teilnehmer"));
        $sql = "SELECT * from anm_felder WHERE (event_id='$id_event') ORDER BY position ASC";
        $result = mysql_query($sql);
        while($row = mysql_fetch_array($result))
            {array_push($felder_tmp,array("feld".$row['position'],$row['label'],$row['typ']));        
            }

        $sql = "SELECT * from anmeldung WHERE (id = ".$_SESSION[$db_table."id"].")" ;
        $result = mysql_query($sql);
        while($row = mysql_fetch_array($result))
            {$row = $vorschau;
            foreach($felder_tmp as $feld_tmp)
                {$var = $feld_tmp[0];
                $label = $feld_tmp[1];
                $$var = $row[$var];
                if ($feld_tmp[2]=='checkbox')
                    {if($$var=='1') $$var = "ja";
                    else $$var = "nein";
                    }

                echo "<tr><td style='padding-left:10px;font-weight:bold;'>".$label."</td><td style='padding-left:10px;'>".$$var."</td>";
                }
            }
        echo "</table >";
        }        
    }
}
?>