<?php

// =============================================================================
// Kann auf der Website nach einem Suchbegriff suchen.
// =============================================================================

use Olz\Utils\AbstractDateUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;

$date_utils = AbstractDateUtils::fromEnv();
$code_href = EnvUtils::fromEnv()->getCodeHref();
$db = DbUtils::fromEnv()->getDb();

$length_a = 40;
$length_b = 40;
$search_key = trim(str_replace([",", ".", ";", "   ", "  "], [" ", " ", " ", " ", " "], $search_key));
$search_words = explode(" ", $search_key, 4);
$sql = "";

// TERMINE
$or = '';
$sql_termine = '';
$sql_news = '';
$search = '';
for ($n = 0; $n < 3; $n++) {
    $search_key = $search_words[$n] ?? '';
    $search_key = $db->escape_string($search_key);
    if ($n > 0) {
        $or = " AND ";
    }
    if ($search_key > "") {
        $sql_termine .= $or."((title LIKE '%{$search_key}%') OR (text LIKE '%{$search_key}%'))";
    }
    if ($search_key > "") {
        $sql_news .= $or."((title LIKE '%{$search_key}%') OR (teaser LIKE '%{$search_key}%') OR (content LIKE '%{$search_key}%'))";
    }
    if ($search_key > "") {
        $search .= $or."{$search_key}";
    }
}

echo "<h2>Suchresultate (Suche nach: {$search})</h2>";

$result_termine = '';
$result_news = '';

// TERMINE
$sql = "SELECT * FROM termine WHERE ({$sql_termine}) AND (on_off = 1) ORDER BY start_date DESC";
$result = $db->query($sql);
$num = mysqli_num_rows($result);
if ($num > 0) {
    $result_termine .= "<tr><td colspan='2'><h3 class='tablebar'>Termine...</h3></td></tr>";
}

for ($i = 0; $i < $num; $i++) {
    $row = mysqli_fetch_array($result);
    $start_date = strtotime($row['start_date']);
    $title = strip_tags($row['title']);
    $text = strip_tags($row['text']);
    $id = $row['id'];
    $jahr = date("Y", $start_date);
    $start_date = $date_utils->olzDate("t. MM jjjj", $start_date);
    cutout($text);
    $result_termine .= "<tr><td><a href=\"{$code_href}termine/{$id}\" class=\"linkint\"><b>{$start_date}</b></a></td><td><b><a href=\"{$code_href}termine/{$id}\" class=\"linkint\">".$title."</a></b><br>{$prefix}".$text."{$suffix}</td></tr>";
}

// NEWS
$result = $db->query("SELECT * FROM news WHERE ({$sql_news}) AND (on_off = 1) ORDER BY published_date DESC");
$num = mysqli_num_rows($result);
if ($num > 0) {
    $result_news = "<tr><td colspan='2'><h3 class='tablebar'>News...</h3></td></tr>";
}

for ($i = 0; $i < $num; $i++) {
    $row = mysqli_fetch_array($result);
    $published_date = strtotime($row['published_date']);
    $title = strip_tags($row['title']);
    $text = strip_tags($row['teaser']).strip_tags($row['content']);
    $id = $row['id'];
    $published_date = $date_utils->olzDate("t. MM jjjj", $published_date);
    cutout($text);
    $result_news .= "<tr><td><a href=\"news/{$id}\" class=\"linkint\"><b>{$published_date}</b></a></td><td><b><a href=\"news/{$id}\" class=\"linkint\">".$title."</a></b><br>{$prefix}".$text."{$suffix}</td></tr>";
}

$text = $result_termine.$result_news;
// HIGHLITE
for ($n = 0; $n < 3; $n++) {
    $search_key = $search_words[$n] ?? '';
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

if ($text != '') {
    echo "<table class='liste'>".$text."</table>";
}

function cutout($text) {
    global $text,$length_a,$length_b,$prefix,$suffix,$search_words;
    for ($m = 0; $m < 3; $m++) {
        $prefix = "...";
        $suffix = "...";
        $search_key = $search_words[$m] ?? '';
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
    $text = substr($text, $start - $length_a, $length_a + $length_b);
}
