<?php

namespace Hathoriel\Tatum\tatum;

use Hathoriel\Tatum\base\UtilsProvider;

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

    public function getByChainAndProductId($product_id) {
        if($product_id === false) {
            return array();
        }
        return $this->wpdb->get_results("SELECT * FROM $this->tableName WHERE product_id = $product_id");
    }
}