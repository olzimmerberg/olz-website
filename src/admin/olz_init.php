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
