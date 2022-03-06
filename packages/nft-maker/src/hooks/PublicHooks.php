<?php

namespace Hathoriel\NftMaker\hooks;

use Hathoriel\NftMaker\Connectors\DbConnector;
use Hathoriel\NftMaker\Utils\AddressValidator;
use Hathoriel\NftMaker\Utils\BlockchainLink;
use Hathoriel\NftMaker\Utils\Chains;

class PublicHooks
{
    private $dbConnector;

    public function __construct() {
        $this->dbConnector = new DbConnector();
    }

    public function checkoutAddBlockchainAddress($checkout) {
        $chains = array();
        foreach (($tmp = WC()) ? $tmp->cart->get_cart() : $tmp->cart->get_cart() as $cart_item_key => $cart_item) {
            $product_id = $cart_item['product_id'];
            $NFTsToMint = $this->dbConnector->getPreparedByProduct($product_id);
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

    public function saveAddressCheckout($order_id) {
        foreach (Chains::getChainCodes() as $chain) {
            if (!empty($_POST['recipient_blockchain_address_' . $chain])) {
                update_post_meta($order_id, 'recipient_blockchain_address_' . $chain, sanitize_text_field($_POST['recipient_blockchain_address_' . $chain]));
            }
        }
    }

    public function validateAddressCheckout() {
        foreach (Chains::getChainCodes() as $chain) {
            $recipient_address = sanitize_text_field($_POST['recipient_blockchain_address_' . $chain]);
            if (isset($_POST['recipient_blockchain_address_' . $chain]) && !AddressValidator::isETHAddress($recipient_address)) {
                wc_add_notice(__('Please enter valid format of your ' . $chain . ' address.'), 'error');
            }
        }
    }

    public function thankYouPageAfterCheckout($thank_you_title, $order) {
        $transactions = '';
        foreach ($order->get_items() as $order_item) {
            $product_id = $order_item->get_product_id();
            if ($product_id) {
                $minted_nfts = $this->dbConnector->getLazyNftByProductAndOrder($product_id, $order->get_id());
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

    public static function instance() {
        return new PublicHooks();
    }
}