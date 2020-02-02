<?php

require_once("image_tools.php");
$db_table = "galerie";
$monate = array("", "januar", "februar", "märz", "april", "mai", "juni", "juli", "august", "september", "oktober", "november", "dezember");
$breite = 4;
$pfad_galerie = $data_path."img/galerie/";

//-------------------------------------------------------------
// ACTIVATE
//if ($_GET["action"]=="activate" && $zugriff) $db->query("UPDATE $db_table SET on_off='1' WHERE id='".$id."'");

$sql = "SELECT id,datum,on_off FROM $db_table WHERE (id='$id')";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$datum = $row['datum'];

$_SESSION[$db_table.'jahr_'] = date("Y",strtotime($datum));
$jahr = $_SESSION[$db_table.'jahr_'];


//-------------------------------------------------------------
// DATENSATZ EDITIEREN
if ($zugriff)
    {$functions = array('neu' => 'Neue Galerie',
                'edit' => 'Bearbeiten',
                'abbruch' => 'Abbrechen',
                'vorschau' => 'Vorschau',
                'save' => 'Speichern',
                'delete' => 'Löschen',
                'start' => 'start',
                'upload' => 'Upload',
                'undo' => 'undo',
				'activate' => 'Aktivieren');
    }
else
    {$functions = array('neu' => 'Neue Galerie',
                'edit' => 'Bearbeiten',
                'abbruch' => 'Abbrechen',
                'vorschau' => 'Vorschau',
                'save' => 'Speichern',
                'upload' => 'Upload',
                'undo' => 'undo');
    }
$function = array_search($$button_name,$functions);
if ($function!="")
    {include 'admin/admin_db.php';}
if ($_SESSION['edit']['table']==$db_table) $db_edit = "1";
else $db_edit = "0";

//-------------------------------------------------------------
// MENÜ
$res = preg_match("/MSIE ([0-9\.]+)/", $_SERVER["HTTP_USER_AGENT"], $matches);
/*if ($db_edit=='0' && ($zugriff || !$res || 10<=$matches[1]))
    {$btn_aktivieren = ($do!="abbruch") ? "<a href='?action=activate'>Aktivieren</a>" : "" ;
	$btns = array(array("Neue Galerie","0"));
    echo "<div class='buttonbar'><span id='userneuegalerie'></span>".($zugriff?($row["on_off"]!=1?$btn_aktivieren:""):"")."</div>\n<!-- Dies ist nötig, damit Bots nicht dauernd neue Galerien erstellen --><script type='text/javascript'>document.getElementById(\"userneuegalerie\").innerHTML = ".json_encode(olz_buttons("button".$db_table,$btns,"")).";</script>";}*/
if ($db_edit=='0' && ($zugriff || !$res))
    {$btns = array(array("Neue Galerie","0"));
	if ($row['on_off']!='1' AND $$button_name!='Aktivieren' && $zugriff) array_push($btns,array("Aktivieren","1")) ;
    echo "<div class='buttonbar'><span id='userneuegalerie'></span></div>\n<!-- Dies ist nötig, damit Bots nicht dauernd neue Galerien erstellen --><script type='text/javascript'>document.getElementById(\"userneuegalerie\").innerHTML = ".json_encode(olz_buttons("button".$db_table,$btns,"")).";</script>";}


