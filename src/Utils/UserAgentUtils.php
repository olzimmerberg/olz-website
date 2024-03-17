<?php

namespace Olz\Utils;

class UserAgentUtils {
    use WithUtilsTrait;

    public function getUserAgentString() {
        return $this->server()['HTTP_USER_AGENT'] ?? '';
    }

    public function isAndroidDevice(): bool {
        return stripos($this->getUserAgentString(), 'Android') !== false;
    }

    public function isIOsDevice(): bool {
        return $this->isIPhone() || $this->isIPad();
    }

    public function isIPhone(): bool {
        return stripos($this->getUserAgentString(), 'iPhone') !== false;
    }

    public function isIPad(): bool {
        return stripos($this->getUserAgentString(), 'iPad') !== false;
    }
}
