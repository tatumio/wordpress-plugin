<?php

namespace Hathoriel\Tatum\tatum;

class Chains
{
    public static function getChainCodes() {
        return array('ETH', 'BSC', 'CELO', 'MATIC', 'ONE');
    }

    public static function getChainLabels() {
        return array(
            'ETH' => 'Ethereum (ETH)',
            'BSC' => 'Binance Smart Chain (BSC)',
            'CELO' => 'Celo (CELO)',
            'MATIC' => 'Polygon (MATIC)',
            'ONE' => 'Harmony (ONE)'
        );
    }
}