<?php

namespace Hathoriel\Tatum\tatum;

class BlockchainLink
{
    public static function txLink($txHash, $chain) {
        $prefixes = self::getExplorerPrefixes();
        return self::formatLink($txHash, $prefixes[$chain] . 'tx/' . $txHash);
    }

    public static function tx($txHash, $chain) {
        $prefixes = self::getExplorerPrefixes();
        return $prefixes[$chain] . 'tx/' . $txHash;
    }

    public static function address($address, $chain) {
        $prefixes = self::getExplorerPrefixes();
        return self::formatLink($address, $prefixes[$chain] . 'address/' . $address);
    }

    private static function formatLink($text, $linkUrl) {
        return "<a href='$linkUrl' target='_blank'>$text</a>";
    }

    private static function getExplorerPrefixes() {
        return array(
            'ETH' => "https://etherscan.io/",
            'CELO' => "https://explorer.celo.org/",
            'BSC' => "https://bscscan.com/",
            'MATIC' => "https://polygonscan.com/",
            'ONE' => "https://explorer.harmony.one/"
        );
    }
}