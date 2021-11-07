<?php

namespace Hathoriel\Tatum\tatum;

use Hathoriel\Tatum\base\UtilsProvider;
use Hathoriel\Tatum\tatum\Ipfs;
use Hathoriel\Tatum\tatum\BlockchainLink;


class LazyMint
{
    use UtilsProvider;

    private $lazyNft;
    private $preparedNft;
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->lazyNft = $this->getTableName("lazy_nft");
        $this->preparedNft = $this->getTableName("prepared_nft");
    }

    public function insertPrepared($productId, $chain) {
        $this->wpdb->insert($this->preparedNft, array('product_id' => $productId, 'chain' => $chain));
    }

    public function insertLazyNft($preparedId, $orderId, $recipientAddress, $transactionId = null, $errorCause = null) {
        $this->wpdb->insert($this->lazyNft, array(
            'prepared_nft_id' => $preparedId,
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
            'error_cause' => $errorCause,
            'recipient_address' => $recipientAddress
        ));
    }

    public function deletePrepared($product_id) {
        $this->wpdb->query("DELETE FROM $this->preparedNft WHERE product_id = $product_id;");
    }

    public function getPreparedCount() {
        $this->wpdb->get_results("SELECT * FROM $this->preparedNft;");
        return $this->wpdb->num_rows;
    }

    public function getLazyNftCount() {
        $this->wpdb->get_results("SELECT * FROM $this->lazyNft;");
        return $this->wpdb->num_rows;
    }

    public function getPreparedByProduct($product_id) {
        if ($product_id === false) {
            return array();
        }
        return $this->wpdb->get_results("SELECT * FROM $this->preparedNft WHERE product_id = $product_id");
    }

    public function getLazyNftByProductAndOrder($product_id, $order_id) {
        if ($product_id === false) {
            return array();
        }

        if ($order_id === false) {
            return array();
        }
        return $this->wpdb->get_results("SELECT * FROM $this->preparedNft INNER JOIN $this->lazyNft ON $this->lazyNft.prepared_nft_id = $this->preparedNft.id WHERE product_id = $product_id AND order_id = $order_id");
    }

    public function getPrepared() {
        $nfts = $this->wpdb->get_results("SELECT * FROM $this->preparedNft;");
        return self::formatPreparedNfts($nfts);
    }

    public function getMinted() {
        $nfts = $this->wpdb->get_results("SELECT * FROM $this->lazyNft INNER JOIN $this->preparedNft ON $this->lazyNft.prepared_nft_id = $this->preparedNft.id;");
        return self::formatMintedNfts($nfts);
    }

    private static function formatPreparedNfts($nfts) {
        return array_map(function ($nft) {
            $product = wc_get_product($nft->product_id);
            $datetime_created = $product->get_date_created();
            return array(
                "name" => $product->get_title(),
                "imageUrl" => wp_get_attachment_image_url($product->get_image_id(), 'full'),
                "chain" => $nft->chain,
                "created" => $datetime_created,
                "productId" => $product->get_id()
            );
        }, $nfts);
    }

    private static function formatMintedNfts($nfts) {
        return array_map(function ($nft) {
            $order = wc_get_order($nft->order_id);
            $product = wc_get_product($nft->product_id);
            $datetime_created = $product->get_date_created();
            return array(
                "name" => $product->get_title(),
                "imageUrl" => wp_get_attachment_image_url($product->get_image_id(), 'full'),
                "transactionId" => $nft->transaction_id,
                "transactionLink" => BlockchainLink::tx($nft->transaction_id, $nft->chain),
                "errorCause" => $nft->error_cause,
                "chain" => $nft->chain,
                "sold" => $order->get_date_paid(),
                "created" => $datetime_created,
                "productId" => $product->get_id()
            );
        }, $nfts);
    }
}