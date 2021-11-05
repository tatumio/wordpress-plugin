<?php

namespace Hathoriel\Tatum\hooks;

use Hathoriel\Tatum\tatum\BlockchainLink;
use Hathoriel\Tatum\tatum\Chains;
use Hathoriel\Tatum\tatum\Ipfs;
use Hathoriel\Tatum\tatum\LazyMint;
use Hathoriel\Tatum\tatum\Connector;

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
                    try {
                        $url = Ipfs::storeProductImageToIpfs($product_id, $api_key);
                        $this->mintProduct($product_id, $order_id, $api_key, $url);
                    } catch (\Exception $e) {
                        $this->resolveNftError($product_id, $order_id, $e->getMessage());
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
                    $this->resolveNftError($product_id, $order_id, 'Cannot mint NFT. Check recipient address or contact support.');
                }
            }
        }
    }

    private function resolveNftError($product_id, $order_id, $error_message) {
        foreach (Chains::getChainCodes() as $chain) {
            if (get_post_meta($order_id, 'recipient_blockchain_address_' . $chain, true)) {
                $recipient_address = get_post_meta($order_id, 'recipient_blockchain_address_' . $chain, true);
                $this->lazyMint->updateByProductAndChain($product_id, $chain, array('error_cause' => $error_message, 'recipient_address' => $recipient_address, 'order_id' => $order_id));

            }
        }
    }

    public function updateThankYouPage($thank_you_title, $order) {
        $transactions = '';
        foreach ($order->get_items() as $order_item) {
            $product_id = $order_item->get_product_id();
            if ($product_id) {
                $minted_nfts = $this->lazyMint->getByProduct($product_id);
                foreach ($minted_nfts as $minted_nft) {
                    if ($minted_nft->transaction_id != "") {
                        $link = BlockchainLink::txLink($minted_nft->transaction_id, $minted_nft->chain);
                        $transactions .= "$link<br>";
                    }
                }
            }
        }
        if ($transactions !== '') {
            return "Thank you. Your order has been received.<br><br> Following transactions were sent: <br><br> $transactions";
        }
        return "Thank you. Your order has been received.";
    }
}