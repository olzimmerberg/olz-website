<?php

if (!defined('CALLED_THROUGH_INDEX')) {
    require_once __DIR__.'/config/init.php';

    session_start_if_cookie_set();

    require_once __DIR__.'/admin/olz_functions.php';
    require_once __DIR__.'/components/page/olz_header/olz_header.php';
    echo olz_header([
        'title' => "Service",
        'description' => "Diverse Online-Tools rund um OL und die OL Zimmerberg.",
    ]);
}

echo "<div id='content_rechts'>";
include __DIR__.'/service_r.php';
echo "</div>
<div id='content_mitte'>";
include __DIR__.'/service_l.php';
echo "</div>";

if (!defined('CALLED_THROUGH_INDEX')) {
    require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
    echo olz_footer();
}
