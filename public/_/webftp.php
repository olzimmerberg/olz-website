<?php

// Datei herunterladen
if ($_GET['ftp_mode'] == 'get_file') {
    require_once __DIR__.'/config/paths.php';
    $pfad = urldecode($_GET['pfad']);
    header("Location: {$data_href}OLZimmerbergAblage/{$pfad}");
    exit();
}

require_once __DIR__.'/config/init.php';

session_start();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/page/olz_header/olz_header.php';
require_once __DIR__.'/config/doctrine_db.php';
require_once __DIR__.'/model/index.php';
require_once __DIR__.'/utils/auth/AuthUtils.php';
require_once __DIR__.'/utils/env/EnvUtils.php';
echo olz_header([
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

    echo "<div id='content_double'>
    <form name='Formularl' method='post' action='webftp.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>
    <div>";
    include __DIR__.'/library/phpWebFileManager/start.php';
    echo "</div>
    </form>";

    echo "<br/><br/>
    <p>Experimentell: <a href='{$code_href}webdav/server.php/' class='linkext'>WebDAV im Browser</a></b></p>";

    $auth_utils = AuthUtils::fromEnv();
    $user = $auth_utils->getSessionUser();
    $access_token_repo = $entityManager->getRepository(AccessToken::class);
    $access_token = $access_token_repo->findOneBy(['user' => $user, 'purpose' => 'WebDAV']);
    if ($access_token) {
        $env_utils = EnvUtils::fromEnv();
        $token = $access_token->getToken();
        $code_url = "{$env_utils->getBaseHref()}{$env_utils->getCodeHref()}";
        $webdav_url = "{$code_url}webdav/server.php/access_token__{$token}/";
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
                onclick='return revokeWebdavAccessToken()'
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
                onclick='return generateWebdavAccessToken()'
            >
                WebDAV-Zugang erstellen
            </button>
            <div id='generate-webdav-token-error-message' class='alert alert-danger' role='alert'></div>
        </p>";
    }

    echo "</div>";
} else {
    echo "<div id='content_double'>
    <div id='profile-message' class='alert alert-danger' role='alert'>Da musst du schon eingeloggt sein!</div>
    </div>";
}

require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
echo olz_footer();
