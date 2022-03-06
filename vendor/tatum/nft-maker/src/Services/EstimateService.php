<?php

namespace Hathoriel\NftMaker\Services;

use Hathoriel\NftMaker\Connectors\TatumConnector;
use Hathoriel\NftMaker\Utils\Chains;

class EstimateService
{
    private $tatumConnector;
    public function __construct() {
        $this->tatumConnector = new TatumConnector();
    }

    public function estimateMintFeeInCredit($chain) {
        $gasEstimate = $this->tatumConnector->estimate(['chain' => $chain, 'type' => 'MINT_NFT']);
        $rate = $this->tatumConnector->getRate($chain, 'USD');
        return $gasEstimate['gasPrice'] / 1000000000 * $gasEstimate['gasLimit'] * $rate['value'] * 111111;
    }

    public function estimateMintPerMonthInCredits($chain) {
        $credits = $this->estimateMintFeeInCredit($chain);
        return [
            'starter' => round(1000000 / $credits),
            'basic' => round(5000000 / $credits),
            'advanced' => round(25000000 / $credits),
            'chain' => $chain,
            'label' => Chains::getChainLabels()[$chain]
        ];
    }

    public function estimateCountOfMintAllSupportedBlockchain() {
        $chains = Chains::getChainCodes();
        $estimates = [];
        foreach ($chains as $chain) {
            $estimates[] = $this->estimateMintPerMonthInCredits($chain);
        }
        return $estimates;
    }
}