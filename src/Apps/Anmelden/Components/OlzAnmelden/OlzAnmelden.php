<?php

namespace Olz\Apps\Anmelden\Components\OlzAnmelden;

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzAnmelden {
    public static function render() {
        global $db;
        require_once __DIR__.'/../../../../../_/config/init.php';
        require_once __DIR__.'/../../../../../_/config/database.php';
        require_once __DIR__.'/../../../../../_/config/server.php';

        session_start_if_cookie_set();

        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $env_utils = EnvUtils::fromEnv();
        $logger = $env_utils->getLogsUtils()->getLogger('anmelden');
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLogger($logger);
        $http_utils->validateGetParams([
            'id' => new FieldTypes\IntegerField(['allow_null' => true]),
        ], $_GET);

        $out = '';

        require_once __DIR__.'/../../../../../_/components/page/olz_header/olz_header.php';
        $out .= olz_header([
            'title' => 'Anmelden',
            'description' => "Hier kann man sich für OLZ-Anlässe anmelden.",
        ]);

        $css_path = __DIR__.'/../../../../public/jsbuild/app-Anmelden/main.min.css';
        $js_path = __DIR__.'/../../../../public/jsbuild/app-Anmelden/main.min.js';
        $css_modified = is_file($css_path) ? filemtime($css_path) : 0;
        $js_modified = is_file($js_path) ? filemtime($js_path) : 0;
        $css_href = "/jsbuild/app-Anmelden/main.min.css?modified={$css_modified}";
        $js_href = "/jsbuild/app-Anmelden/main.min.js?modified={$js_modified}";

        $js_path = "{$_CONFIG->getCodePath()}anmelden/jsbuild/main.min.js";
        $js_modified = is_file($js_path) ? filemtime($js_path) : 0;

        $out .= "<div id='content_double'><div id='react-root'>Lädt...</div></div>";

        $out .= "<link rel='stylesheet' href='{$css_href}' />";
        $out .= "<script type='text/javascript' src='{$js_href}' onload='olz.loaded()'></script>";

        require_once __DIR__.'/../../../../../_/components/page/olz_footer/olz_footer.php';
        $out .= olz_footer();

        return $out;
    }
}
