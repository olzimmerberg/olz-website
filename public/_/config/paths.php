<?php

// =============================================================================
// Konfiguration für die Benützung des Dateisystems
// =============================================================================

global $_CONFIG, $data_path, $data_href, $code_path, $code_href, $base_href;

require_once __DIR__.'/server.php';

$data_path = $_CONFIG->getDataPath();
$data_href = $_CONFIG->getDataHref();

$code_path = $_CONFIG->getCodePath();
$code_href = $_CONFIG->getCodeHref();

$base_href = $_CONFIG->getBaseHref();
