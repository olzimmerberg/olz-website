<?php

namespace Olz\Karten\Components\OlzKarten;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Karten\Components\OlzKartenList\OlzKartenList;

class OlzKarten extends OlzComponent {
    public static $title = "Karten";
    public static $description = "Die OL-Karten, die die OL Zimmerberg aufnimmt, unterhält und verkauft.";

    public function getHtml($args = []): string {
        $this->httpUtils()->validateGetParams([]);
        $db = $this->dbUtils()->getDb();
        $code_href = $this->envUtils()->getCodeHref();

        $out = OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
        ]);

        $out .= "<div class='content-right'>";
        $out .= OlzKartenList::render([]);
        $out .= "</div>
        <div class='content-middle'>";

        $out .= '<script type="text/javascript" src="https://map.search.ch/api/map.js?lang=en"></script>';

        // Karte von mapserch.ch initialisieren
        $sql = "SELECT * FROM karten ORDER BY typ ASC";
        $result = $db->query($sql);
        $pois = "";

        while ($row = mysqli_fetch_array($result)) {
            $name = $row['name'];
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

            $pois .= "theMap.addPOI(new SearchChPOI({ center:[{$center_x},{$center_y}], title:\"\",html:\"{$name}\", maxzoom:128, icon:\"{$code_href}assets/icns/{$icon}\" }));\n";
        }

        $out .= <<<ZZZZZZZZZZ
        <script type="text/javascript">
            var theMap = new SearchChMap({ center:[687500,237000], controls:"zoom,type", type:'aerial', circle:0, poigroups:"", zoom:32 });
            {$pois}
            function goto(x,y,z,name) {
            var x=x;
            var x0 = Number(window.localStorage.getItem('x0'));
            var y=y;
            var y0 = Number(window.localStorage.getItem('y0'));
            var z=z;
            var name=name;
            window.location.hash="top";
            //theMap.removeAllPOIs();
        
            x0 = (x0>'') ? x0 : 687500 ;
            y0 = (y0>'') ? y0 : 237000 ;
            x1 = Math.round((x+x0)/2);
            y1 = Math.round((y+y0)/2);
            z1 = 32;
            theMap.go({ center:[x1,y1], zoom:z1, animated:true });
        
            window.localStorage.setItem('x0', x);
            window.localStorage.setItem('y0', y);
        
            // Add a custom POI
            //theMap.addPOI(new SearchChPOI({ center:[x,y], title:"",html:name, maxzoom:512, icon:"{$code_href}assets/icns/orienteering_forest_16.svg" }));\n
            setTimeout("theMap.go({center:["+x+","+y+"], zoom:"+z+", animated:true})", 2000);
            }
        </script>

        <div class="box test-flaky" id="mapcontainer" style="position:relative;height:650px;z-index:1;"></div>
        <br>
        ZZZZZZZZZZ;

        $out .= "<h2>Kartenverkauf</h2>";
        $out .= "<div style='margin-top:10px;'>";
        $out .= "<table class='liste'>";
        $out .= "<tr class='tablebar'><td style='width:46%;'>Kartentyp</td><td style='width:18%;'>Format</td><td style='width:18%;'>Karte gedruckt</td><td style='width:18%;'>Karte digital</td></tr>
        <tr><td>Wald-/Dorf-Karte</td><td>A4</td><td>2.50</td><td>1.50</td></tr>
        <tr><td></td><td>A3</td><td>4.00</td><td>2.50</td></tr>
        <tr><td>Schulhauskarte</td><td>A4</td><td>1.50</td><td>1.00</td></tr>
        </table><p>(Kartenpreise gültig ab 1.1.2019)</p></div>";
        $out .= OlzEditableText::render(['snippet_id' => 12]);

        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
