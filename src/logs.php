<?php

if (!defined('CALLED_THROUGH_INDEX')) {
    require_once __DIR__.'/config/init.php';

    session_start();

    require_once __DIR__.'/admin/olz_functions.php';
    include __DIR__.'/components/page/olz_header/olz_header.php';
}

require_once __DIR__.'/config/doctrine_db.php';
require_once __DIR__.'/model/index.php';

$user_repo = $entityManager->getRepository(User::class);
$username = $_SESSION['user'];
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
    echo "<div id='logs'></div>";
    echo "<script>olzLogsGetLogs();</script>";
} else {
    echo "<div id='profile-message' class='alert alert-danger' role='alert'>Kein Zugriff!</div>";
}
echo "</div>";

if (!defined('CALLED_THROUGH_INDEX')) {
    include __DIR__.'/components/page/olz_footer/olz_footer.php';
}
