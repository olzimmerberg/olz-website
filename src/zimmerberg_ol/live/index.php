<?php

$today = date('Y-m-d');
$link_saturday = 'http://www.compass-zos.ch/resultate/resultate_13_nat_ol_2021.html';
$link_sunday = 'https://liveresultat.orientering.se/followfull.php?comp=20113&lang=de';

if ($today === '2021-10-02') {
    header("Location: {$link_saturday}");
    exit('Umleitung...');
}
if ($today === '2021-10-03') {
    header("Location: {$link_sunday}");
    exit('Umleitung...');
}
echo "<h2>Zurzeit ({$today}) sind wir nicht live!</h2>";
echo "<p><a href='{$link_saturday}'>Live-Resultate für Samstag</a></p>";
echo "<p><a href='{$link_sunday}'>Live-Resultate für Sonntag</a></p>";
