<?php

class ContinueAsyncTaskFetcher {
    public function continueAsyncTask($url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
