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
            return $api_key_resp;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public static function getApiKey() {
        return [
            'apiKey' => get_option(TATUM_SLUG . '_api_key'),
            'plan' => 'Basic plan',
            'remainingCredits' => 839123,
            'usedCredits' => 32332,
            'nftCreated' => 32,
            'nftSold' => 13
        ];
    }
}