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

    private static function post($url, $body)
    {
        $ch = curl_init(self::TATUM_URL . $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
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
            'x-api-key:' . get_option('tatum_api_key'),
        ];
    }

    public static function get_api_version()
    {
        return self::get('/v3/tatum/version');
    }

    public static function generate_ethereum_wallet() {
        return self::get('/v3/ethereum/wallet');
    }

    public static function generate_ethereum_account($xpub, $index) {
        return self::get('/v3/ethereum/address/'. $xpub . '/'.$index);
    }

    public static function generate_ethereum_private_key($body) {
        return self::post('/v3/ethereum/wallet/priv/', $body);
    }

    public static function deploy_nft_smart_contract($body) {
        return self::post('/v3/nft/deploy', $body);
    }

    public static function mint_nft($body) {
        return self::post('/v3/nft/mint', $body);
    }

    public static function get_ethereum_balance($address) {
        return self::get('/v3/ethereum/account/balance/'.$address);
    }

    public static function get_ethereum_transaction($hash) {
        return self::get('/v3/ethereum/transaction/'.$hash);
    }
}