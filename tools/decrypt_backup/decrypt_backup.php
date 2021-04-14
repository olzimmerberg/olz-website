<?php

$filename = $_SERVER['argv'][1] ?? null;
if ($filename == null) {
    exit("Please provide the filename of the database backup as the first parameter.");
}
if (!is_file($filename)) {
    exit("No such file: {$filename}.");
}

$password = $_SERVER['argv'][2] ?? null;
if ($password == null) {
    exit("Please provide the password for the prod database backup.");
}

$json_data = explode("\n", file_get_contents($filename))[0];
$decrypt_data = json_decode($json_data, true);
if (!$decrypt_data) {
    $error_message = json_last_error_msg();
    exit("Invalid JSON file: {$filename}; {$error_message}");
}
$ciphertext = base64_decode($decrypt_data['ciphertext']);
$algo = $decrypt_data['algo'] ?? 'aes-256-gcm';
$iv = base64_decode($decrypt_data['iv']);
$tag = base64_decode($decrypt_data['tag']);
$plaintext = openssl_decrypt($ciphertext, $algo, $password, OPENSSL_RAW_DATA, $iv, $tag);

echo $plaintext;
