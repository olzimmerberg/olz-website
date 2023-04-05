<?php

use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Startseite\Components\OlzCustomizableHome\OlzCustomizableHome;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';

$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLog($logger);
$http_utils->validateGetParams([
    'id' => new FieldTypes\IntegerField(['allow_null' => true]),
], $_GET);

echo OlzHeader::render([
    'description' => "Eine Übersicht der Neuigkeiten und geplanten Anlässe der OL Zimmerberg.",
]);

$banner_text = OlzEditableText::render(['olz_text_id' => 22]);
if (trim(strip_tags($banner_text)) !== '') {
    echo "<div class='content-full'><div id='important-banner' class='banner'>";
    echo $banner_text;
    echo "</div></div>";
}

echo OlzCustomizableHome::render();

echo "<div style='height:100px;'>&nbsp;</div>";

echo "
<div class='content-right'>
<div>";
include __DIR__.'/startseite_r.php';
echo "</div>
</div>
<div class='content-middle'>";
include __DIR__.'/startseite_l.php';
echo "</div>";

echo OlzFooter::render();
