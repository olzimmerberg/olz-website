<?php

// =============================================================================
// Kann auf der Website nach einem Suchbegriff suchen.
// =============================================================================

$length_a = 40;
$length_b = 40;
$search_key = trim(str_replace([",", ".", ";", "   ", "  "], [" ", " ", " ", " ", " "], $search_key));
$search_words = explode(" ", $search_key, 4);
$sql = "";

//TERMINE
for ($n = 0; $n < 3; $n++) {
    $search_key = $search_words[$n];
    if ($n > 0) {
        $or = " AND ";
    }
    if ($search_key > "") {
        $sql1 .= $or."((titel LIKE '%{$search_key}%') OR (text LIKE '%{$search_key}%'))";
    }
    if ($search_key > "") {
        $sql2 .= $or."((name LIKE '%{$search_key}%') OR (eintrag LIKE '%{$search_key}%'))";
    }
    if ($search_key > "") {
        $sql3 .= $or."(titel LIKE '%{$search_key}%')";
    }
    if ($search_key > "") {
        $search .= $or."{$search_key}";
    }
}

echo "<h2>Suchresultate (Suche nach: {$search})</h2>";

if ($sql1 > "") {// TERMINE
    $sql = "select * from termine WHERE ({$sql1}) AND (on_off = 1) ORDER BY datum DESC";
    $result = $db->query($sql);
    $num = mysqli_num_rows($result);
    if ($num > 0) {
        $result_termine .= "<tr><td colspan='2'><h3 class='tablebar'>Termine...</h3></td></tr>";
    }

    for ($i = 0; $i < $num; $i++) {
        $row = mysqli_fetch_array($result);
        $datum = strtotime($row['datum']);
        $titel = strip_tags($row['titel']);
        $text = strip_tags($row['text']);
        $id = $row['id'];
        $jahr = date("Y", $datum);
        $datum = date("j. ", $datum).strftime("%B", $datum).date(" Y", $datum);
        cutout($text);
        $result_termine .= "<tr><td><a href=\"termine.php?show=1&amp;id={$id}&amp;jahr={$jahr}\" class=\"linkint\"><b>{$datum}</b></a></td><td><b><a href=\"termine.php?show=1&amp;id={$id}&amp;jahr={$jahr}\" class=\"linkint\">".$titel."</a></b><br>{$prefix}".$text."{$suffix}</td></tr>";
    }

    //AKTUELL
    $result = $db->query("select * from aktuell WHERE ({$sql1}) AND (on_off = 1) ORDER BY datum DESC");
    $num = mysqli_num_rows($result);
    if ($num > 0) {
        $result_aktuell = "<tr><td colspan='2'><h3 class='tablebar'>Aktuell...</h3></td></tr>";
    }

    for ($i = 0; $i < $num; $i++) {
        $row = mysqli_fetch_array($result);
        $datum = strtotime($row['datum']);
        $titel = strip_tags($row['titel']);
        $text = strip_tags($row['text']).strip_tags($row['textlang']);
        $id = $row['id'];
        $datum = date("j. ", $datum).strftime("%B", $datum).date(" Y", $datum);
        cutout($text);
        $result_aktuell .= "<tr><td><a href=\"aktuell.php?id={$id}\" class=\"linkint\"><b>{$datum}</b></a></td><td><b><a href=\"aktuell.php?id={$id}\" class=\"linkint\">".$titel."</a></b><br>{$prefix}".$text."{$suffix}</td></tr>";
    }

    //FORUM
    $result = $db->query("select * from forum WHERE ({$sql2}) AND (on_off = 1) ORDER BY datum DESC");
    $num = mysqli_num_rows($result);
    if ($num > 0) {
        $result_forum = "<tr><td colspan='2'><h3 class='tablebar'>Forum...</h3></td></tr>";
    }

    for ($i = 0; $i < $num; $i++) {
        $row = mysqli_fetch_array($result);
        $datum = strtotime($row['datum']);
        $titel = strip_tags($row['name']);
        $text = strip_tags($row['eintrag']);
        $id = $row['id'];
        $datum = date("j. ", $datum).strftime("%B", $datum).date(" Y", $datum);
        cutout($text);
        $result_forum .= "<tr><td><a href=\"forum.php?id_forum={$id}\" class=\"linkint\"><b>{$datum}</b></a></td><td><b><a href=\"forum.php?id_forum={$id}\" class=\"linkint\">".$titel."</a></b><br>{$prefix}".$text."{$suffix}</td></tr>";
    }

    // GALERIE
    $result = $db->query("select * from galerie WHERE {$sql3} AND (on_off = 1) ORDER BY datum DESC");
    $num = mysqli_num_rows($result);
    if ($num > 0) {
        $result_galerie = "<tr><td colspan='2'><h3 class='tablebar'>Galerien...</h3></td></tr>";
    }

    for ($i = 0; $i < $num; $i++) {
        $row = mysqli_fetch_array($result);
        $datum = strtotime($row['datum']);
        $datum_ = $row['datum'];
        $titel = strip_tags($row['titel']);
        $id = $row['id'];
        $datum = date("j. ", $datum).strftime("%B", $datum).date(" Y", $datum);
        cutout($text);
        $result_galerie .= "<tr><td><a href=\"galerie.php?id={$id}\" class=\"linkint\"><b>{$datum}</b></a></td><td><b><a href=\"galerie.php?id={$id}\" class=\"linkint\">".$titel."</a></b></td></tr>";
    }

    $text = $result_termine.$result_aktuell.$result_galerie.$result_forum;
    // HIGHLITE
    for ($n = 0; $n < 3; $n++) {
        $search_key = $search_words[$n];
        $search_variants = [
            $search_key,
            strtoupper($search_key),
            ucfirst($search_key), ];
        $replace_variants = [
            '<span style="color:red">'.$search_key.'</span>',
            '<span style="color:red">'.strtoupper($search_key).'</span>',
            '<span style="color:red">'.ucfirst($search_key).'</span>', ];
        $text = str_replace($search_variants, $replace_variants, $text);
    }
}
if ($text != '') {
    echo "<table class='liste'>".$text."</table>";
}

function cutout($text) {
    global $text,$length_a,$length_b,$prefix,$suffix;
    for ($m = 0; $m < 3; $m++) {
        $prefix = "...";
        $suffix = "...";
        $search_key = $search_words[$m];
        $start = strpos(strtolower($text), $search_key);
        if ($start > 0) {
            $m = 3;
        }
    }
    if (($start - $length_a) < 0) {
        $start = $length_a;
        $prefix = "";
    }
    if (strlen($text) < ($length_a + $length_b)) {
        $suffix = "";
    }
    $text = substr($text, ($start - $length_a), ($length_a + $length_b));
}
