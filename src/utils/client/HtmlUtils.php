<?php

class HtmlUtils {
    const EMAIL_REGEX = '([A-Z0-9a-z._%+-]+)@([A-Za-z0-9.-]+\\.[A-Za-z]{2,64})';

    public function sanitize($html) {
        return $this->replaceEmailAdresses($html);
    }

    public function replaceEmailAdresses($html) {
        $html = preg_replace(
            '/<a ([^>]*)href=[\'"]mailto:'.self::EMAIL_REGEX.'\?subject=([^\'"]*)[\'"]([^>]*)>([^<]*)<\/a>/',
            "<script>MailTo(\"$2\", \"$3\", \"$6\", \"$4\")</script>",
            $html
        );
        $html = preg_replace(
            '/<a ([^>]*)href=[\'"]mailto:'.self::EMAIL_REGEX.'[\'"]([^>]*)>([^<]*)<\/a>/',
            "<script>MailTo(\"$2\", \"$3\", \"$5\")</script>",
            $html
        );
        return preg_replace(
            '/(\s|^)'.self::EMAIL_REGEX.'([\s,\.!\?]|$)/',
            "$1<script>MailTo(\"$2\", \"$3\", \"E-Mail\")</script>$4",
            $html
        );
    }

    public static function fromEnv() {
        return new self();
    }
}
