<?php

if (($_SESSION['auth'] == "all") OR (in_array("trainingphotos" ,split(' ',$_SESSION['auth'])))) $zugriff = "1";
else $zugriff = "0";


if ($_GET["ajaxrequest"]=="namen") {
	include_once "admin/olz_init.php";
	if ($_GET["suche"]) {
		$suche = addslashes($_GET["suche"]);
	} else {
		$suche = "";
	}
	$result = mysql_query("SELECT DISTINCT name from trainingsphotos WHERE name LIKE '".$suche."%' ORDER BY name ASC");
	echo "<option>Auswählen...</option>";
	while ($row=mysql_fetch_array($result)) {
		echo "<option>".$row["name"]."</option>";
	}
	return;
}

if ($zugriff) {
	echo "</form><form name='".md5(time())."' method='post' action='index.php' enctype='multipart/form-data'>
<script type='text/javascript'>
function copyname(id) {
	var list = document.getElementById(id+\"sel\");
	document.getElementById(id+\"text\").value = list.options[list.options.selectedIndex].value;
}
function namen(id) {
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp=new ActiveXObject(\"Microsoft.XMLHTTP\");
	}
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById(id+\"sel\").innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open(\"GET\",(\"trainingphotos.php?ajaxrequest=namen&suche=\"+document.getElementById(id+\"text\").value),true);
	xmlhttp.send();
}
</script>
<h2>Photos der Trainings</h2>";
	if ($_POST) {
		$keys = array_keys($_POST);
		for ($i=0; $i<count($keys); $i++) {
			if ($_POST[$keys[$i]]==">del") {
				$result = mysql_query("SELECT pfad from trainingsphotos WHERE id='".base64_decode(substr($keys[$i],32))."'");
				$row = mysql_fetch_array($result);
				unlink($row["pfad"]);
				mysql_query("DELETE from trainingsphotos WHERE id='".base64_decode(substr($keys[$i],32))."'");
			} else {
				mysql_query("UPDATE trainingsphotos SET name='".$_POST[$keys[$i]]."' WHERE id='".base64_decode(substr($keys[$i],32))."'");
			}
		}
	}
	if ($_FILES["trainingphotos"] && $_FILES["trainingphotos"]["error"]!=4) {
		include_once "admin/upload_functions.php";
		$tmp_folder = $_SERVER["DOCUMENT_ROOT"]."/trainingphotos/";
		$date = date("Y-m-d H:i:s");
		$arbeitsverzeichnis = getcwd();
		chdir($tmp_folder);
		mkdir($date,0777);
		chdir($arbeitsverzeichnis);
		$tmp_zip = $tmp_folder.$date."/tmp.zip";
		move_uploaded_file($_FILES["trainingphotos"]["tmp_name"],$tmp_zip);
		$photos = olz_unzip($tmp_zip,$tmp_folder.$date,200);
		unlink($tmp_zip);
		for ($i=0; $i<count($photos); $i++) {
			mysql_query("INSERT into trainingsphotos (name,datum,pfad) VALUES ('Unbekannt','".date("Y-m-d")."','".str_replace($_SERVER["DOCUMENT_ROOT"],"",$photos[$i])."')");
		}
	}
	echo "<input type='file' name='trainingphotos'><input type='submit' value='Senden'>";
	echo "<table><tr><td style='width:20%;'></td><td style='width:80%;'></td></tr>";
	$result = mysql_query("(SELECT * from trainingsphotos WHERE name='Unbekannt') UNION ALL (SELECT * from trainingsphotos WHERE name!='Unbekannt' ORDER BY name ASC)");
	$ids = array();
	while ($row=mysql_fetch_array($result)) {
		if ($row["name"]=="Unbekannt") {
			echo "<tr><td><img src='".$root_path.$row["pfad"]."'></td><td><input type='text' name='".md5(time())."".base64_encode($row["id"])."' value='".$row["name"]."' style='width:100%;' onkeyup='namen(\"".base64_encode($row["id"])."\")' id='".base64_encode($row["id"])."text'><select size='8' style='width:100%;' id='".base64_encode($row["id"])."sel' onchange='copyname(\"".base64_encode($row["id"])."\")'><option>---</option></select></td></tr>";
		} else {
			echo "<tr><td id='l".$row["id"]."'><a href='javascript:showimg();'>Bilder zeigen</a></td><td id='r".$row["id"]."'><a href='javascript:showr".$row["id"]."();'>".((0<strlen($row["name"]))?$row["name"]:"---")."</a></td></tr>";
			echo "<script type='text/javascript'>
function showl".$row["id"]."() {
	document.getElementById(\"l".$row["id"]."\").innerHTML = \"<img src='".$root_path.$row["pfad"]."' style='height:50px;'>\"
}
function showr".$row["id"]."() {
	document.getElementById(\"r".$row["id"]."\").innerHTML = \"<input type='text' name='".md5(time())."".base64_encode($row["id"])."' value='".$row["name"]."' style='width:100%;' onkeyup='namen(\\\"".base64_encode($row["id"])."\\\")' id='".base64_encode($row["id"])."text'><select size='1' style='width:100%;' id='".base64_encode($row["id"])."sel' onchange='copyname(\\\"".base64_encode($row["id"])."\\\")'><option>---</option></select>\"
}
</script>";
			array_push($ids,$row["id"]);
		}
	}
	echo "<script type='text/javascript'>";
	echo "function showimg() {
";
	for ($i=0; $i<count($ids); $i++) {
		echo "showl".$ids[$i]."();\n";
	}
	echo "}
</script>";
	echo "</table>";
} else {
	echo "<h2>Fotoposten</h2>";
	if (isset($_POST["nametest"])) {
		$result = mysql_query("SELECT name from trainingsphotos WHERE name LIKE '".addslashes($_POST["nametest"])."%'");
		$num = mysql_num_rows($result);
		if (0<$num) {
			if (3<=strlen(addslashes($_POST["nametest"]))) {
				$_SESSION["trainingsphotos"] = true;
			} else {
				echo "<p style='color:#aa0000;'>Sorry, dieser Name ist etwas kurz.</p>";
			}
		} else {
			echo "<p style='color:#aa0000;'>Niemand ist unter diesem Namen eingetragen</p>";
		}
	}
	if ($_SESSION["trainingsphotos"]) {
		$result = mysql_query("SELECT DISTINCT name from trainingsphotos ORDER BY name ASC");
		while ($row=mysql_fetch_array($result)) {
			echo "<h2>".$row["name"]."</h2>";
			$result_tmp = mysql_query("SELECT * from trainingsphotos WHERE name='".$row["name"]."' ORDER BY datum DESC");
			$num_tmp = mysql_num_rows($result_tmp);
			$row_tmp = mysql_fetch_array($result_tmp);
			echo "<div><img src='".$root_path.$row_tmp["pfad"]."' style='height:200px;'>";
			for ($i=1; $i<$num_tmp; $i++) {
				$row_tmp = mysql_fetch_array($result_tmp);
				echo "<img src='".$root_path.$row_tmp["pfad"]."' style='height:".str_replace(",",".",(1/($i+1))*200)."px;'>";
			}
			echo "</div><br>";
		}
	} else {
		echo "<p><b>Bitte gib deinen Namen ein, oder den Namen irgendeines Teilnehmenden.</b></p><p><input type='text' name='nametest'>&nbsp;&nbsp;<input type='submit' value='Fotos zeigen'></p><p>Wir versuchen so, Suchmaschinen davon abzuhalten, allenfalls unvorteilhafte Bilder von euch aufzuspüren.</p>";
	}
}

?>