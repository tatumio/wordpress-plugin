<?php

namespace Hathoriel\Tatum\hooks;

class Admin
{
    public function add_product_data_tab($tabs) {
        $tabs['tatum'] = array(
            'label' => 'Tatum',
            'target' => 'tatum_product_data',
            'priority' => 21,
        );

        return $tabs;
    }

    public function add_product_data_icon() {
        ?>
      <style>
        #woocommerce-product-data .tatum_options.active:hover > a:before,
        #woocommerce-product-data .tatum_options > a:before {
            background: url(<?php echo plugin_dir_url( 'tatum/public/assets/tatum.png' ). 'tatum.png'; ?>) center center no-repeat;
            content: " " !important;
            background-size: 100%;
            width: 13px;
            height: 13px;
            display: inline-block;
            line-height: 1;
        }

        @media only screen and (max-width: 900px) {
            #woocommerce-product-data .tatum_options.active:hover > a:before,
            #woocommerce-product-data .tatum_options > a:before,
            #woocommerce-product-data .tatum_options:hover a:before {
                background-size: 35%;
            }
        }

        .tatum_options:hover a:before {
            background: url(<?php echo plugin_dir_url( 'tatum/public/assets/tatum.png' ). 'tatum.png'; ?>) center center no-repeat;
        }

        #tatum_product_data label {
            width: 200px;
        }

      </style><?php

    }

    public function add_product_data_fields() {
        echo '<div id="tatum_product_data" class="panel woocommerce_options_panel hidden">';
        echo '<h4 style="margin-left: 10px;">Select the chain to mint your NFT on</h4>';
        woocommerce_wp_checkbox(array( // Checkbox.
            'id' => 'tatum_eth',
            'label' => __('Ethereum (ETH)', 'woocommerce'),
        ));

        woocommerce_wp_checkbox(array( // Checkbox.
            'id' => 'tatum_bsc',
            'label' => __('Binance Smart Chain (BSC)', 'woocommerce'),
        ));

        woocommerce_wp_checkbox(array( // Checkbox.
            'id' => 'tatum_matic',
            'label' => __('Polygon (MATIC)', 'woocommerce'),
        ));

        woocommerce_wp_checkbox(array( // Checkbox.
            'id' => 'tatum_one',
            'label' => __('Harmony (ONE)', 'woocommerce'),
        ));

        woocommerce_wp_checkbox(array( // Checkbox.
            'id' => 'tatum_celo',
            'label' => __('Celo (CELO)', 'woocommerce'),
        ));

        echo '</div>';
    }

    public function productSave($product_id) {
        print_r($_POST);
        exit();
        $product = wc_get_product($product_id);
        if ($product === NULL || $product === false) {
            throw new \Exception('Cannot find product.');
        }

    }


    public static function instance() {
        return new Admin();
    }
}