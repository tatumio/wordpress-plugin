<?php

namespace Hathoriel\Tatum\hooks;

use Hathoriel\Tatum\tatum\Chains;
use Hathoriel\Tatum\tatum\Ipfs;
use Hathoriel\Tatum\tatum\LazyMint;
use Hathoriel\Tatum\tatum\Connector;
use Hathoriel\Tatum\tatum\LazyMintUtils;

class PublicHooks
{
    private $lazyMint;

    public static function instance() {
        return new PublicHooks();
    }

    public function __construct() {
        $this->lazyMint = new LazyMint();
    }

    public function woocommerce_order_set_to_processing($order_id) {
        try {
            $api_key = get_option(TATUM_SLUG . '_api_key');

            if ($api_key) {
                $order = wc_get_order($order_id);
                foreach ($order->get_items() as $order_item) {
                    $product_id = $order_item->get_product_id();
                    $checkedChains = $this->lazyMint->getByProduct($product_id);
                    $isMinted = LazyMintUtils::minted($checkedChains);
                    if (!$isMinted) {
                        $url = Ipfs::storeProductImageToIpfs($product_id, $api_key);
                        if ($url != false) {
                            $this->mintProduct($product_id, $order_id, $api_key, $url);
                        } else {
                            $this->resolveNftError($product_id, $order_id);
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            echo '{"result":"failure","messages":"<ul class=\"woocommerce-error\" role=\"alert\">\n\t\t\t<li>\n\t\t\tNFT minting error occurred. Please try again or contact administrator.\t\t<\/li>\n\t<\/ul>\n","refresh":false,"reload":false}';
            exit();
        }
    }

    public function woocommerce_add_address_checkout($checkout) {
        $chains = array();
        foreach (($tmp = WC()) ? $tmp->cart->get_cart() : $tmp->cart->get_cart() as $cart_item_key => $cart_item) {
            $product_id = $cart_item['product_id'];
            $NFTsToMint = $this->lazyMint->getByProduct($product_id);
            foreach ($NFTsToMint as $nftToMint) {
                array_push($chains, $nftToMint->chain);
            }

        }
        $chains = array_unique($chains);
        foreach ($chains as $chain) {
            echo '<div id="custom_checkout_field">';
            woocommerce_form_field('recipient_blockchain_address_' . $chain, array('type' => 'text', 'class' => array('my-field-class form-row-wide'), 'label' => __($chain . ' address'), 'placeholder' => __($chain . ' address'), 'required' => true), $checkout->get_value('recipient_blockchain_address_' . $chain));
            echo '</div>';
        }
    }

    public function woocommerce_save_address_checkout($order_id) {
        foreach (Chains::getChainCodes() as $chain) {
            if (!empty($_POST['recipient_blockchain_address_' . $chain])) {
                update_post_meta($order_id, 'recipient_blockchain_address_' . $chain, sanitize_text_field($_POST['recipient_blockchain_address_' . $chain]));
            }
        }
    }

    private function mintProduct($product_id, $order_id, $api_key, $url) {
        $lazyMints = $this->lazyMint->getByProduct($product_id);
        foreach ($lazyMints as $lazyMint) {
            $recipient_address = get_post_meta($order_id, 'recipient_blockchain_address_' . $lazyMint->chain, true);

            if ($recipient_address) {
                $transfer_body = array('to' => $recipient_address, 'chain' => $lazyMint->chain, 'url' => "ipfs://$url");
                if ($lazyMint->chain === 'CELO') {
                    $transfer_body['feeCurrency'] = 'CELO';
                }
                $response = Connector::mint_nft($transfer_body, $api_key);

                if (isset($response['txId'])) {
                    $this->lazyMint->updateByProductAndChain($product_id, $lazyMint->chain, array('transaction_id' => $response['txId'], 'order_id' => $order_id));
                } else {
                    $this->lazyMint->updateByProductAndChain($product_id, $lazyMint->chain, array('error_cause' => 'MINT', 'recipient_address' => $recipient_address, 'order_id' => $order_id));
                    wc_add_notice(__('NFT blockchain minting error occurred. Please try again or contact administrator.'), 'error');
                    exit();
                }
            }
        }
    }

    private function resolveNftError($product_id, $order_id) {
        $lazyMints = $this->lazyMint->getByProduct($product_id);
        $recipient_address = get_post_meta($order_id, 'recipient_blockchain_address_' . $lazyMint->chain, true);
        $this->lazyMint->updateByProductAndChain($product_id, $lazyMint->chain, array('error_cause' => 'IPFS', 'recipient_address' => $recipient_address, 'order_id' => $order_id));
    }

    public function addShippingFee() {
        
    }
}