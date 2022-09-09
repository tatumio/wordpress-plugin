<?php

namespace Hathoriel\NftMaker\Hooks;

use Hathoriel\NftMaker\Connectors\DbConnector;
use Hathoriel\NftMaker\Services\MintService;
use Hathoriel\NftMaker\Services\NftService;
use Hathoriel\NftMaker\Utils\AddressValidator;
use Hathoriel\NftMaker\Utils\BlockchainLink;
use Hathoriel\NftMaker\Utils\Chains;
use Hathoriel\NftMaker\Utils\Constants;

class PublicHooks
{
    private $dbConnector;
    private $mintService;
    private $nftService;

    public function __construct() {
        $this->dbConnector = new DbConnector();
        $this->mintService = new MintService();
        $this->nftService = new NftService();
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
        foreach (Constants::CHAIN_CODES as $chain) {
            if (!empty($_POST['recipient_blockchain_address_' . $chain])) {
                update_post_meta($order_id, 'recipient_blockchain_address_' . $chain, sanitize_text_field($_POST['recipient_blockchain_address_' . $chain]));
            }
        }
    }

    public function validateAddressCheckout() {
        foreach (Constants::CHAIN_CODES as $chain) {
            $recipient_address = sanitize_text_field($_POST['recipient_blockchain_address_' . $chain]);
            if (isset($_POST['recipient_blockchain_address_' . $chain]) && !AddressValidator::isETHAddress($recipient_address)) {
                wc_add_notice(__('Please enter valid format of your ' . $chain . ' address.'), 'error');
            }
        }
    }

    public function thankYouPageAfterCheckout($thank_you_title, $order) {
        $nftsDetails = '';
        foreach ($order->get_items() as $order_item) {
            $product_id = $order_item->get_product_id();
            if ($product_id) {
                $minted_nfts = $this->dbConnector->getLazyNftByProductAndOrder($product_id, $order->get_id());
                foreach ($minted_nfts as $minted_nft) {
                    if ($minted_nft->transaction_id != "") {
                        $nftsDetails .= $this->getNftDetail($minted_nft->chain, $minted_nft->transaction_id, $minted_nft->testnet);
                    }
                }
            }
        }
        if ($nftsDetails !== '') {
            return "$thank_you_title <br><br> Following NFTs were minted: <br><br> $nftsDetails";
        }
        return $thank_you_title;
    }

    private function getNftDetail($chain, $txId, $testnet) {
        $nftDetail = $this->nftService->getNftDetail($chain, $txId, $testnet);
        $txLink = BlockchainLink::txLink($txId, $chain, $testnet);
        $html = "Transction Hash: $txLink<br>";

        if (array_key_exists("tokenId", $nftDetail)) {
            $html .= "Token Id: " . $nftDetail['tokenId'] . "<br>";
        }

        $html .= "Contract Address: " . $nftDetail['contractAddress'] . "<br><br>";
        return $html;
    }

    public static function instance() {
        return new PublicHooks();
    }
}