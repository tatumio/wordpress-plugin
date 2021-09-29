<?php

namespace Hathoriel\Tatum\tatum;

class Ipfs
{
    public static function storeProductImageToIpfs($product_id, $api_key) {


        $curl = curl_init();
        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;

        $image_content = self::getProductImageNameAndContent($product_id);
        $post_data = self::buildDataFiles($boundary, $image_content);

        curl_setopt_array($curl, array(
            CURLOPT_URL => Connector::TATUM_URL . '/v3/ipfs',
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
    }

    private static function getProductImageNameAndContent($product_id) {
        $product = wc_get_product($product_id);
        $attachment_url = wp_get_attachment_url($product->get_image_id());
        $uploads = wp_upload_dir();
        $file_path = str_replace($uploads['baseurl'], $uploads['basedir'], $attachment_url);
        return array(basename($attachment_url) => file_get_contents($file_path));
    }


    private static function buildDataFiles($boundary, $files) {
        $data = '';
        $eol = "\r\n";

        $delimiter = '-------------' . $boundary;

        foreach ($files as $name => $content) {
            $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="file"; filename="' . $name . '"' . $eol
                //. 'Content-Type: image/png'.$eol
                . 'Content-Transfer-Encoding: binary' . $eol;

            $data .= $eol;
            $data .= $content . $eol;
        }
        $data .= "--" . $delimiter . "--" . $eol;


        return $data;
    }
}