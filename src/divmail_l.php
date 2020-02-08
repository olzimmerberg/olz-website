    
<?php
//-------------------------------------------------------------
// KONSTANTEN
$db_table = "rundmail";
$betreff_vorspann = "Newsletter OLZimmerberg - ";
$text_nachspann = "\n\n-------------------------------\nHINWEIS\nDu erhältst dieses Mail, weil du dich für den Newsletter angemeldet hast. Über folgenden Link kannst du den Newsletter löschen oder die Einstellungen ändern: http://www.olzimmerberg.ch/index.php?page=8&buttonnewsletter=Weiter&uid=";
$mail_header = "From: OL Zimmerberg <".$db_table."@olzimmerberg.ch>\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8 \nContent-Transfer-Encoding: base64";

//-------------------------------------------------------------
// ZUGRIFF
if (($_SESSION['auth'] == "all") OR (in_array($db_table ,split(' ',$_SESSION['auth'])))) $zugriff = "1";
else $zugriff = "0";
$button_name = "button".$db_table;
if(isset($$button_name)) $_SESSION['edit']['db_table'] = $db_table;

echo "<h2>Rundmail verschicken</h2>";

//-------------------------------------------------------------
// BEARBEITEN
if ($zugriff)
    {$functions = array('neu' => 'Neues Rundmail',
                'edit' => 'Bearbeiten',
                'abbruch' => 'Abbrechen',
                'vorschau' => 'Vorschau',
                'submit' => 'Abschicken');
    }
else
    {$functions = array();}
$function = array_search($$button_name,$functions);
if ($function!="")
    {include 'admin/admin_db.php';}
if ($_SESSION['edit']['table']==$db_table) $db_edit = "1";
else $db_edit = "0";


//-------------------------------------------------------------
// AKTUELL - VORSCHAU
if ($do=="vorschau")
    {$row = $vorschau;
    $id = $row['id'];
    $betreff = $row['betreff'];
    $mailtext = $row['mailtext'];
    echo "<table class='liste'><tr><td$style>Betreff:</td><td>".$row['betreff']."</td></tr>";
    echo "<tr><td$style>Mailtext:</td><td>".$row['mailtext']."</td></tr></table>";

    $sql = "SELECT * FROM newsletter WHERE (kategorie LIKE '%vorstand%') AND (on_off = '1') ORDER BY email DESC";
    $result = mysql_query($sql);
    $num_rows = mysql_numrows($result);
    if ($num_rows == 0) $feedback = "Dieses Mail wird an keine Adresse verschickt.";
    elseif ($num_rows == 1) $feedback = "Dieses Mail wird an 1 Adresse verschickt.";
    else $feedback = "Dieses Mail wird an ".$num_rows." Adressen verschickt.";
    echo "<div class='buttonbar'>".$feedback."</div>";
    }

/*//Neuer Eintrag
if ($function == "edit")
    {$input = form_mail("eingabe","","","");
    $menu = "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Vorschau","3"),array("Abbrechen","2")),"")."</div>";
    }

//Vorschau
elseif ($function == "vorschau")
    {$sql = "SELECT * FROM $db_table WHERE (kategorie LIKE '%vorstand%') AND (on_off = '1') ORDER BY email DESC";
    $result = mysql_query($sql);
    $num_rows = mysql_numrows($result);
    if ($num_rows == 0) $feedback = "Dieses Mail wird an keine Adresse verschickt.";
    elseif ($num_rows == 1) $feedback = "Dieses Mail wird an 1 Adresse verschickt.";
    else $feedback = "Dieses Mail wird an ".$num_rows." Adressen verschickt.";
    echo "<div class='buttonbar'>".$feedback."</div>";
    $input = form_mail("vorschau","","","");
    $menu = "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Abschicken","0"),array("Bearbeiten","1"),array("Abbrechen","2")),"")."</div>";
    }*/
        
