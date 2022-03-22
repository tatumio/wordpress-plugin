<?php

namespace Hathoriel\NftMaker\Services;

use Hathoriel\NftMaker\Connectors\DbConnector;
use Hathoriel\NftMaker\Connectors\TatumConnector;
use Hathoriel\NftMaker\Utils\UtilsProvider;


class SetupService
{
    private $tatumConnector;
    private $dbConnector;

    public function __construct() {
        $this->tatumConnector = new TatumConnector();
        $this->dbConnector = new DbConnector();
    }

    use UtilsProvider;

    private static function isWoocommerceInstalled() {
        return class_exists('WooCommerce');
    }

    public static function getSetup() {
        return ['isWoocommerceInstalled' => self::isWoocommerceInstalled()];
    }

    public function setApiKey($api_key) {
        try {
            $this->tatumConnector->setApiKey($api_key);
            $api_key_resp = $this->tatumConnector->getApiVersion();
            if ($api_key_resp['testnet'] === false && $api_key_resp['price'] !== 0 && $api_key_resp['status'] === 'ACTIVE' && $api_key_resp['expiration'] >= round(microtime(true) * 1000)) {
                update_option(TATUM_SLUG . '_api_key', $api_key);
                return [
                    'apiKey' => $api_key,
                    'plan' => $api_key['planName'],
                    'remainingCredits' => ($api_key_resp['creditLimit'] - $api_key_resp['usage']),
                    'creditLimit' => $api_key_resp['creditLimit'],
                    'usedCredits' => $api_key_resp['usage'],
                    'nftCreated' => $this->dbConnector->getPreparedCount(),
                    'nftSold' => $this->dbConnector->getLazyNftCount(),
                    'isTutorialDismissed' => get_option(TATUM_SLUG . '_is_tutorial_dismissed', false),
                    'version' => $api_key_resp['version']
                ];
            }
            return [
                'status' => 'error',
                'message' => 'You API key must be paid, mainnet type, active and not expired. If you dont have one, you can buy paid subscription at Tatum dashboard!'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getApiKey() {
        $api_key_uuid = $this->tatumConnector->getApiKey();
        if ($api_key_uuid && $api_key_uuid !== TatumConnector::DEFAULT_API_KEY) {
            $api_key = $this->tatumConnector->getApiVersion();
            return [
                'apiKey' => $api_key_uuid,
                'plan' => $api_key['planName'],
                'remainingCredits' => ($api_key['creditLimit'] - $api_key['usage']),
                'creditLimit' => $api_key['creditLimit'],
                'usedCredits' => $api_key['usage'],
                'nftCreated' => $this->dbConnector->getPreparedCount(),
                'nftSold' => $this->dbConnector->getLazyNftCount(),
                'isTutorialDismissed' => get_option(TATUM_SLUG . '_is_tutorial_dismissed', false)
            ];
        }
        return array();
    }

    public static function dismissTutorial() {
        update_option(TATUM_SLUG . '_is_tutorial_dismissed', true);
    }
}