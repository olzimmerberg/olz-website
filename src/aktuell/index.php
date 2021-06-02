<?php

global $db;
require_once __DIR__.'/../config/init.php';
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/paths.php';

session_start_if_cookie_set();

require_once __DIR__.'/../admin/olz_functions.php';

require_once __DIR__.'/../fields/BooleanField.php';
require_once __DIR__.'/../fields/IntegerField.php';
require_once __DIR__.'/../fields/StringField.php';
require_once __DIR__.'/../utils/client/HttpUtils.php';
require_once __DIR__.'/../utils/env/EnvUtils.php';
$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$http_utils->validateGetParams([
    new IntegerField('id', ['allow_null' => true]),
    new BooleanField('archiv', ['allow_null' => true]),
    new StringField('buttonaktuell', ['allow_null' => true]),
], $_GET);

$html_title = "Aktuell";
$article_metadata = "";
$id = $_GET['id'] ?? null;
if ($id !== null) {
    try {
        require_once __DIR__.'/article_metadata.php';
        $article_metadata = get_article_metadata($id);
    } catch (Exception $exc) {
        $http_utils->dieWithHttpError(404);
    }
}

require_once __DIR__.'/../components/page/olz_header/olz_header.php';
echo olz_header([
    'title' => $html_title,
    'description' => "Aktuelle Beiträge, Berichte von Anlässen und weitere Neuigkeiten von der OL Zimmerberg.",
    'additional_headers' => [
        $article_metadata,
    ],
]);

require_once __DIR__.'/../components/common/olz_posting_list_item/olz_posting_list_item.php';

require_once __DIR__.'/../file_tools.php';
require_once __DIR__.'/../image_tools.php';

$db_table = 'aktuell';

if ($id === null) {
    echo "<div id='content_rechts' class='optional'>";
    include __DIR__.'/aktuell_r.php';
    echo "</div>";
    echo "<div id='content_mitte'>";
    echo "<form method='post' action='aktuell.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";

    $year = intval($_DATE->olzDate('jjjj'));
    $sql = <<<ZZZZZZZZZZ
    SELECT
        id,
        datum,
        zeit,
        titel,
        text
    FROM aktuell ak
    WHERE
        YEAR(ak.datum)='{$year}'
        AND ak.on_off='1'
        AND ak.typ NOT LIKE 'box%'
    ORDER BY datum DESC, zeit DESC
    ZZZZZZZZZZ;
    $res = $db->query($sql);

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
        echo "<div class='buttonbar'>\n".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")."</div>";
    }

    while ($row = $res->fetch_assoc()) {
        $icon = "icns/entry_type_aktuell_20.svg";
        $datum = $row['datum'];
        $title = $row['titel'];
        $text = $row['text'];
        $link = "aktuell.php?id=".$row['id'];

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
} else {
    $button_name = 'button'.$db_table;
    if (isset($_GET[$button_name])) {
        $_POST[$button_name] = $_GET[$button_name];
    }
    if (isset($_POST[$button_name])) {
        $_SESSION['edit']['db_table'] = $db_table;
    }

    $zugriff = ((($_SESSION['auth'] ?? null) == 'all') or (in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? '')))) ? '1' : '0';

    echo "
    <div id='content_rechts' class='optional'>
    <form name='Formularr' method='post' action='aktuell.php?id={$id}#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>
    <div>";
    include __DIR__.'/aktuell_r.php';
    echo "</div>
    </form>
    </div>
    <div id='content_mitte'>
    <form name='Formularl' method='post' action='aktuell.php?id={$id}#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";
    include __DIR__.'/aktuell_l.php';
    echo "</form>
    </div>
    ";
}

require_once __DIR__.'/../components/page/olz_footer/olz_footer.php';
echo olz_footer();
