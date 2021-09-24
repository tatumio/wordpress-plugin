<?php

namespace Hathoriel\Tatum\hooks;

use Hathoriel\Tatum\tatum\Chains;
use Hathoriel\Tatum\tatum\LazyMint;

class Admin
{
    private $lazyMint;

    public function __construct() {
        $this->lazyMint = new LazyMint();
    }

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

    public function add_product_data_fields($product_id) {
        echo '<div id="tatum_product_data" class="panel woocommerce_options_panel hidden">';
        echo '<h4 style="margin-left: 10px;">Select the chain to mint your NFT on</h4>';


        $checkedChains = $this->lazyMint->getByChainAndProductId(get_the_ID());
        foreach (Chains::getChainLabels() as $chain => $label) {
            $value = '';
            foreach ($checkedChains as $checkedChain) {
                if ($checkedChain->chain === $chain) {
                    $value = 'yes';
                }
            }
            woocommerce_wp_checkbox(array(
                'id' => 'tatum_' . $chain,
                'label' => __($label, 'woocommerce'),
                'value' => $value
            ));
        }
        echo '</div>';
    }

    public function productSave($product_id) {
        $product = wc_get_product($product_id);
        if ($product === NULL || $product === false) {
            throw new \Exception('Cannot find product.');
        }

        $this->lazyMint->deleteByProduct($product_id);

        foreach (Chains::getChainCodes() as $chain) {
            if (isset($_POST['tatum_' . $chain]) && $_POST['tatum_' . $chain] === 'yes') {
                $this->lazyMint->insert($product_id, $chain);
            }

        }
    }

    public static function instance() {
        return new Admin();
    }
}