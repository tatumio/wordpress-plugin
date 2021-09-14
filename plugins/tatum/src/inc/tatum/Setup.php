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
        return [ 'isWoocommerceInstalled' => self::isWoocommerceInstalled()];

    }
}