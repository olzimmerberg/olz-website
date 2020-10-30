<?php

// Datei herunterladen
if ($_GET['ftp_mode'] == 'get_file') {
    require_once __DIR__.'/config/paths.php';
    $pfad = urldecode($_GET['pfad']);
    header("Location: {$data_href}OLZimmerbergAblage/{$pfad}");
    exit();
}

if (!defined('CALLED_THROUGH_INDEX')) {
    session_start();

    require_once __DIR__.'/admin/olz_init.php';
    require_once __DIR__.'/admin/olz_functions.php';
    include __DIR__.'/components/page/olz_header/olz_header.php';
}

// Zugriff prüfen
if (in_array('ftp', explode(' ', $_SESSION['auth'])) or $_SESSION['auth'] == 'all') {
    $var = (isset($_POST['fm_dir']) || isset($_GET['fm_dir'])) ? $fm_dir : $_SESSION['root'];
    $var2 = explode('/', $var);
    $var = (substr($var, -3) == '/..') ? implode('/', array_splice($var2, 0, count($var2) - 2)) : $var; // Übergeordnetes Verzeichnis
    if (isset($_POST['fm_dir'])) {
        if (substr($var, 0, strlen($_SESSION['root'])) !== $_SESSION['root'] and $_SESSION['auth'] != 'all') {
            $fm_error = "<div class='error'>Keine Berechtigung für diese Funktion</div>";
            $_POST['fm_dir'] = $_SESSION['root'];
        }
    } elseif (isset($_GET['fm_dir'])) {
        if (substr($var, 0, strlen($_SESSION['root'])) !== $_SESSION['root'] and $_SESSION['auth'] != 'all') {
            $fm_error = "<div class='error'>Keine Berechtigung für diese Funktion</div>";
            $_GET['fm_dir'] = $_SESSION['root'];
        }
    } else {
        $_GET['fm_dir'] = $var;
    }

    // User 'olzkarten' > darf Daten nicht umbenennen/löschen
    if ($_SESSION['user'] == 'olzkarten') {
        $var = $_GET['fm_action'];
        if (in_array($var, ['confirm_rename_file', 'confirm_rename_directory', 'confirm_delete_file', 'confirm_remove_directory'])) {
            $_GET['fm_action'] = "";
            $_GET['fm_filename'] = "";
            $fm_error = "<div class='error'>Keine Berechtigung für diese Funktion</div>";
        }
    }
}

echo "<div id='content_double'>
<form name='Formularl' method='post' action='webftp.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/library/phpWebFileManager/start.php';
echo "</div>
</form>
</div>";

if (!defined('CALLED_THROUGH_INDEX')) {
    include __DIR__.'/components/page/olz_footer/olz_footer.php';
}