//Eintrag abschicken (submit)
if ($function == "submit")
    {$sql = "SELECT * FROM newsletter WHERE (kategorie LIKE '%vorstand%') AND (on_off = '1') AND (email > '') ORDER BY email DESC";
    $result = mysql_query($sql);
    $num_rows = mysql_numrows($result);
    $mail_to = array();
    while($row = mysql_fetch_array($result))
        {$email = $row['email'];
        $name = $row['name'];
        $uid = $row['uid'];
        $mail_text = "Newsletter OL Zimmerberg\n************************\n".$text.$text_nachspann.$uid;
        //mail($email,$betreff_vorspann.$betrefff,base64_encode($mail_text),$mail_header);
        array_push($mail_to,$email);
        }
    if ($num_rows == 0) $feedback = "Dein Mail wurde an keine Adresse verschickt.";
    elseif ($num_rows == 1) $feedback = "Dein Mail wurde an 1 Adresse verschickt.";
    else $feedback = "Dein Mail wurde an ".$num_rows." Adressen verschickt.";
    echo "<div class='buttonbar'>".$feedback."</div>";
    // Kontrollmail
    mail("u.utzinger@sunrise.ch","OLZ Rundmail","Datum: ".date("Y-m-d")."/".date("H:i:s")."\nAdressen: ".implode(', ',$mail_to)."\n".$feedback,$mail_header);
    //echo $result.implode(', ',$mail_to);
    echo "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Neues Rundmail","0")),"")."</div>";
include 'service_01.php';
    }
/*
//Abbruch oder löschen(abbruch)
elseif ($function == "abbruch")
    {echo "<div class='buttonbar'>Es wurde kein Mail verschickt.</div>";
    $input = "";
    $menu = "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Neues Rundmail","0")),"")."</div>";
    }

//Status unbekannt
else
    {$input = "";
    $menu = "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Neues Rundmail","0")),"")."</div>";
    }

echo "<input type=hidden name=edit value='$edit'>\n";
echo "<input type=hidden name=status value='$status'>\n";
echo "<input type=hidden name=uid value='$uid'>";
echo $input . $menu;*/


//-------------------------------------------------------------
// FUNKTION: FORMULAR GENERIEREN
/*function form_mail($input,$error_name,$error_email,$error_eintrag)
{global $text,$betreff,$betreff_vorspann,$text_nachspann;
if ($input == "eingabe")
    {$input_tmp = "<table><tr class='listmenu'><td style='width:20%'>Betreff: </td><td style='width:80%'><input type=text name=betreff style='width:94%;' value='$betreff'><span class='error'>".$error_betreff."</span></td></tr>\n<tr class='listmenu'><td>Mailtext: </td><td><textarea name=text rows='15' style='width:95%; '>$text</textarea><span class='error'>".$error_eintrag."</span></td></tr></table>\n";
    }
elseif ($input == "code")
    {$input_tmp = "<table><tr class='listmenu'><td style='vertical-align:middle;'>Code eingeben:</td><td colspan='2'><input type=text name=uid size='40'></td></tr></table>";
    }
elseif ($input == "vorschau")
    {$input_tmp = "<table><input type=hidden name=text value='$text'>\n";
    $input_tmp .= "<input type=hidden name=betreff value='$betreff'>\n";
    $input_tmp .= "<tr class='listmenu'><td colspan='2'>Rundmail Vorschau</td></tr>";
    $input_tmp .= "<tr class='listmenu'><td style='width:20%'>Betreff: </td><td style='width:80%'><input type=text name=betreffvorschau style='width:94%;' value='$betreff_vorspann$betreff' readonly></td></tr>\n<tr class='listmenu'><td>Mailtext: </td><td><textarea name=textvorschau rows='15' style='width:95%; ' readonly>$text$text_nachspann</textarea><span class='error'>".$error_eintrag."</span></td></tr></table>\n";
    }
return $input_tmp;
}*/
?>
