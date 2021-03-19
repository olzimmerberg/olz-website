<?php

// =============================================================================
// Das Verzeichnis unserer Karten.
// =============================================================================

require_once __DIR__.'/config/database.php';

echo '<script type="text/javascript" src="https://map.search.ch/api/map.js?lang=en"></script>';

// Karte von mapserch.ch initialisieren
$sql = "SELECT * FROM {$db_table} ORDER BY typ ASC";
$result = $db->query($sql);
$pois = "";

while ($row = mysqli_fetch_array($result)) {
    $name = $row['name'];
    $massstab = $row['massstab'];
    $center_x = $row['center_x'];
    $center_y = $row['center_y'];
    $typ = $row['typ'];
    if ($typ == 'ol') {
        $icon = 'orienteering_forest_16.svg';
    } elseif ($typ == 'stadt') {
        $icon = 'orienteering_village_16.svg';
    } elseif ($typ == 'scool') {
        $icon = 'orienteering_scool_16.svg';
    }

    $pois .= "Map.addPOI(new SearchChPOI({ center:[".$center_x.",".$center_y."], title:\"\",html:\"".$name."\", maxzoom:128, icon:\"icns/".$icon."\" }));\n";
}

echo "<script type=\"text/javascript\">
    var Map = new SearchChMap({ center:[687500,237000], controls:\"zoom,type\", type:'aerial', circle:0, poigroups:\"\", zoom:32 });\n
    ".$pois."\n"."function goto(x,y,z,name) {
    var x=x;
    var x0 = Number(window.localStorage.getItem('x0'));
    var y=y;
    var y0 = Number(window.localStorage.getItem('y0'));
    var z=z;
    var name=name;
    window.location.hash=\"top\";
    //Map.removeAllPOIs();

    x0 = (x0>'') ? x0 : 687500 ;
    y0 = (y0>'') ? y0 : 237000 ;
    x1 = Math.round((x+x0)/2);
    y1 = Math.round((y+y0)/2);
    z1 = 32;
    Map.go({ center:[x1,y1], zoom:z1, animated:true });

    window.localStorage.setItem('x0', x);
    window.localStorage.setItem('y0', y);

    // Add a custom POI
    //Map.addPOI(new SearchChPOI({ center:[x,y], title:\"\",html:name, maxzoom:512, icon:\"icns/orienteering_forest_16.svg\" }));\n
    setTimeout(\"Map.go({center:[\"+x+\",\"+y+\"], zoom:\"+z+\", animated:true})\", 2000);
    }
</script>";
?>

<div class="box test-flaky" id="mapcontainer" style="position:relative;height:650px;z-index:1;"></div>
<br>

<?php
echo "<h2>Kartenverkauf</h2>";
echo "<div style='margin-top:10px;'>";
echo "<table class='liste'>";
echo "<tr class='tablebar'><td style='width:46%;'>Kartentyp</td><td style='width:18%;'>Format</td><td style='width:18%;'>Karte gedruckt</td><td style='width:18%;'>Karte digital</td></tr>
<tr><td>Wald-/Dorf-Karte</td><td>A4</td><td>2.50</td><td>1.50</td></tr>
<tr><td></td><td>A3</td><td>4.00</td><td>2.50</td></tr>
<tr><td>Schulhauskarte</td><td>A4</td><td>1.50</td><td>1.00</td></tr>
</table><p>(Kartenpreise gültig ab 1.1.2019)</p></div>";
echo get_olz_text(12);
?>
<!--<div class="nobox">
    Silvia Baumann<br>Seegartenstrasse 26<br>8810 Horgen<br>Tel. 044 726 06 94<br>
    <script type="text/javascript">MailTo("kartenverkauf", "olzimmerberg.ch", "Karten bestellen", "Bestellung%20OL-Karten");
    </script>
</div>

<h2>Links</h2>
<div class="nobox">
    <div class="linkext"><a href="http://www.swiss-orienteering.ch/karten/index.php" target="_blank">
            Kartenverzeichnis swiss orienteering
        </a>
    </div>
</div>-->
