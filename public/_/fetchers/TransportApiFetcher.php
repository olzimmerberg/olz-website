<?php

class TransportApiFetcher {
    /**
     * Fetch a public transport connection.
     *
     * API docs: https://transport.opendata.ch/docs.html#connections
     *
     * @param mixed $request_data
     */
    public function fetchConnection($request_data) {
        $transport_api_connection_url = 'https://transport.opendata.ch/v1/connections';

        $get_params = http_build_query($request_data, '', '&');
        $url = "{$transport_api_connection_url}?{$get_params}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $connection_result = curl_exec($ch);
        $connection_response = json_decode($connection_result, true);
        curl_close($ch);

        return $connection_response;
    }
}
