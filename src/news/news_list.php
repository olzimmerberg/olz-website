<?php

require_once __DIR__.'/../components/common/olz_posting_list_item/olz_posting_list_item.php';
require_once __DIR__.'/../utils/NewsUtils.php';
require_once __DIR__.'/components/olz_news_filter/olz_news_filter.php';

$news_utils = NewsUtils::fromEnv();
$current_filter = json_decode($_GET['filter'] ?? '{}', true);

if (!$news_utils->isValidFilter($current_filter)) {
    $enc_json_filter = urlencode(json_encode($news_utils->getDefaultFilter()));
    $http_utils->redirect("?filter={$enc_json_filter}", 308);
}

echo olz_header([
    'title' => "Aktuell",
    'description' => "Aktuelle Beiträge, Berichte von Anlässen und weitere Neuigkeiten von der OL Zimmerberg.",
]);

echo "<div id='content_rechts'>";
echo "<h2>Filter</h2>";
echo olz_news_filter([]);
echo "</div>";
echo "<div id='content_mitte'>";
echo "<form method='post' action='aktuell.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";

//-------------------------------------------------------------
// DATENSATZ EDITIEREN
if ($zugriff) {
    $functions = [
        'neu' => 'Neuer Eintrag',
        'edit' => 'Bearbeiten',
        'abbruch' => 'Abbrechen',
        'vorschau' => 'Vorschau',
        'save' => 'Speichern',
        'delete' => 'Löschen',
        'start' => 'start',
        'upload' => 'Upload',
        'deletebild1' => 'BILD 1 entfernen',
        'deletebild2' => 'BILD 2 entfernen',
        'deletebild3' => 'BILD 3 entfernen',
        'undo' => 'undo',
    ];
} else {
    $functions = [];
}
$function = array_search($_POST[$button_name] ?? null, $functions);
if ($function != "") {
    include __DIR__.'/../admin/admin_db.php';
}
if (($_SESSION['edit']['table'] ?? null) == $db_table) {
    $db_edit = "1";
} else {
    $db_edit = "0";
}

//-------------------------------------------------------------
// MENÜ
if ($zugriff and ($db_edit == '0')) {
    echo <<<'ZZZZZZZZZZ'
    <div class='feature news'>
        <a
            id='create-news-button'
            class='btn btn-primary'
            href='#'
            role='button'
            data-toggle='modal'
            data-target='#edit-news-modal'
        >
            Neuer Eintrag
        </a>
    </div>
    ZZZZZZZZZZ;
    echo "<div class='buttonbar'>\n".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")."</div>";
}

if (($do ?? null) == 'vorschau') {
    $id = $_SESSION[$db_table."id"];
    require_once __DIR__.'/components/olz_news_article/olz_news_article.php';
    echo olz_news_article([
        'id' => $id,
        'row' => $vorschau,
        'can_edit' => $zugriff,
        'is_preview' => true,
    ]);
    echo "<hr /><br/><br/>";
}

$filter_where = $news_utils->getSqlFromFilter($current_filter);
$sql = <<<ZZZZZZZZZZ
SELECT
    id,
    datum,
    zeit,
    titel,
    text
FROM aktuell n
WHERE
    {$filter_where}
    AND n.on_off='1'
    AND n.typ NOT LIKE 'box%'
ORDER BY datum DESC, zeit DESC
ZZZZZZZZZZ;
$res = $db->query($sql);

while ($row = $res->fetch_assoc()) {
    $icon = "{$code_href}icns/entry_type_aktuell_20.svg";
    $datum = $row['datum'];
    $title = $row['titel'];
    $text = $row['text'];
    $link = "?id=".$row['id'];

    echo olz_posting_list_item([
        'icon' => $icon,
        'date' => $datum,
        'title' => $title,
        'text' => $text,
        'link' => $link,
    ]);
}

echo "</form>";
echo "</div>";
