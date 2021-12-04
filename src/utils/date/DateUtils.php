<?php

class DateUtils {
    public static function fromEnv() {
        require_once __DIR__.'/../env/EnvUtils.php';

        $env_utils = EnvUtils::fromEnv();
        $class_name = $env_utils->getDateUtilsClassName();
        $class_args = $env_utils->getDateUtilsClassArgs();

        if ($class_name == 'FixedDateUtils') {
            require_once __DIR__.'/FixedDateUtils.php';
            return new FixedDateUtils($class_args[0]);
        }
        if ($class_name == 'LiveDateUtils') {
            require_once __DIR__.'/LiveDateUtils.php';
            return new LiveDateUtils();
        }
        throw new Exception("Invalid DateUtils class name: {$class_name}");
    }
}
