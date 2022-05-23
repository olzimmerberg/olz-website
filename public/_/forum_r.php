<?php

// =============================================================================
// Unser Forum, wo Mitglieder und Besucher Einträge schreiben können.
// =============================================================================

require_once __DIR__.'/components/common/olz_editable_text/olz_editable_text.php';

?>

<h2>Regeln</h2>
<!--<p>
    <b>Im Forum k&ouml;nnen Mitteilungen aller Art platziert werden (Kommentare, Fragen, Hinweise usw.). Dabei ist folgendes zu beachten:
    </b>
    <ul style='list-style:disc inside;'>
        <li>Ein Eintrag muss mit dem richtigen Namen und Vornamen gemacht werden.</li>
        <li>Es muss eine gültige Emailadresse angegeben werden. An diese Emailadresse wird ein Code geschickt, mit welchem der Eintrag später bearbeitet oder gelöscht werden kann. Um die Gefahr von Spam zu minimieren werden Emailadressen verschlüsselt angezeigt.
        </li>
        <li>Es liegt im Ermessen der Website-Betreiber, Einträge jederzeit zu entfernen, insbesondere wenn sie verletzenden Inhalt haben, gegen Gesetze verstossen oder Spam enthalten.
        </li>
    </ul>
</p>-->
<?php
echo olz_editable_text(['olz_text_id' => 4]);
?>
