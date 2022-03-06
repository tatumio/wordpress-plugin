<?php

namespace Hathoriel\NftMaker\Services;

use Hathoriel\NftMaker\Connectors\IpfsConnector;
use Hathoriel\NftMaker\Connectors\TatumConnector;
use Hathoriel\NftMaker\Connectors\DbConnector;

class MintService
{
    private $dbConnector;
    private $tatumConnector;
    private $ipfsConnector;

    public function __construct() {
        $this->dbConnector = new DbConnector();
        $this->tatumConnector = new TatumConnector();
        $this->ipfsConnector = new IpfsConnector();
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
                                $this->mintProduct($product_id, $order_id, $url);
                            } catch (\Exception $e) {
                                $recipient_address = get_post_meta($order_id, 'recipient_blockchain_address_' . $preparedNft->chain, true);
                                $this->resolveNftError($order_id, $e->getMessage(), $preparedNft->chain, $preparedNft, $recipient_address);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            var_dump($exception);
            echo '{"result":"failure","messages":"<ul class=\"woocommerce-error\" role=\"alert\">\n\t\t\t<li>\n\t\t\tNFT minting error occurred. Please try again or contact administrator.\t\t<\/li>\n\t<\/ul>\n","refresh":false,"reload":false}';
            exit();
        }
    }

    private function resolveNftError($order_id, $error_message, $chain, $preparedNft, $recipient_address) {
        $this->dbConnector->insertLazyNft($preparedNft->id, $order_id, $recipient_address, $chain, null, $error_message);
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
                    $this->dbConnector->insertLazyNft($preparedNft->id, $order_id, $recipient_address, $preparedNft->chain, $response['txId']);
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