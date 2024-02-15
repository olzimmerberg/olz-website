<?php

// TODO: Remove this

use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\AccessToken;
use Olz\Utils\AuthUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;

// Datei herunterladen
if (($_GET['ftp_mode'] ?? null) == 'get_file') {
    require_once __DIR__.'/config/paths.php';
    $pfad = urldecode($_GET['pfad']);
    header("Location: {$data_href}OLZimmerbergAblage/{$pfad}");
    exit;
}

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';

echo OlzHeader::render([
    'title' => "Web FTP",
    'norobots' => true,
]);

// Zugriff prüfen
if (in_array('ftp', preg_split('/ /', $_SESSION['auth'] ?? '')) or ($_SESSION['auth'] ?? null) == 'all') {
    if (isset($_POST['fm_dir'])) {
        $fm_dir = $_POST['fm_dir'];
    } elseif (isset($_GET['fm_dir'])) {
        $fm_dir = $_GET['fm_dir'];
    } else {
        $fm_dir = $_SESSION['root'];
    }
    $fm_dir_parts = explode('/', $fm_dir);
    if (substr($fm_dir, -3) == '/..') { // Übergeordnetes Verzeichnis
        $fm_dir = implode('/', array_splice($fm_dir_parts, 0, count($fm_dir_parts) - 2));
    }
    if (isset($_POST['fm_dir'])) {
        if (substr($fm_dir, 0, strlen($_SESSION['root'])) !== $_SESSION['root'] and ($_SESSION['auth'] ?? null) != 'all') {
            $fm_error = "<div class='error'>Keine Berechtigung für diese Funktion</div>";
            $_POST['fm_dir'] = $_SESSION['root'];
        }
    } elseif (isset($_GET['fm_dir'])) {
        if (substr($fm_dir, 0, strlen($_SESSION['root'])) !== $_SESSION['root'] and ($_SESSION['auth'] ?? null) != 'all') {
            $fm_error = "<div class='error'>Keine Berechtigung für diese Funktion</div>";
            $_GET['fm_dir'] = $_SESSION['root'];
        }
    } else {
        $_GET['fm_dir'] = $fm_dir;
    }

    // User 'olzkarten' > darf Daten nicht umbenennen/löschen
    if ($_SESSION['user'] == 'olzkarten') {
        $fm_action = $_GET['fm_action'];
        if (in_array($fm_action, ['confirm_rename_file', 'confirm_rename_directory', 'confirm_delete_file', 'confirm_remove_directory'])) {
            $_GET['fm_action'] = "";
            $_GET['fm_filename'] = "";
            $fm_error = "<div class='error'>Keine Berechtigung für diese Funktion</div>";
        }
    }

    echo <<<'ZZZZZZZZZZ'
    <div class='content-full'>
    <div class='alert alert-danger'>
        <b>Diese Seite wird bald gelöscht.</b>
        Bitte <a href='/apps/files' class='linkint'>Dateien-App</a> verwenden.
        Fehler bitte <script type='text/javascript'>
            olz.MailTo("website", "olzimmerberg.ch", "hier", "Fehler%20OLZ%20Datei-App");
        </script> melden.
    </div>
    <form name='Formularl' method='post' action='webftp.php' enctype='multipart/form-data'>
    <div>
    ZZZZZZZZZZ;
    include __DIR__.'/library/phpWebFileManager/start.php';
    echo "</div>
    </form>";

    echo "<br/><br/>
    <p>Experimentell: <a href='/apps/files/webdav' class='linkext'>WebDAV im Browser</a></b></p>";

    $auth_utils = AuthUtils::fromEnv();
    $entityManager = DbUtils::fromEnv()->getEntityManager();
    $user = $auth_utils->getCurrentUser();
    $access_token_repo = $entityManager->getRepository(AccessToken::class);
    $access_token = $access_token_repo->findOneBy(['user' => $user, 'purpose' => 'WebDAV']);
    if ($access_token) {
        $env_utils = EnvUtils::fromEnv();
        $token = $access_token->getToken();
        $code_url = "{$env_utils->getBaseHref()}{$env_utils->getCodeHref()}";
        $webdav_url = "{$code_url}apps/files/webdav/token__{$token}/";
        $enc_webdav_url = htmlentities($webdav_url);
        echo "<p>
            WebDAV-Zugang:
            <input
                type='text'
                class='form-control'
                readonly
                value='{$webdav_url}'
            />
        </p>
        <p>
            <button
                type='button'
                class='btn btn-danger'
                onclick='return olz.revokeWebdavAccessToken()'
            >
                WebDAV-Zugang deaktivieren
            </button>
            <div id='revoke-webdav-token-error-message' class='alert alert-danger' role='alert'></div>
        </p>";
    } else {
        echo "<p>
            <button
                type='button'
                class='btn btn-secondary'
                onclick='return olz.generateWebdavAccessToken()'
            >
                WebDAV-Zugang erstellen
            </button>
            <div id='generate-webdav-token-error-message' class='alert alert-danger' role='alert'></div>
        </p>";
    }

    echo "</div>";
} else {
    echo "<div class='content-full'>
    <div id='profile-message' class='alert alert-danger' role='alert'>Da musst du schon eingeloggt sein!</div>
    </div>";
}

echo OlzFooter::render();
