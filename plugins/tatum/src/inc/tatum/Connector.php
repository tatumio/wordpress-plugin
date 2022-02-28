<?php

namespace Hathoriel\Tatum\tatum;

class Connector
{
    public const TATUM_URL = [
        'eu1' => 'https://api-eu1.tatum.io',
        'us1' => 'https://api-us-west1.tatum.io'
    ];

    const BLOCKCHAIN_URL_MAPPING = array('ETH' => 'ethereum', 'CELO' => 'celo', 'BSC' => 'bsc');

    private static function isResponseOk($response) {
        $server_output = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($server_output['errorCode']) && $server_output['errorCode'] === 'api.key.invalid') {
            throw new \Exception('Invalid API key.');
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code > 299 || $code < 200) {
            throw new \Exception('An error occurred. Please contact Tatum support.');
        }
    }

    private static function get($url, $api_key, $base = null) {
        $args = array('headers' => self::headers($api_key), 'timeout' => 20);
        $baseUrl = $base === null ? self::get_base_url() : $base;
        $response = wp_remote_get($baseUrl . $url, $args);
        $server_output = wp_remote_retrieve_body($response);
        self::isResponseOk($response);
        return json_decode($server_output, true);
    }

    private static function post($url, $body, $api_key) {
        $args = array('headers' => self::headers($api_key), 'body' => json_encode($body), 'timeout' => 20);
        $response = wp_remote_post(self::get_base_url() . $url, $args);
        $server_output = wp_remote_retrieve_body($response);
        self::isResponseOk($response);
        return json_decode($server_output, true);
    }

    public static function get_base_url() {
        $region = get_option(TATUM_SLUG . '_region');

        if (!$region) {
            return self::TATUM_URL['eu1'];
        }

        return self::TATUM_URL[$region];
    }

    private static function headers($api_key): array {
        return array('Accept' => 'application/json', 'Content-Type' => 'application/json', 'x-api-key' => $api_key);
    }

    public static function get_api_version($api_key) {
        try {
            $eu = self::get('/v3/tatum/version', $api_key, self::TATUM_URL['eu1']);
            update_option(TATUM_SLUG . '_region', 'eu1');
            return $eu;
        } catch (\Exception $e) {}

        try {
            $us = self::get('/v3/tatum/version', $api_key, self::TATUM_URL['us1']);
            update_option(TATUM_SLUG . '_region', 'us1');
            return $us;
        } catch (\Exception $e) {}

        throw new \Exception('Invalid API key.');
    }

    public static function generate_wallet($chain, $api_key) {
        return self::get('/v3/' . self::BLOCKCHAIN_URL_MAPPING[$chain] . '/wallet', $api_key);
    }

    public static function generate_account($chain, $xpub, $index, $api_key) {
        return self::get('/v3/' . self::BLOCKCHAIN_URL_MAPPING[$chain] . '/address/' . $xpub . '/' . $index, $api_key);
    }

    public static function generate_private_key($chain, $body, $api_key) {
        return self::post('/v3/' . self::BLOCKCHAIN_URL_MAPPING[$chain] . '/wallet/priv/', $body, $api_key);
    }

    public static function deploy_nft_smart_contract($body, $api_key) {
        return self::post('/v3/nft/deploy', $body, $api_key);
    }

    public static function mint_nft($body, $api_key) {
        return self::post('/v3/nft/mint', $body, $api_key);
    }

    public static function get_balance($chain, $address, $api_key) {
        return self::get('/v3/' . self::BLOCKCHAIN_URL_MAPPING[$chain] . '/account/balance/' . $address, $api_key);
    }

    public static function get_transaction($chain, $hash, $api_key) {
        return self::get('/v3/' . self::BLOCKCHAIN_URL_MAPPING[$chain] . '/transaction/' . $hash, $api_key);
    }

    public static function get_nonce($chain, $address, $api_key) {
        return self::get('/v3/' . self::BLOCKCHAIN_URL_MAPPING[$chain] . '/transaction/count/' . $address, $api_key);
    }

    public static function transfer_nft_token($body, $api_key) {
        return self::post('/v3/nft/transaction', $body, $api_key);
    }

    public static function estimate($body, $api_key) {
        return self::post('/v3/blockchain/estimate', $body, $api_key);
    }

    public static function get_rate($chain, $fiat, $api_key) {
        return self::get('/v3/tatum/rate/' . $chain . '?basePair=' . $fiat, $api_key);
    }
}