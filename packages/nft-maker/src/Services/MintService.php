<?php

namespace Hathoriel\NftMaker\Services;

use Hathoriel\NftMaker\Connectors\IpfsConnector;
use Hathoriel\NftMaker\Connectors\TatumConnector;
use Hathoriel\NftMaker\Connectors\DbConnector;
use Hathoriel\NftMaker\Utils\BlockchainLink;
use Hathoriel\NftMaker\Utils\Constants;

class MintService
{
    private $dbConnector;
    private $tatumConnector;
    private $ipfsConnector;
    private $setupService;

    public function __construct() {
        $this->dbConnector = new DbConnector();
        $this->tatumConnector = new TatumConnector();
        $this->ipfsConnector = new IpfsConnector();
        $this->setupService = new SetupService();
    }

    public function mintOrder($order_id) {
        try {
            if ($this->tatumConnector->hasValidApiKey()) {
                $order = wc_get_order($order_id);
                foreach ($order->get_items() as $order_item) {
                    for ($i = 0; $i < $order_item->get_quantity(); $i++) {
                        $product_id = $order_item->get_product_id();
                        $preparedNfts = $this->dbConnector->getPreparedByProduct($product_id);
                        foreach ($preparedNfts as $preparedNft) {
                            try {
                                $url = $this->ipfsConnector->storeProductImageToIpfs($product_id);
                                return $this->mintProduct($product_id, $order_id, $url);
                            } catch (\Exception $e) {
                                $recipient_address = get_post_meta($order_id, 'recipient_blockchain_address_' . $preparedNft->chain, true);
                                $this->resolveNftError($order_id, $e->getMessage(), $preparedNft->chain, $preparedNft, $recipient_address);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            echo '{"result":"failure","messages":"<ul class=\"woocommerce-error\" role=\"alert\">\n\t\t\t<li>\n\t\t\tNFT minting error occurred. Please try again or contact administrator.\t\t<\/li>\n\t<\/ul>\n","refresh":false,"reload":false}';
            exit();
        }
    }

    private function resolveNftError($order_id, $error_message, $chain, $preparedNft, $recipient_address) {
        $testnet = $this->setupService->isTestnet();
        $this->dbConnector->insertLazyNft($preparedNft->id, $order_id, $recipient_address, $chain, $testnet, null, $error_message);
    }

    private function mintProduct($product_id, $order_id, $url) {
        $preparedNfts = $this->dbConnector->getPreparedByProduct($product_id);
        foreach ($preparedNfts as $preparedNft) {
            $recipient_address = get_post_meta($order_id, 'recipient_blockchain_address_' . $preparedNft->chain, true);

            if ($recipient_address) {
                $mint_body = array('to' => $recipient_address, 'chain' => $preparedNft->chain, 'url' => "ipfs://$url");
                if ($preparedNft->chain === 'CELO') {
                    $mint_body['feeCurrency'] = 'CELO';
                }
                $response = $this->tatumConnector->mintNft($mint_body);
                if (isset($response['txId'])) {
                    $testnet = $this->setupService->isTestnet();
                    $this->dbConnector->insertLazyNft($preparedNft->id, $order_id, $recipient_address, $preparedNft->chain, $testnet, $response['txId']);
                    return $response;
                } else {
                    $this->resolveNftError($product_id, $order_id, $preparedNft->chain, 'Cannot mint NFT. Check recipient address or contact support.', $recipient_address);
                }
            }
        }
    }

    public static function instance() {
        return new MintService();
    }

}