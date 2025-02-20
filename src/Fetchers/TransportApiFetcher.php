<?php

namespace Olz\Fetchers;

class TransportApiFetcher {
    /**
     * Fetch a public transport connection.
     *
     * @param array<string, mixed> $request_data
     *
     * API docs: https://transport.opendata.ch/docs.html#connections
     */
    public function fetchConnection(array $request_data): mixed {
        $transport_api_connection_url = 'https://transport.opendata.ch/v1/connections';

        $get_params = http_build_query($request_data, '', '&');
        $url = "{$transport_api_connection_url}?{$get_params}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $connection_result = curl_exec($ch);
        $connection_response = json_decode(!is_bool($connection_result) ? $connection_result : '', true);
        curl_close($ch);

        return $connection_response;
    }
}
