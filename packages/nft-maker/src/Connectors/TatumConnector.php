<?php

namespace Hathoriel\NftMaker\Connectors;

use Hathoriel\NftMaker\Utils\Constants;

class TatumConnector
{
    private $apiKey;

    /**
     * @return string
     */
    public function getApiKey(): string {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey): void {
        $this->apiKey = $apiKey;
    }


    public function __construct() {
        if (get_option(TATUM_SLUG . '_api_key')) {
            $this->apiKey = get_option(TATUM_SLUG . '_api_key');
        } else {
            $this->apiKey = Constants::DEFAULT_API_KEY;
        }
    }

    public function hasValidApiKey() {
        return $this->apiKey !== Constants::DEFAULT_API_KEY;
    }

    private static function isResponseOk($response) {
        $server_output = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($server_output['errorCode']) && $server_output['errorCode'] === 'api.key.invalid') {
            throw new \Exception('Invalid API key.');
        }

        if (isset($server_output['errorCode']) && $server_output['errorCode'] === 'credit.exhausted') {
            throw new \Exception('Minting this NFT will cause credit exhaustion. Buy higher or enterprise (in case of ETH) API key plan at <a href="https://dashboard.tatum.io" target="_blank" rel="noreferrer">Tatum dashboard.</a>');
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code > 299 || $code < 200) {
            throw new \Exception('An error occurred. Please contact Tatum support.');
        }
    }

    private function get($url, $base = null) {
        $args = array('headers' => self::headers(), 'timeout' => 20);
        $baseUrl = $base === null ? self::getBaseUrl() : $base;
        $response = wp_remote_get($baseUrl . $url, $args);
        $server_output = wp_remote_retrieve_body($response);
        self::isResponseOk($response);
        return json_decode($server_output, true);
    }

    private function post($url, $body) {
        $args = array('headers' => self::headers(), 'body' => json_encode($body), 'timeout' => 20);
        $response = wp_remote_post(self::getBaseUrl() . $url, $args);
        $server_output = wp_remote_retrieve_body($response);
        self::isResponseOk($response);
        return json_decode($server_output, true);
    }

    public static function getBaseUrl() {
        $region = get_option(TATUM_SLUG . '_region');

        if (!$region) {
            return Constants::TATUM_URL['eu1'];
        }

        return Constants::TATUM_URL[$region];
    }

    private function headers(): array {

        return array('Accept' => 'application/json', 'Content-Type' => 'application/json', 'x-api-key' => $this->apiKey, 'User-Agent' => 'Tatum_WordPress_NftMaker/'.TATUM_VERSION);
    }

    public function getApiVersion() {
        try {
            $eu = $this->get('/v3/tatum/version', Constants::TATUM_URL['eu1']);
            update_option(TATUM_SLUG . '_region', 'eu1');
            return $eu;
        } catch (\Exception $e) {
        }

        try {
            $us = $this->get('/v3/tatum/version', Constants::TATUM_URL['us1']);
            update_option(TATUM_SLUG . '_region', 'us1');
            return $us;
        } catch (\Exception $e) {
        }

        throw new \Exception('Invalid API key.');
    }

    public function generateWallet($chain) {
        return $this->get('/v3/' . Constants::BLOCKCHAIN_URL_MAPPING[$chain] . '/wallet');
    }

    public function generateAccount($chain, $xpub, $index) {
        return $this->get('/v3/' . Constants::BLOCKCHAIN_URL_MAPPING[$chain] . '/address/' . $xpub . '/' . $index);
    }

    public function generatePrivateKey($chain, $body) {
        return $this->post('/v3/' . Constants::BLOCKCHAIN_URL_MAPPING[$chain] . '/wallet/priv/', $body);
    }

    public function deployNftSmartContract($body) {
        return $this->post('/v3/nft/deploy', $body);
    }

    public function mintNft($body) {
        return $this->post('/v3/nft/mint', $body);
    }

    public function getBalance($chain, $address) {
        return $this->get('/v3/' . Constants::BLOCKCHAIN_URL_MAPPING[$chain] . '/account/balance/' . $address);
    }

    public function getTransaction($chain, $hash) {
        return $this->get('/v3/' . Constants::BLOCKCHAIN_URL_MAPPING[$chain] . '/transaction/' . $hash);
    }

    public function getNonce($chain, $address) {
        return $this->get('/v3/' . Constants::BLOCKCHAIN_URL_MAPPING[$chain] . '/transaction/count/' . $address);
    }

    public function getNftTransaction($chain, $hash) {
        return $this->get('/v3/nft/transaction/' . $chain . '/' . $hash);
    }

    public function transferNftToken($body) {
        return $this->post('/v3/nft/transaction', $body);
    }

    public function estimate($body) {
        return $this->post('/v3/blockchain/estimate', $body);
    }

    public function getRate($chain, $fiat) {
        return $this->get('/v3/tatum/rate/' . $chain . '?basePair=' . $fiat);
    }
}