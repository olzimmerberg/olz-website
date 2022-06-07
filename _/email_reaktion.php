<?php

use Olz\Utils\EmailUtils;

require_once __DIR__.'/config/init.php';

session_start();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/page/olz_header/olz_header.php';
echo olz_header([
    'title' => "Reaktion auf E-Mail",
    'description' => "Reaktion auf E-Mail.",
]);

require_once __DIR__.'/config/doctrine_db.php';
require_once __DIR__.'/config/paths.php';

$email_utils = EmailUtils::fromEnv();
$token = $_GET['token'] ?? '';
$js_token = htmlentities(json_encode($token));
$reaction_data = $email_utils->decryptEmailReactionToken($token);

echo "<div id='content_double'>";

if ($reaction_data) {
    if ($reaction_data['action'] == 'unsubscribe') {
        if ($reaction_data['notification_type'] ?? null != null) {
            $question = "<p>Willst du wirklich <b>alle E-Mail dieser Art abbestellen?</b></p>";
        } elseif (isset($reaction_data['notification_type_all'])) {
            $question = "<p>Willst du wirklich <b>jegliche E-Mails von OL Zimmerberg abbestellen?</b></p>";
        } else {
            $question = "<p>Hier ist etwas falsch gelaufen! Dies ist eine unbekannte Aktion. Trotzdem probieren?</p>";
        }
    }
    if ($reaction_data['action'] == 'reset_password') {
        $question = "<p>Willst du wirklich <b>dein Passwort zurücksetzen?</b></p>";
    }
    echo <<<ZZZZZZZZZZ
    {$question}
    <p>
        <a class='btn btn-secondary' href='{$code_href}' role='button'>Abbrechen</a>
        <button class='btn btn-danger' type='submit' onclick='olzExecuteEmailReaction({$js_token})'>Ausführen</button>
    </p>
    <div id='email-reaction-success-message' class='alert alert-success' role='alert'></div>
    <div id='email-reaction-error-message' class='alert alert-danger' role='alert'></div>
    ZZZZZZZZZZ;
} else {
    echo "<div id='profile-message' class='alert alert-danger' role='alert'>Ungültiger Link!</div>";
}

echo "</div>";

require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
echo olz_footer();
