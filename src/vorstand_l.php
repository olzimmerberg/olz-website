<?php
echo "<h2>Vorstand OL Zimmerberg</h2><p>";
olz_text_insert(8);

//echo "<a href='".$root_path."downloads/OLZ_Organigramm_2013.pdf' target='_blank' style='background-position:left center;'>Organigramm</a></p>";

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
		echo "<td style='width:25%;vertical-align:top;'><img src='".$root_path."olz_mitglieder/".$bild."' title='$name' alt=''><br>
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

<?php
// MITGLIEDER ALBUM
echo "<!--<h2>Mitglieder (letzte Ergänzung: 11.6.2006)</h2>
<table class='galerie'>";

$poeple = array(
array('Albert','Maag','Horgen'),
array('Andrea','Bossert','Wädenswil'),
array('Andreas','Bachmann','Wädenswil'),
array('Annick','Attinger','Adliswil'),
array('Brigitte','Krummenacher','Horgen'),
array('Catrine','Luternauer-Capeder','Altendorf'),
array('Dani','Rohr','Gattikon'),
array('Dominic','Badertscher','Langnau'),
array('Edu','Hatt','Zürich'),
array('Elisabeth','Fuchs Hatt','Zürich'),
array('Esther','Gasser','Oberrieden'),
array('Florian','Attinger','Adliswil'),
array('Frido','Koch','Schönenberg'),
//array('Hansjörg','Gasser','Oberrieden'),
array('Heidi','Gross','Richterswil'),
array('Jan','Hug','Wädenswil'),
array('Jann','Dietrich','Islisberg'),
array('Joel','Neeser','Langnau'),
array('Jörg','Krummenacher','Horgen'),
array('Judith','Attinger','Samstagern'),
array('Lars','Weber','Langnau'),
array('Laura','Borner','Adliswil'),
array('Laura','Laager','Horgen'),
array('Leonhard','Capeder','Horgen'),
array('Lilly','Gross','Richterswil'),
array('Markus','Hotz','Hirzel'),
array('Martin','Gross','Richterswil'),
array('Menga','Rettich','Horgen'),
array('Michi','Laager','Horgen'),
array('Nils','Weber','Langnau'),
array('Noldi','Schneider','Zürich'),
array('Paula','Gross','Richterswil'),
array('Peter','Jäger','Basel'),
//array('Priska','Badertscher','Langnau'),
array('Rahel','Detering-Bossert','Wädenswil'),
array('Serafina','Hatt','Zürich'),
array('Simon','Hatt','Zürich'),
array('Susanne','Laager','Horgen'),
array('Thomas','Lüchinger Bernhard','Zürich'),
array('Tiziana','Rigamonti','Adliswil'),
array('Urs','Utzinger','Thalwil'),
array('Ursina','Mathys','Zürich'),
array('Willi','Streuli','Richterswil')
);
$groesse = sizeof($poeple);
$breite = 4;
$array = array('ä' => 'ae', 'ü' => 'ue', 'ö' => 'oe', '-' => '', ' ' => '');
shuffle($poeple);
$reihen = ($groesse - ($groesse%$breite))/$breite;
if ($groesse%$breite != 0) $reihen = $reihen + 1;

for ($i=0;$i<$reihen;$i++)
	{echo '<tr class=\'thumbs\' style=\'height:130px;\'>';
	for ($n=0; $n<$breite; $n++)
		{$nr = ($i*$breite+$n);
		$file = strtolower(strtr($poeple[$nr][0],$array)).'_'.strtolower(strtr($poeple[$nr][1],$array)).'_'.strtolower(strtr($poeple[$nr][2],$array)).'.jpg';
		if (($i*$breite+$n+1) > $groesse)
			{echo '<td>&nbsp;</td>\n';
			}
		else
			{echo '<td><img src=\'".$root_path."olz_mitglieder/' . $file . '\' title=\'' . $poeple[$nr][0] . ' ' . $poeple[$nr][1] . ', ' . $poeple[$nr][2]. '\' alt=\'\'></td>\n';
			}
		if ($i >= $groesse) break;
		}
	echo '</tr>\n';
	}
echo '<tr class=\'listmenu\'>';
for ($i=0;$i<$breite;$i++)
	{echo '<td>&nbsp;</td>\n';
	}
echo '</tr>';		

echo "</table>-->";
?>