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
        $api_key = Connector::get_api_version(get_option(TATUM_SLUG . '_api_key'));
        $lazyMint = new LazyMint();
        return [
            'apiKey' => get_option(TATUM_SLUG . '_api_key'),
            'plan' => 'Advanced',
            'remainingCredits' => ($api_key['creditLimit'] - $api_key['usage']),
            'creditLimit' => $api_key['creditLimit'],
            'usedCredits' => $api_key['usage'],
            'nftCreated' => $lazyMint->getLazyMintCount(),
            'nftSold' => $lazyMint->getMintCount(),
            'isTutorialDismissed' => get_option(TATUM_SLUG . '_is_tutorial_dismissed', false)
        ];
    }

    public static function dismissTutorial() {
        update_option(TATUM_SLUG . '_is_tutorial_dismissed', true);
    }
}