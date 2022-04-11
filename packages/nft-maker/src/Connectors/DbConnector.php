<?php

namespace Hathoriel\NftMaker\Connectors;

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

    public function insertLazyNft($preparedId, $orderId, $recipientAddress, $chain, $testnet, $transactionId = null, $errorCause = null) {
        $this->wpdb->insert($this->lazyNftName, array(
            'prepared_nft_id' => $preparedId,
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
            'error_cause' => $errorCause,
            'recipient_address' => $recipientAddress,
            'chain' => $chain,
            'testnet' => $testnet
        ));
    }

    public function deletePrepared($product_id) {
        $this->wpdb->query("DELETE FROM $this->preparedNftName WHERE product_id = $product_id;");
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
        return $this->wpdb->get_results("SELECT * FROM $this->preparedNftName;");
    }

    public function getMinted() {
        return $this->wpdb->get_results("SELECT * FROM $this->preparedNftName INNER JOIN $this->lazyNftName ON $this->lazyNftName.prepared_nft_id = $this->preparedNftName.id;");
    }
}