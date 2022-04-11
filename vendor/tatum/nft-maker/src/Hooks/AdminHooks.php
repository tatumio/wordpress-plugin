<?php

namespace Hathoriel\NftMaker\Hooks;


use Hathoriel\NftMaker\Connectors\DbConnector;
use Hathoriel\NftMaker\Utils\Chains;
use Hathoriel\NftMaker\Utils\Constants;

class AdminHooks
{
    private $dbConnector;

    public function __construct() {
        $this->dbConnector = new DbConnector();
    }

    public function addNftMakerToProductEditTab($tabs) {
        $tabs['tatum'] = array(
            'label' => 'NFT Maker',
            'target' => 'tatum_product_data',
            'priority' => 21,
        );

        return $tabs;
    }

    public function addIcon() {
        ?>
        <style>

        #woocommerce-product-data .tatum_options.active > a:before {
            background: url(<?php echo plugin_dir_url( 'tatum/public/assets/nft-maker-icon-gray.svg' ). 'nft-maker-icon-gray.svg'; ?>) center center no-repeat;
            content: " " !important;
            background-size: 100%;
            width: 13px;
            height: 13px;
            display: inline-block;
            line-height: 1;
        }

        #woocommerce-product-data .tatum_options > a:before {
            background: url(<?php echo plugin_dir_url( 'tatum/public/assets/nft-maker-icon-blue.svg' ). 'nft-maker-icon-blue.svg'; ?>) center center no-repeat;
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
            background: url(<?php echo plugin_dir_url( 'tatum/public/assets/nft-maker-icon.svg' ). 'nft-maker-icon.svg'; ?>) center center no-repeat;
        }

        #tatum_product_data label {
            width: 200px;
        }

        </style><?php

    }

    public function addNftMakerDataToProductTab($product_id) {
        echo '<div id="tatum_product_data" class="panel woocommerce_options_panel hidden">';

        $preparedNfts = $this->dbConnector->getPreparedByProduct(get_the_ID());

        $this->addNftMakerRadioToProductTab($preparedNfts);
        echo '</div>';
    }

    private function addNftMakerRadioToProductTab($preparedNfts) {
        echo '<h4 style="margin-left: 10px;">Select the chain to mint your NFT on</h4>';

        $selectedChain = '';
        foreach (Constants::CHAIN_LABELS as $chain => $label) {
            foreach ($preparedNfts as $checkedChain) {
                if ($checkedChain->chain === $chain) {
                    $selectedChain = $chain;
                }
            }
        }

        woocommerce_wp_radio(array(
            'id' => 'tatum_chain',
            'label' => __('', 'woocommerce'),
            'value' => $selectedChain,
            'options' => Constants::CHAIN_LABELS
        ));
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
            if (isset($_POST['tatum_chain'])) {
                $this->dbConnector->deletePrepared($product_id);

                if (empty($this->dbConnector->getPreparedByProduct($product_id))) {
                    $this->dbConnector->insertPrepared($product_id, $_POST['tatum_chain']);
                } else {
                    $this->dbConnector->updatePrepared($product_id, $_POST['tatum_chain']);
                }
            }
        }
        set_transient($updating_product_id, $product_id, 2); // change 2 seconds if not enough

    }

    public function update_message($data) {
        if (isset($data['upgrade_notice'])) {
            printf(
                '<div class="update-message">%s</div>',
                wpautop($data['upgrade_notice'])
            );
        }
    }
    public static function instance() {
        return new AdminHooks();
    }
}