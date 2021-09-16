<?php

namespace Hathoriel\Tatum\tatum;

use Hathoriel\Tatum\tatum\Connector;

class Estimate
{
    public static function estimateMintFeeInCredit($chain) {
        $api_key = "4ce2274723354471a7b65d1f726a8a68_100";
        $gasEstimate = Connector::estimate(['chain' => $chain, 'type' => 'MINT_NFT'], $api_key);
        $rate = Connector::get_rate($chain, 'USD', $api_key);
        return $gasEstimate['gasPrice'] / 1000000000 * $gasEstimate['gasLimit'] * $rate['value'] * 111111;
    }

    public static function estimateMintPerMonthInCredits($chain) {
        $credits = self::estimateMintFeeInCredit($chain);
        return [
            'starter' => round(1000000 / $credits),
            'basic' => round(5000000 / $credits),
            'advanced' => round(25000000 / $credits),
            'chain' => $chain,
        ];
    }

    public static function estimateCountOfMintAllSupportedBlockchain() {
        $chains = ['ETH', 'BSC', 'MATIC', 'ONE', 'CELO'];
        $estimates = [];
        foreach ($chains as $chain) {
            $estimates[] = self::estimateMintPerMonthInCredits($chain);
        }
        return $estimates;
    }
}