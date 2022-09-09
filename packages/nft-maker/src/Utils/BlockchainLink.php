<?php

namespace Hathoriel\NftMaker\Utils;

class BlockchainLink
{
    public static function txLink($txHash, $chain, $testnet = true) {
        $prefixes = self::getExplorerPrefixes($testnet);
        return self::formatLink($txHash, $prefixes[$chain] . 'tx/' . $txHash);
    }

    public static function tx($txHash, $chain, $testnet = true) {
        $prefixes = self::getExplorerPrefixes($testnet);
        return $prefixes[$chain] . 'tx/' . $txHash;
    }

    public static function formatLink($text, $linkUrl) {
        return "<a href='$linkUrl' target='_blank'>$text</a>";
    }

    private static function getExplorerPrefixes($testnet) {
        return Constants::EXPLORER[$testnet ? 'TESTNET' : 'MAINNET'];
    }
}