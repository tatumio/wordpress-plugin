<?php

namespace Hathoriel\Tatum\tatum;

use Hathoriel\Tatum\base\UtilsProvider;

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
        update_option(TATUM_SLUG . '_api_key', $api_key);
    }
}