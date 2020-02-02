<?php
if ($user == "") {

$db_table = "counter"; //Datenbank-Tabelle
$DateinameIP= $root."/ip.txt"; // IP-Track-Datei
$Zeitsperre = 600; // Zeitsperre für IP-Adresse in Sekunden
$Gefunden = FALSE;
$IPListe = file($DateinameIP);

if(count($IPListe) > 0)
	{foreach($IPListe as $Zeile) // IP prüfen
		{$GesplitteteZeile = explode("|", $Zeile);
		if(($GesplitteteZeile[0]+$Zeitsperre) > time())
			{$NeueIPListe[] = trim($Zeile)."\n"; // neue IP
			}
		}
	if(count($NeueIPListe) > 0)
		{foreach($NeueIPListe as $Zeile)
			{$GesplitteteZeile = explode("|", $Zeile);
			if(trim($GesplitteteZeile[1]) == $_SERVER['REMOTE_ADDR']) //IP Prüfung
				{$Gefunden = TRUE;
				}
			}
		}
	}

$FilePointerIP = fopen($DateinameIP, "w"); // IP-Track-Datei öffnen
if(count($IPListe) > 0 && count($NeueIPListe) > 0)
	{foreach($NeueIPListe as $Zeile) // IP-Liste ändern
		{fwrite($FilePointerIP, trim($Zeile)."\n");
		}
	}
	if(!$Gefunden)
		{fwrite($FilePointerIP, time()."|".$_SERVER['REMOTE_ADDR']."\n"); //IP-Liste ergänzen
		}
	fclose($FilePointerIP);

// Zähler in Datenbank
if(!$Gefunden)
	{$db->query("UPDATE $db_table SET counter_ip = (counter_ip+1) WHERE (page = '$page')");
	}
$db->query("UPDATE $db_table SET counter = (counter+1) WHERE (page = '$page')");
}
?>
