<?php

$olz_api = require __DIR__.'/../olz_api.php';

file_put_contents(
    __DIR__.'/generated_olz_api_types.ts',
    $olz_api->getTypeScriptDefinition('OlzApi')
);

echo "\nOLZ API client generated.\n";
