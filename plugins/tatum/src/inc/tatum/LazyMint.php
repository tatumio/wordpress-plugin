<?php

namespace Hathoriel\Tatum\tatum;

use Hathoriel\Tatum\base\UtilsProvider;
use Hathoriel\Tatum\tatum\Ipfs;
use Hathoriel\Tatum\tatum\BlockchainLink;


class LazyMint
{
    use UtilsProvider;

    private $tableName;
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->tableName = $this->getTableName("lazy_nft");
    }

    public function insert($productId, $chain) {
        $this->wpdb->insert($this->tableName, array('product_id' => $productId, 'chain' => $chain));
    }

    public function deleteByProduct($product_id) {
        $this->wpdb->query("DELETE FROM $this->tableName WHERE product_id = $product_id AND transaction_id IS NULL;");
    }

    public function updateByProductAndChain($product_id, $chain, $data) {
        $this->wpdb->update($this->tableName, $data, array('product_id' => $product_id, 'chain' => $chain));
    }

    public function getLazyMintCount() {
        $this->wpdb->get_results("SELECT * FROM $this->tableName");
        return $this->wpdb->num_rows;
    }

    public function getMintCount() {
        $this->wpdb->get_results("SELECT * FROM $this->tableName WHERE transaction_id IS NOT NULL;");
        return $this->wpdb->num_rows;
    }

    public function getByProduct($product_id) {
        if ($product_id === false) {
            return array();
        }
        return $this->wpdb->get_results("SELECT * FROM $this->tableName WHERE product_id = $product_id");
    }

    public function getLazy() {
        $nfts = $this->wpdb->get_results("SELECT * FROM $this->tableName WHERE transaction_id IS NULL AND error_cause IS NULL");
        return self::formatLazyNfts($nfts);
    }

    public function getMinted() {
        $nfts = $this->wpdb->get_results("SELECT * FROM $this->tableName WHERE transaction_id IS NOT NULL OR error_cause IS NOT NULL");
        return self::formatMintedNfts($nfts);
    }

    private static function formatLazyNfts($nfts) {
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