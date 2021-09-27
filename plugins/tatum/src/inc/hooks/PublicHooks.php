<?php

namespace Hathoriel\Tatum\hooks;

use Hathoriel\Tatum\tatum\Chains;
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
            if (!is_admin()) {
                $api_key = get_option(TATUM_SLUG . '_api_key');
                if ($api_key) {
                    $order = wc_get_order($order_id);
                    foreach ($order->get_items() as $item_id => $item) {
                        $product_id = $item->get_product_id();
                        $product = wc_get_product($product_id);
                        $url = wp_get_attachment_url($product->get_image_id());
                        $uploads = wp_upload_dir();
                        $file_path = str_replace($uploads['baseurl'], $uploads['basedir'], $url);
                        echo $api_key;
                        echo "<br/>";
                        echo "<br/>";
                        $url = "https://api-eu1.tatum.io/v3/ipfs";
// data fields for POST request
                        $fields = array("f1"=>"value1", "another_field2"=>"anothervalue");

// files to upload
                        $filenames = array($file_path);

                        $files = array();
                        foreach ($filenames as $f){
                            $files["file"] = file_get_contents($f);
                        }


                        $curl = curl_init();


                        $boundary = uniqid();
                        $delimiter = '-------------' . $boundary;

                        $post_data = $this->build_data_files($boundary, $fields, $files);


                        curl_setopt_array($curl, array(
                            CURLOPT_URL => $url,
                            CURLOPT_RETURNTRANSFER => 1,
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POST => 1,
                            CURLOPT_POSTFIELDS => $post_data,
                            CURLOPT_HTTPHEADER => array(
                                //"Authorization: Bearer $TOKEN",
                                "Content-Type: multipart/form-data; boundary=" . $delimiter,
                                "Content-Length: " . strlen($post_data),
                                "x-api-key: $api_key"

                            ),


                        ));


                        $response = curl_exec($curl);

                        $info = curl_getinfo($curl);

                        var_dump($info);
                        var_dump($response);
                        $err = curl_error($curl);
                        var_dump($err);
                        curl_close($curl);

                        exit();

                        $lazyMints = $this->lazyMint->getByProduct($product_id);
                        foreach ($lazyMints as $lazyMint) {

                            $recipient_address = get_post_meta($order_id, 'recipient_blockchain_address_' . $lazyMint->chain, true);

                            if ($recipient_address) {
                                $transfer_body = array('to' => $recipient_address, 'chain' => $lazyMint->chain, 'url' => 'https://test.com');
                                if ($lazyMint->chain === 'CELO') {
                                    $transfer_body['feeCurrency'] = 'CELO';
                                }
                                $response = Connector::mint_nft($transfer_body, $api_key);
                                if (isset($response['txId'])) {
                                    $this->lazyMint->updateByProductAndChain($product_id, $lazyMint->chain, array('transaction_id' => $response['txId'], 'order_id' => $order_id));
                                } else {
                                    wc_add_notice(__('NFT minting error occurred. Please try again or contact administrator.'), 'error');
                                    exit();
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            echo '{"result":"failure","messages":"<ul class=\"woocommerce-error\" role=\"alert\">\n\t\t\t<li>\n\t\t\tNFT minting error occurred. Please try again or contact administrator.\t\t<\/li>\n\t<\/ul>\n","refresh":false,"reload":false}';
            exit();
        }
    }

    public function build_data_files($boundary, $fields, $files) {
        $data = '';
        $eol = "\r\n";

        $delimiter = '-------------' . $boundary;

        foreach ($fields as $name => $content) {
            $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="' . $name . "\"" . $eol . $eol
                . $content . $eol;
        }


        foreach ($files as $name => $content) {
            $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="' . $name . '"; filename="filename.jpg"' . $eol
                //. 'Content-Type: image/png'.$eol
                . 'Content-Transfer-Encoding: binary' . $eol;

            $data .= $eol;
            $data .= $content . $eol;
        }
        $data .= "--" . $delimiter . "--" . $eol;


        return $data;
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
}