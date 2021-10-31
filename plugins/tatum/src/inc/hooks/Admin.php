<?php

namespace Hathoriel\Tatum\hooks;

use Hathoriel\Tatum\tatum\Chains;
use Hathoriel\Tatum\tatum\LazyMint;
use Hathoriel\Tatum\tatum\BlockchainLink;
use Hathoriel\Tatum\tatum\LazyMintUtils;

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

        $checkedChains = $this->lazyMint->getByProduct(get_the_ID());
        $isBoughtOrFailed = LazyMintUtils::isBoughOrFailed($checkedChains);
        if ($isBoughtOrFailed) {
            $this->mintedProductDataFields($checkedChains);
        } else {
            $this->lazyMintedDataProductFields($checkedChains);
        }

        echo '</div>';
    }

    private function lazyMintedDataProductFields($lazy_minted_nfts) {
        echo '<h4 style="margin-left: 10px;">Select the chain to mint your NFT on</h4>';


        foreach (Chains::getChainLabels() as $chain => $label) {
            $value = '';
            foreach ($lazy_minted_nfts as $checkedChain) {
                if ($checkedChain->chain === $chain) {
                    $value = 'yes';
                }
            }
            woocommerce_wp_checkbox(array(
                'id' => 'tatum_' . $chain,
                'label' => __($label, 'woocommerce'),
                'value' => $value,
            ));
        }
    }

    private function mintedProductDataFields($minted_nfts) {

        echo '<h4 style="margin-left: 10px;">Product is already minted with transaction hashes:</h4>';


        foreach ($minted_nfts as $chain) {
            $link = BlockchainLink::txLink($chain->transaction_id, $chain->chain);
            echo "<p><span>$chain->chain</span>  <span>$link</span></p>";
        }
    }

    public function productSave($product_id) {
        $product = wc_get_product($product_id);
        if ($product === NULL || $product === false) {
            throw new \Exception('Cannot find product.');
        }

        $updating_product_id = 'update_product_' . $product_id;
        if (false === ($updating_product = get_transient($updating_product_id))) {
            // We'll get here only once! within 2 seconds for each product id;
            // run your code here!
            $this->lazyMint->deleteByProduct($product_id);
            $selectedChains = array();
            foreach (Chains::getChainCodes() as $chain) {
                if (isset($_POST['tatum_' . $chain]) && $_POST['tatum_' . $chain] === 'yes') {
                    $this->lazyMint->insert($product_id, $chain);
                    array_push($selectedChains, $chain);
                }
            }

            if (!empty($selectedChains)) {
                $this->addVariableProducts($product_id, $product, $selectedChains);
            }

        }
        set_transient($updating_product_id, $product_id, 2); // change 2 seconds if not enough

    }

    private function addVariableProducts($product_id, $product, $selectedChains) {
        $attribute_name = 'pa_tatum_nft_chain';
        wp_set_object_terms($product_id, $selectedChains, $attribute_name, true);
        $data = array(
            $attribute_name => array(
                'name' => $attribute_name,
                'value' => '',
                'is_visible' => '1',
                'is_variation' => '1',
                'is_taxonomy' => '1'
            )
        );
        //First getting the Post Meta
        $_product_attributes = get_post_meta($product_id, '_product_attributes');

        //Updating the Post Meta
        update_post_meta($product_id, '_product_attributes', array_merge($_product_attributes, $data));


        foreach ($selectedChains as $chain) {
            $variation_post = array(
                'post_title' => $product->get_title(),
                'post_name' => sanitize_title($product->get_title()),
                'post_status' => 'publish',
                'post_excerpt' => 'My attribute: ',
                'post_parent' => $product->get_id(),
                'post_type' => 'product_variation',
                'guid' => $product->get_permalink(),
                'meta_input' => array(
                    "attribute_$attribute_name" => strtolower($chain)
                )

            );
            // Creating the product variation
            wp_insert_post($variation_post);
        }
    }

    public static function instance() {
        return new Admin();
    }
}