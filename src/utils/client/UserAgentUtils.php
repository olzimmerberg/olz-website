<?php

class UserAgentUtils {
    public function setUserAgentString($userAgentString) {
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
        $user_agent_string = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $user_agent_utils = new self();
        $user_agent_utils->setUserAgentString($user_agent_string);
        return $user_agent_utils;
    }
}
