<?php

// TODO: currently unused

//ini_set('memory_limit', '32M');
//ini_set('post_max_size', '100M'); // nur lokal möglich
//ini_set('upload_max_filesize', '100M'); // nur lokal möglich
//ini_set('max_input_time', '420'); // Upload: ADSL 500kb/s > 7s/MB
//ini_set('max_execution_time', '420');
$mem_limit = ini_get('memory_limit');
$img_limit = $mem_limit * 1024 * 1024 / 4 / 1.4; // max. Bildgrösse in Megapixel (Sicherheitsfaktor 1.4)
$ul_limit = ini_get('upload_max_filesize');
