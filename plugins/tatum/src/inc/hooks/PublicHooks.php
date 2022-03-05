<?php

namespace Hathoriel\Tatum\hooks;

use Hathoriel\Tatum\tatum\AddressValidator;
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
                    for ($i = 0; $i < $order_item->get_quantity(); $i++) {
                        $product_id = $order_item->get_product_id();
                        $preparedNfts = $this->lazyMint->getPreparedByProduct($product_id);
                        foreach ($preparedNfts as $preparedNft) {
                            try {
                                $url = Ipfs::storeProductImageToIpfs($product_id, $api_key);
                                $this->mintProduct($product_id, $order_id, $api_key, $url);
                            } catch (\Exception $e) {
                                $recipient_address = get_post_meta($order_id, 'recipient_blockchain_address_' . $preparedNft->chain, true);
                                $this->resolveNftError($order_id, $e->getMessage(), $preparedNft->chain, $preparedNft, $recipient_address);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            var_dump($exception);
            echo '{"result":"failure","messages":"<ul class=\"woocommerce-error\" role=\"alert\">\n\t\t\t<li>\n\t\t\tNFT minting error occurred. Please try again or contact administrator.\t\t<\/li>\n\t<\/ul>\n","refresh":false,"reload":false}';
            exit();
        }
    }

    public function woocommerce_add_address_checkout($checkout) {
        $chains = array();
        foreach (($tmp = WC()) ? $tmp->cart->get_cart() : $tmp->cart->get_cart() as $cart_item_key => $cart_item) {
            $product_id = $cart_item['product_id'];
            $NFTsToMint = $this->lazyMint->getPreparedByProduct($product_id);
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

    public function woocommerce_validate_address_checkout() {
        foreach (Chains::getChainCodes() as $chain) {
            $recipient_address = sanitize_text_field($_POST['recipient_blockchain_address_' . $chain]);
            if (isset($_POST['recipient_blockchain_address_' . $chain]) && !AddressValidator::isETHAddress($recipient_address)) {
                wc_add_notice(__('Please enter valid format of your ' . $chain . ' address.'), 'error');
            }
        }
    }

    private function mintProduct($product_id, $order_id, $api_key, $url) {
        $preparedNfts = $this->lazyMint->getPreparedByProduct($product_id);
        foreach ($preparedNfts as $preparedNft) {
            $recipient_address = get_post_meta($order_id, 'recipient_blockchain_address_' . $preparedNft->chain, true);

            if ($recipient_address) {
                $mint_body = array('to' => $recipient_address, 'chain' => $preparedNft->chain, 'url' => "ipfs://$url");
                if ($preparedNft->chain === 'CELO') {
                    $mint_body['feeCurrency'] = 'CELO';
                }
                $response = Connector::mint_nft($mint_body, $api_key);
                if (isset($response['txId'])) {
                    $this->lazyMint->insertLazyNft($preparedNft->id, $order_id, $recipient_address, $preparedNft->chain, $response['txId']);
                } else {
                    $this->resolveNftError($product_id, $order_id, $preparedNft->chain, 'Cannot mint NFT. Check recipient address or contact support.', $recipient_address);
                }
            }
        }
    }

    private function resolveNftError($order_id, $error_message, $chain, $preparedNft, $recipient_address) {
        $this->lazyMint->insertLazyNft($preparedNft->id, $order_id, $recipient_address, $chain, null, $error_message);
    }

    public function updateThankYouPage($thank_you_title, $order) {
        $transactions = '';
        foreach ($order->get_items() as $order_item) {
            $product_id = $order_item->get_product_id();
            if ($product_id) {
                $minted_nfts = $this->lazyMint->getLazyNftByProductAndOrder($product_id, $order->get_id());
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