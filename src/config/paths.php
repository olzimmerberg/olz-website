<?php

// =============================================================================
// Konfiguration für die Benützung des Dateisystems
// =============================================================================

$data_path = $_SERVER['DOCUMENT_ROOT'].'/';
$data_href = '/';

$code_path = dirname(realpath(__DIR__)).'/';
$code_href = '/_/';

$deploy_path = $_SERVER['DOCUMENT_ROOT'].'/deploy/';
$deploy_href = '/deploy/';

$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
$base_href = "{$protocol}{$_SERVER['HTTP_HOST']}";
