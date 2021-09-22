<?php

namespace Hathoriel\Tatum\tatum;

use Hathoriel\Tatum\base\UtilsProvider;

class LazyMint
{
    use UtilsProvider;

    public function preMint($productId, $chain) {
        global $wpdb;
        $table_name = $this->getTableName("lazy_nft");
        $inserted = $wpdb->insert($table_name, array('product_id' => $productId, 'chain' => $chain));
        echo $inserted;
        $this->debug($inserted);
    }
}