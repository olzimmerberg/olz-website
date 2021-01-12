<?php

require_once __DIR__.'/ApiGenerator.php';
require_once __DIR__.'/../OlzApi.php';

$olz_api = new OlzApi();
$generator = new ApiGenerator();
$typescript_output = $generator->generate($olz_api, 'OlzApi');

file_put_contents(__DIR__.'/OlzApi.ts', $typescript_output);

echo "\n";
echo "OLZ API client generated.\n";
