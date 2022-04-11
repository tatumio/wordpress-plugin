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

    public static function openSea($tokenId, $chain, $testnet = true) {
        $isTestnet = $testnet ? 'TESTNET' : 'MAINNET';
        $openSeaUrl = Constants::OPEN_SEA_BASE_URL[$isTestnet];
        if ($chain === 'ETH') {
            return $openSeaUrl . "assets/" . Constants::CONTRACT_ADDRESS[$isTestnet][$chain] . "/" . $tokenId;
        }
        return $openSeaUrl . "assets/" . Constants::OPEN_SEA_CHAIN_MAPPING[$isTestnet][$chain] . "/" . Constants::CONTRACT_ADDRESS[$isTestnet][$chain] . "/" . $tokenId;
    }

    public static function formatLink($text, $linkUrl) {
        return "<a href='$linkUrl' target='_blank'>$text</a>";
    }

    private static function getExplorerPrefixes($testnet) {
        return Constants::EXPLORER[$testnet ? 'TESTNET' : 'MAINNET'];
    }
}