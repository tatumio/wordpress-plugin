<?php

class Tatum_Connector
{
    const TATUM_URL = 'https://api-eu1.tatum.io';

    private static function get($url)
    {
        $ch = curl_init(self::TATUM_URL . $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, self::headers());

        $server_output = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (substr($code, 0, strlen('2')) !== '2') {
            throw new Exception($server_output);
        }

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            throw new Exception($error_msg);
        }

        return json_decode($server_output, true);
    }

    private static function headers(): array
    {
        return [
            'Accept: application/json',
            'Content-Type: application/json',
            'x-api-key:' . get_option('api_key'),
        ];
    }

    public static function get_api_version()
    {
        return self::get('/v3/tatum/version');
    }

}