//-------------------------------------------------------------
// GALERIE - VORSCHAU
if ($db_edit=="0" OR $do=="vorschau")
    {$sql = "SELECT * FROM $db_table WHERE (id='$id')";
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);
    //print_r($row);
    //if ($do=="vorschau") $row = $vorschau;
    //print_r($row);
    $datum = $row['datum'];
    $titel = $row['titel'];
    $autor = $row['autor'];
    $typ = $row['typ'];
    $id = $row['id'];

    $content = $row['content'];
    if ($autor > "") $autor = " ($autor)";
    $foto_datum = olz_date("jjmmtt",$datum);
    if ($pfad_tmp) $link_arg = "pfad=".$pfad_tmp;
    else $link_arg = "datum=".$foto_datum;

    if (mysqli_num_rows($result)) echo "<h2>".date("j",strtotime($datum)).". ".ucfirst($monate[date("n",strtotime($datum))])." ".date("Y",strtotime($datum)).": ".$titel.$autor."</h2>";
    echo "<div style='overflow:auto;'><table class='liste'>";

    if ($typ == "foto") {
        // INDEX anzeigen
        $html_tmp = "";
        for ($y=0; true; $y++) {
            $continue = false;
            $html_tmp .= "<tr class='thumbs'>";
            for ($x=0; $x<$breite; $x++) {
                $foto_000 = str_pad(($y*$breite+$x+1) ,3, '0', STR_PAD_LEFT);
                $pfad_img = $pfad_galerie.$id."/img/".$foto_000.".jpg";
                if (is_file($pfad_img)) {
                    $html_tmp .= "<td id='galerietd".($y*$breite+$x+1)."'>";
                    $html_tmp .= olz_image("galerie", $id, $y*$breite+$x+1, 110, "gallery[myset]");
                    $html_tmp .= "</td>";
                } else {
                    $html_tmp .= "<td>&nbsp;</td>";
                    if (!$continue) $continue = $y*$breite+$x;
                }
            }
            $html_tmp .= "</tr>";
            if ($continue) break;
        }
        echo "<tr class='galerie_kopf'><td style='padding:auto;'><a href='index.php?datum=$datum&amp;foto=1' style='display:block;'><img src='icns/slides.gif' class='noborder' alt='' title='Bild für Bild'></a></td><td>&nbsp;</td><td>1...".($continue)."</td><td>&nbsp;</td></tr>";

        echo "<tbody id='galerieindex'>";
		echo $html_tmp;
        //if(strpos($html_tmp,".jpg")>0) echo $html_tmp; // Bild vorhanden?
        echo "</tbody>";
        echo "<tr class='galerie_kopf'><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
    } elseif ($typ == "movie") {
	    // FILM DIASHOW
    	//Zähler
        if ( $user == "") {
            $db->query("UPDATE $db_table SET counter = (counter+1) WHERE (id = '$id')",$conn_id);
        }
        $pfad = "movies/";
        $movie = $foto_datum.".flv";
        $res0 = preg_match("/^https\:\/\/(www\.)?youtu\.be\/([a-zA-Z0-9]{6,})/", $content, $matches0);
    	$res1 = preg_match("/^https\:\/\/(www\.)?youtube\.com/watch\?v\=([a-zA-Z0-9]{6,})/", $content, $matches1);
    	$youtube_match = null;
    	if ($res0) $youtube_match = $matches0[2];
    	if ($res1) $youtube_match = $matches1[2];

		$content_to_show = $youtube_match ? "<a href='" . $content . "'>Link zu YouTube, falls das Video nicht abgespielt werden kann</a>" : $content;
        echo "<tr class='galerie_kopf'><td>&nbsp;</td><td colspan='".($breite-2)."'>".$content_to_show."</td><td>&nbsp;</td></tr>";
        echo "<tr class='foto'><td colspan='$breite' style='background-color:#000000;padding:0px;margin:0px;height:100%;'>";
        echo "<div style='background-image:url(icns/movie_dot.gif);background-repeat:repeat-x;margin:0px;padding:0px;height:24px;'></div>\n";
        if (is_file($pfad.$movie)) {
        	include 'library/flv_info/flvinfo.php';
		    $flvinfo = new FLVInfo();
		    $movie_info = $flvinfo->getInfo($pfad.$movie,true,true);
		    //var_dump($movie_info);
		    $movie_width = $movie_info->video->width;
		    $movie_height = $movie_info->video->height;
		    $movie_height = $movie_height+20;
		    $preview = $pfad.$foto_datum.".jpg";
		    $player = "movies/player.swf";
		    $skin = "glow.zip";
		    //$skin = "";

		    echo "<object id='player1'
		                classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'
		                codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9.0.115'
		                width='$movie_width' height='$movie_height'>
		            <param name=backcolor value='#FFFFFF'>
		            <param name=movie value='".$player."'>
		            <param name=allowfullscreen value='true'>
		            <param name=allowscriptaccess value='always'>
		            <param name='flashvars' value='file=".$movie."&image=".$preview."&fullscreen=true&controlbar=bottom&skin=".$skin."&stretching=exactfit'>

		            <embed name='player1'
		                type='application/x-shockwave-flash'
		                pluginspage='http://www.macromedia.com/go/getflashplayer'
		                width='$movie_width' height='$movie_height'
		                backcolor='#FFFFFF'
		                src='".$player."'
		                allowfullscreen='true'
		                allowscriptaccess='always'
		                flashvars='file=".$movie."&image=".$preview."&fullscreen=true&controlbar=bottom&skin=".$skin."&stretching=exactfit'>
		            </embed>
		        </object>";
        } else {
        	if ($youtube_match != null) {
				echo "<iframe width='560' height='315' src='https://www.youtube.com/embed/" . $youtube_match . "' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";
		    } else {
		    	echo "Weder lokales Video noch YouTube Link vorhanden";
		    }
        }
        echo "<div style='background-image:url(icns/movie_dot.gif);background-repeat:repeat-x;margin:0px;padding:0px;height:24px;'></div>";
        echo "</td></tr>";
    }
    echo "</table></div>";
}
if ($do=="submit" && !$zugriff) mail("simon.hatt@olzimmerberg.ch", "Neue Galerie", "Link: http://olzimmerberg.ch/index.php?page=4&id=".$_SESSION[$db_table."id"]);

?>
