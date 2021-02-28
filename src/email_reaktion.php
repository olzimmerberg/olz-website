<?php

require_once __DIR__.'/config/init.php';

session_start();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/page/olz_header/olz_header.php';
echo olz_header([
    'title' => "Reaktion auf E-Mail",
    'description' => "Reaktion auf E-Mail.",
]);

require_once __DIR__.'/config/doctrine_db.php';
require_once __DIR__.'/model/index.php';
require_once __DIR__.'/utils/notify/EmailUtils.php';

$email_utils = EmailUtils::fromEnv();
$token = $_GET['token'] ?? '';
$reaction_data = $email_utils->decryptEmailReactionToken($token);

echo "<div id='content_double'>";

if ($reaction_data) {
    print_r($reaction_data);
} else {
    echo "<div id='profile-message' class='alert alert-danger' role='alert'>Ungültiger Link!</div>";
}

echo "</div>";

require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
echo olz_footer();
