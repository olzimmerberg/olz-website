<?php

$today = date('Y-m-d');
$link_saturday = 'https://compass-zos.ch/resultate/resultate_11_nat_ol_2022.html';
$link_sunday = 'https://compass-zos.ch/resultate/resultate_12_nat_ol_2022.html';

if ($today === '2022-10-01') {
    header("Location: {$link_saturday}");
    exit('Umleitung...');
}
if ($today === '2022-10-02') {
    header("Location: {$link_sunday}");
    exit('Umleitung...');
}
echo "<html><head><title>Zimmerberg OL live</title><meta name='viewport' content='width=device-width, initial-scale=1.0'/></head><body>";
echo "<h2>Zurzeit ({$today}) sind wir nicht live!</h2>";
echo "<p><a href='{$link_saturday}'>Live-Resultate für Samstag</a></p>";
echo "<p><a href='{$link_sunday}'>Live-Resultate für Sonntag</a></p>";
echo "</body></html>";
