<?php

namespace Hathoriel\Tatum\tatum;

use Hathoriel\Tatum\base\UtilsProvider;
use Hathoriel\Tatum\tatum\Connector;


class Setup
{
    use UtilsProvider;

    private static function isWoocommerceInstalled() {
        return class_exists('WooCommerce');
    }

    public static function getSetup() {
        return ['isWoocommerceInstalled' => self::isWoocommerceInstalled()];
    }

    public static function setApiKey($api_key) {
        try {
            $api_key_resp = Connector::get_api_version($api_key);
            update_option(TATUM_SLUG . '_api_key', $api_key);
            if ($api_key_resp['testnet'] === false && $api_key_resp['price'] !== 0 && $api_key_resp['status'] === 'ACTIVE' && $api_key_resp['expiration'] >= round(microtime(true) * 1000)) {
                $lazyMint = new LazyMint();
                return [
                    'apiKey' => $api_key,
                    'plan' => $api_key['planName'],
                    'remainingCredits' => ($api_key_resp['creditLimit'] - $api_key_resp['usage']),
                    'creditLimit' => $api_key_resp['creditLimit'],
                    'usedCredits' => $api_key_resp['usage'],
                    'nftCreated' => $lazyMint->getPreparedCount(),
                    'nftSold' => $lazyMint->getLazyNftCount(),
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

    public static function getApiKey() {
        $api_key_uuid = get_option(TATUM_SLUG . '_api_key');
        if ($api_key_uuid) {
            $api_key = Connector::get_api_version($api_key_uuid);
            $lazyMint = new LazyMint();
            return [
                'apiKey' => $api_key_uuid,
                'plan' => $api_key['planName'],
                'remainingCredits' => ($api_key['creditLimit'] - $api_key['usage']),
                'creditLimit' => $api_key['creditLimit'],
                'usedCredits' => $api_key['usage'],
                'nftCreated' => $lazyMint->getPreparedCount(),
                'nftSold' => $lazyMint->getLazyNftCount(),
                'isTutorialDismissed' => get_option(TATUM_SLUG . '_is_tutorial_dismissed', false)
            ];
        }
        return array();
    }

    public static function dismissTutorial() {
        update_option(TATUM_SLUG . '_is_tutorial_dismissed', true);
    }
}