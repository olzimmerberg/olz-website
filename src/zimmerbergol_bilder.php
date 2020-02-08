<?php
// Bilder als 'BildnameBildnr_thumb.jpg' (110x73) und 'BildnameBildnr_gross.jpg' (800x533) abspeichern
$pfad_galerie = "img/zol_richterswil_2019/" ;
$bild_name = "richti";
$breite = 4;
$file_list = scandir($pfad_galerie);
$img_list = array();
foreach($file_list as $this_file){
    if(is_array(getimagesize($pfad_galerie.$this_file))) array_push($img_list,$this_file);
    }
$groesse = count($img_list)/2;

if($groesse > 0 ) {
    echo "<div style='overflow:auto;'><a href='#top' name='impressionen'><h3 style='margin-top:10px;margin-bottom:10px;'>$impressionen</h3></a>";
    echo "<table class='liste'>";
    $reihen = ($groesse - ($groesse%$breite))/$breite;
    if ($groesse%$breite != 0) $reihen = $reihen + 1;
    echo "<tbody id='galerieindex'>";
    for ($i=0;$i<$reihen;$i++)
        {echo "<tr class='thumbs'>";
        for ($n=0; $n<$breite; $n++)
            {$foto_000 = str_pad(($i*$breite+$n+1) ,3, '0', STR_PAD_LEFT);
            if (($i*$breite+$n+1) > $groesse)
                {echo "</tr><td id='galerietd".($i*$breite+$n+1)."'>&nbsp;</td>";
                }
            else
                {
                $bild_nr = substr("0".($i*$breite+$n+1),-2);
                $pfad_thumb = $pfad_galerie.$bild_name.$bild_nr."_thumb.jpg";
                $pfad_img = $pfad_galerie.$bild_name.$bild_nr."_gross.jpg";
                echo "<td id='galerietd".($i*$breite+$n+1)."'>";
                echo "<a href='".$pfad_img."' class='lightview' rel='gallery[myset]'><img src='".$pfad_thumb."' alt='' onerror='onimageloaderror(this)' id='".($foto_000)."'></a>";
                echo "</td>";
                }
            }
        if ($i >= $groesse) break;
        }
    echo "</tr></table></div>";
}
?>