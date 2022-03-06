<?php

namespace Hathoriel\NftMaker\Connectors;

use Hathoriel\NftMaker\Utils\BlockchainLink;
use Hathoriel\NftMaker\Utils\UtilsProvider;


class DbConnector
{
    use UtilsProvider;

    private $lazyNftName;
    private $preparedNftName;
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->lazyNftName = $this->getTableName("lazy_nft");
        $this->preparedNftName = $this->getTableName("prepared_nft");
    }

    public function insertPrepared($productId, $chain) {
        $this->wpdb->insert($this->preparedNftName, array('product_id' => $productId, 'chain' => $chain));
    }

    public function updatePrepared($productId, $chain) {
        $this->wpdb->update($this->preparedNftName, array('chain' => $chain), array('product_id' => $productId));
    }

    public function insertLazyNft($preparedId, $orderId, $recipientAddress, $chain, $transactionId = null, $errorCause = null) {
        $this->wpdb->insert($this->lazyNftName, array(
            'prepared_nft_id' => $preparedId,
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
            'error_cause' => $errorCause,
            'recipient_address' => $recipientAddress,
            'chain' => $chain
        ));
    }

    public function deletePrepared($product_id) {
        $this->wpdb->query("DELETE FROM $this->preparedNftName WHERE product_id = $product_id;");
    }

    public function getPreparedCount() {
        $nfts = $this->wpdb->get_results("SELECT * FROM $this->preparedNftName;");
        return count(self::formatPreparedNfts($nfts));
    }

    public function getLazyNftCount() {
        $nfts = $this->wpdb->get_results("SELECT * FROM $this->lazyNftName INNER JOIN $this->preparedNftName ON $this->lazyNftName.prepared_nft_id = $this->preparedNftName.id;");
        return count(self::formatMintedNfts($nfts));
    }

    public function getPreparedByProduct($product_id) {
        if ($product_id === false) {
            return array();
        }
        return $this->wpdb->get_results("SELECT * FROM $this->preparedNftName WHERE product_id = $product_id");
    }

    public function getLazyNftByProductAndOrder($product_id, $order_id) {
        if ($product_id === false) {
            return array();
        }

        if ($order_id === false) {
            return array();
        }
        return $this->wpdb->get_results("SELECT * FROM $this->preparedNftName INNER JOIN $this->lazyNftName ON $this->lazyNftName.prepared_nft_id = $this->preparedNftName.id WHERE product_id = $product_id AND order_id = $order_id");
    }

    public function getPrepared() {
        $nfts = $this->wpdb->get_results("SELECT * FROM $this->preparedNftName;");
        return self::formatPreparedNfts($nfts);
    }

    public function getMinted() {
        $nfts = $this->wpdb->get_results("SELECT * FROM $this->preparedNftName INNER JOIN $this->lazyNftName ON $this->lazyNftName.prepared_nft_id = $this->preparedNftName.id;");
        return self::formatMintedNfts($nfts);
    }

    private static function formatPreparedNfts($nfts) {
        $formatted = array();
        foreach ($nfts as $nft) {
            $product = wc_get_product($nft->product_id);
            if ($product) {
                $datetime_created = $product->get_date_created();
                array_push($formatted, array(
                    "name" => $product->get_title(),
                    "imageUrl" => wp_get_attachment_image_url($product->get_image_id(), 'full'),
                    "chain" => $nft->chain,
                    "created" => $datetime_created,
                    "productId" => $product->get_id()
                ));
            }
        }
        return $formatted;
    }

    private static function formatMintedNfts($nfts) {
        $formatted = array();
        foreach ($nfts as $nft) {
            $order = wc_get_order($nft->order_id);
            $product = wc_get_product($nft->product_id);
            if ($order && $product) {
                $datetime_created = $product->get_date_created();
                array_push($formatted, array(
                    "name" => $product->get_title(),
                    "imageUrl" => wp_get_attachment_image_url($product->get_image_id(), 'full'),
                    "transactionId" => $nft->transaction_id,
                    "transactionLink" => BlockchainLink::tx($nft->transaction_id, $nft->chain),
                    "errorCause" => $nft->error_cause,
                    "chain" => $nft->chain,
                    "sold" => $order->get_date_paid(),
                    "created" => $datetime_created,
                    "productId" => $product->get_id()
                ));
            }
        }

        return $formatted;
    }
}