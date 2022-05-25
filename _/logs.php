<?php

use Olz\Entity\User;

require_once __DIR__.'/config/init.php';

session_start();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/page/olz_header/olz_header.php';
echo olz_header([
    'title' => "Logs",
    'norobots' => true,
]);

require_once __DIR__.'/config/doctrine_db.php';

$user_repo = $entityManager->getRepository(User::class);
$username = ($_SESSION['user'] ?? null);
$user = $user_repo->findOneBy(['username' => $username]);

echo <<<'ZZZZZZZZZZ'
<style>
.menu-container {
    max-width: none;
} 
.site-container {
    max-width: none;
}
</style>
ZZZZZZZZZZ;

echo "<div id='content_double'>";
if ($user && $user->getZugriff() == 'all') {
    echo <<<'ZZZZZZZZZZ'
    <div class='logs-header'>
        <button type='button' class='form-control btn btn-outline-primary' onclick='olzLogsGetNextLog()'>
            Ältere laden
        </button>
        <select id='log-level-filter-select' class='form-control form-select' onchange='olzLogsLevelFilterChange()'>
            <option value='levels-all' selected>Alle Log-Levels</option>
            <option value='levels-info-higher'>"Info" & höher</option>
            <option value='levels-notice-higher'>"Notice" & höher</option>
            <option value='levels-warning-higher'>"Warning" & höher</option>
            <option value='levels-error-higher'>"Error" & höher</option>
        </select>
    </div>
    <div id='logs'></div>
    <script>
        $(() => {
            olzLogsGetFirstLog();
            olzLogsLevelFilterChange();
        });
    </script>
    ZZZZZZZZZZ;
} else {
    echo "<div id='profile-message' class='alert alert-danger' role='alert'>Kein Zugriff!</div>";
}
echo "</div>";

require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
echo olz_footer();
