<?php

// =============================================================================
// Code, der für (fast) jede Anfrage ausgeführt wird.
// TODO(simon): Dies soll durch thematisch organisierte Dateien in `config/`
// ersetzt werden.
// =============================================================================

if (isset($_GET['unset'])) {
    unset($_SESSION['edit']);
}

//-----------------------------------------
// KONSTANTEN - DEFAULTS
//-----------------------------------------
require_once __DIR__.'/../config/date.php';

// Spezialkategorien Aktuell
$aktuell_special = [
    "OL-Lager Tesserete 2011" => "lager11",
    "OL-Lager Vinelz 2010" => "lager10",
    "OL-Lager Mannenbach 2009" => "lager09",
    "JWOC 2008" => "jwoc2008",
    "OL-Lager Schwarzenegg 2008" => "lager08",
    "JWOC 2006" => "jwoc",
    "1. Zimmerberg OL 2006" => "zimmerbergol2006", ];

require_once __DIR__.'/../config/paths.php';

//-------------------------------------------
// POST/GET-Variable
//-------------------------------------------
require_once dirname(__DIR__)."/library/webtool/class_security.php";
$s = new security();
$s->set_std_sonderbehandlung(["terminelink" => "sql_safe"]);
$s->check_REQUEST();

require_once __DIR__.'/../config/database.php';

//-------------------------------------------
// Sendmail
//-------------------------------------------
require_once __DIR__.'/../config/mail.php';

//-----------------------------------------
//Speicher für Bildbearbeitung (gdlib)
//-----------------------------------------
require_once __DIR__.'/../config/limits.php';
