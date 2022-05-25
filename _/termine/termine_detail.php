<?php

// =============================================================================
// Zeigt geplante und vergangene Termine an.
// =============================================================================

use Doctrine\Common\Collections\Criteria;
use Olz\Entity\Termine\Termin;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/date.php';
require_once __DIR__.'/../config/doctrine_db.php';
require_once __DIR__.'/../config/paths.php';
require_once __DIR__.'/../config/server.php';
require_once __DIR__.'/utils/TermineFilterUtils.php';

$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$validated_get_params = $http_utils->validateGetParams([
    'filter' => new FieldTypes\StringField(['allow_null' => true]),
    'id' => new FieldTypes\IntegerField(['allow_null' => true]),
    'buttontermine' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

$termine_utils = TermineFilterUtils::fromEnv();
$termin_repo = $entityManager->getRepository(Termin::class);
$is_not_archived = $termine_utils->getIsNotArchivedCriteria();
$criteria = Criteria::create()
    ->where(Criteria::expr()->andX(
        $is_not_archived,
        Criteria::expr()->eq('id', $id),
    ))
    ->setFirstResult(0)
    ->setMaxResults(1)
;
$news_entries = $termin_repo->matching($criteria);
$num_news_entries = $news_entries->count();
$no_robots = $num_news_entries !== 1;
$host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
$canonical_uri = "https://{$host}{$_CONFIG->getCodeHref()}termine.php?id={$id}";

require_once __DIR__.'/../components/page/olz_header/olz_header.php';
echo olz_header([
    'title' => "Termin",
    'description' => "Orientierungslauf-Wettkämpfe, OL-Wochen, OL-Weekends, Trainings und Vereinsanlässe der OL Zimmerberg.",
    'norobots' => $no_robots,
    'additional_headers' => [
        "<link rel='canonical' href='{$canonical_uri}'/>",
    ],
]);

$button_name = 'button'.$db_table;
if (isset($_GET[$button_name])) {
    $_POST[$button_name] = $_GET[$button_name];
}
if (isset($_POST[$button_name])) {
    $_SESSION['edit']['db_table'] = $db_table;
}

$zugriff = ((($_SESSION['auth'] ?? null) == 'all') or (in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? '')))) ? '1' : '0';

$sql = "SELECT * FROM {$db_table} WHERE (id = '{$id}') ORDER BY datum DESC";
$result = $db->query($sql);
$row = $result->fetch_assoc();

$id_edit = $_SESSION['id_edit'] ?? ''; // TODO: Entfernen?
echo <<<ZZZZZZZZZZ
<div id='content_rechts' class='optional'>
    <div style='padding:4px 3px 10px 3px;'>
    </div>
</div>
<div id='content_mitte'>
<form name='Formularl' method='post' action='termine.php?id={$id}' enctype='multipart/form-data'>
ZZZZZZZZZZ;

// -------------------------------------------------------------
// DATENSATZ EDITIEREN
if ($zugriff) {
    $functions = ['neu' => 'Neuer Eintrag',
        'edit' => 'Bearbeiten',
        'abbruch' => 'Abbrechen',
        'vorschau' => 'Vorschau',
        'save' => 'Speichern',
        'delete' => 'Löschen',
        'start' => 'start',
        'duplicate' => 'duplicate',
        'undo' => 'undo', ];
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

// -------------------------------------------------------------
// AKTUELL - VORSCHAU
if (($db_edit == "0") or (($do ?? null) == 'vorschau')) {
    require_once __DIR__.'/components/olz_termin_detail/olz_termin_detail.php';
    echo olz_termin_detail([
        'id' => $id,
        'can_edit' => $zugriff,
        'is_preview' => (($do ?? null) == 'vorschau'),
    ]);
}

echo "</form>
</div>";

require_once __DIR__.'/../components/page/olz_footer/olz_footer.php';
echo olz_footer();
