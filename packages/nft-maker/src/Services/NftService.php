<?php

namespace Hathoriel\NftMaker\Services;

use Hathoriel\NftMaker\Connectors\DbConnector;
use Hathoriel\NftMaker\Connectors\TatumConnector;
use Hathoriel\NftMaker\Utils\BlockchainLink;
use Hathoriel\NftMaker\Utils\Constants;

class NftService
{
    private $tatumConnector;
    private $dbConnector;

    public function __construct() {
        $this->dbConnector = new DbConnector();
        $this->tatumConnector = new TatumConnector();
    }

    public function getPrepared() {
        $nfts = $this->dbConnector->getPrepared();
        return self::formatPreparedNfts($nfts);
    }

    public function formatPreparedNfts($nfts) {
        $prepared = [];
        foreach ($nfts as $nft) {
            $product = wc_get_product($nft->product_id);
            if ($product) {
                array_push($prepared, self::formatPreparedNft($product, $nft));
            }
        }
        return $prepared;
    }

    public function formatMintedNfts($nfts, $addTokenId = true) {
        $minted = [];
        foreach ($nfts as $nft) {
            $order = wc_get_order($nft->order_id);
            $product = wc_get_product($nft->product_id);
            if ($order && $product) {
                $nftPrepared = self::formatPreparedNft($product, $nft);
                $nftMinted = self::formatMintedNft($order, $nft, $addTokenId);
                array_push($minted, array_merge($nftPrepared, $nftMinted));
            }
        }
        return $minted;
    }

    public function getMinted() {
        $nfts = $this->dbConnector->getMinted();
        return $this->formatMintedNfts($nfts);
    }

    public function getPreparedCount() {
        $nfts = $this->dbConnector->getPrepared();
        return count($this->formatPreparedNfts($nfts));
    }

    public function getMintedCount() {
        $nfts = $this->dbConnector->getMinted();
        return count($this->formatMintedNfts($nfts, false));
    }


    private static function formatPreparedNft($product, $nft) {
        $datetime_created = $product->get_date_created();
        return [
            "name" => $product->get_title(),
            "imageUrl" => wp_get_attachment_image_url($product->get_image_id(), 'full'),
            "chain" => $nft->chain,
            "created" => $datetime_created,
            "productId" => $product->get_id()
        ];
    }

    private function formatMintedNft($order, $nft, $addTokenId = true) {
        $formatted = [
            "transactionId" => $nft->transaction_id,
            "transactionLink" => BlockchainLink::tx($nft->transaction_id, $nft->chain, $nft->testnet),
            "errorCause" => $nft->error_cause,
            "sold" => $order->get_date_paid(),
        ];

        if ($addTokenId) {
            $nftDetail = $this->getNftDetail($nft->chain, $nft->transaction_id, $nft->testnet);

            if (array_key_exists("tokenId", $nftDetail)) {
                $formatted['tokenId'] = $nftDetail['tokenId'];
            }
            $formatted['contractAddress'] = $nftDetail['contractAddress'];
        }

        return $formatted;
    }


    private function getNftTokenId($transaction, $chain) {
        try {
            $tx = $this->tatumConnector->getNftTransaction($chain, $transaction);
            return hexdec(substr($tx['input'], 74, 64));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getNftDetail($chain, $txId, $testnet) {
        $tokenId = $this->getNftTokenId($txId, $chain);
        $isTestnet = $testnet ? 'TESTNET' : 'MAINNET';
        if (is_null($tokenId)) {
            return [
                'contractAddress' => Constants::CONTRACT_ADDRESS[$isTestnet][$chain],
            ];
        }
        return [
            'contractAddress' => Constants::CONTRACT_ADDRESS[$isTestnet][$chain],
            'tokenId' => $tokenId
        ];
    }
}