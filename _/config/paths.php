<?php

// =============================================================================
// Konfiguration für die Benützung des Dateisystems
// =============================================================================

use Olz\Utils\EnvUtils;

global $data_path, $data_href, $code_path, $code_href, $base_href;

$env_utils = EnvUtils::fromEnv();

$data_path = $env_utils->getDataPath();
$data_href = $env_utils->getDataHref();

$code_path = $env_utils->getCodePath();
$code_href = $env_utils->getCodeHref();

$base_href = $env_utils->getBaseHref();
