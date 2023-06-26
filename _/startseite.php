<?php

use Olz\Startseite\Components\OlzStartseite\OlzStartseite;

http_response_code(301);
header('Location: /');

echo OlzStartseite::render([]);
