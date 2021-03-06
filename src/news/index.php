<?php

global $db;
require_once __DIR__.'/../config/init.php';
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/paths.php';

session_start_if_cookie_set();

require_once __DIR__.'/../admin/olz_functions.php';

require_once __DIR__.'/../fields/BooleanField.php';
require_once __DIR__.'/../fields/IntegerField.php';
require_once __DIR__.'/../fields/StringField.php';
require_once __DIR__.'/../utils/client/HttpUtils.php';
require_once __DIR__.'/../utils/env/EnvUtils.php';
$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$http_utils->validateGetParams([
    new IntegerField('id', ['allow_null' => true]),
    new BooleanField('archiv', ['allow_null' => true]),
    new StringField('buttonaktuell', ['allow_null' => true]),
    new StringField('filter', ['allow_null' => true]),
], $_GET);

$id = $_GET['id'] ?? null;

require_once __DIR__.'/../components/page/olz_header/olz_header.php';

require_once __DIR__.'/../file_tools.php';
require_once __DIR__.'/../image_tools.php';

$db_table = 'aktuell';

if ($id === null) {
    include __DIR__.'/news_list.php';
} else {
    include __DIR__.'/news_detail.php';
}

require_once __DIR__.'/../components/page/olz_footer/olz_footer.php';
echo olz_footer();
