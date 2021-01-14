<?php

class UserAgentUtils {
    private $userAgentString;

    public function __construct($userAgentString) {
        $this->userAgentString = $userAgentString;
    }

    public function isAndroidDevice(): bool {
        return stripos($this->userAgentString, 'Android') !== false;
    }

    public function isIOsDevice(): bool {
        return $this->isIPhone() || $this->isIPad();
    }

    public function isIPhone(): bool {
        return stripos($this->userAgentString, 'iPhone') !== false;
    }

    public function isIPad(): bool {
        return stripos($this->userAgentString, 'iPad') !== false;
    }

    public static function fromEnv() {
        global $_SERVER;

        return new self(
            $_SERVER['HTTP_USER_AGENT'],
        );
    }
}

function getUserAgentUtilsFromEnv() {
    // @codeCoverageIgnoreStart
    // Reason: functions cannot be covered.
    return UserAgentUtils::fromEnv();
    // @codeCoverageIgnoreEnd
}
