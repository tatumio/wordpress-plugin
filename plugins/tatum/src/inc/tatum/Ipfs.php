<?php

namespace Hathoriel\Tatum\tatum;

class Ipfs
{
    public static function storeProductImageToIpfs($product_id, $api_key) {
        $image = self::getProductImageNameAndContent($product_id);
        if ($image['name'] != '' && $image['content'] != false) {
            $responseImage = self::storeIpfsFile($image, $api_key);
            $json = self::createMetadataJson($image, rawurldecode($responseImage['ipfsHash']));
            $responseMetadata = self::storeIpfsFile(array('name' => 'metadata.json', 'content' => $json), $api_key);
            return rawurldecode($responseMetadata['ipfsHash']);
        }
        return false;
    }

    private static function storeIpfsFile($data_files, $api_key) {
        $curl = curl_init();
        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;

        $post_data = self::buildDataFiles($boundary, $data_files);

        curl_setopt_array($curl, array(
            CURLOPT_URL => Connector::TATUM_URL . '/v3/ipfs',
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: multipart/form-data; boundary=" . $delimiter,
                "Content-Length: " . strlen($post_data),
                "x-api-key: $api_key"
            ),
        ));

        $response = curl_exec($curl);
        return json_decode($response, true);
    }

    private static function getProductImageNameAndContent($product_id) {
        $product = wc_get_product($product_id);
        $attachment_url = wp_get_attachment_url($product->get_image_id());
        var_dump($attachment_url);
        $uploads = wp_upload_dir();
        var_dump($uploads);
        $file_path = str_replace($uploads['baseurl'], $uploads['basedir'], $attachment_url);
        if(file_exists($file_path)) {
            var_dump(filesize($file_path));
        } else {
            echo 'dont exists';
        }
        exit();
        return array('name' => basename($attachment_url), 'content' => file_get_contents($file_path));
    }


    private static function buildDataFiles($boundary, $file) {
        $data = '';
        $eol = "\r\n";

        $delimiter = '-------------' . $boundary;

        // files start
        $data .= "--" . $delimiter . $eol
            . 'Content-Disposition: form-data; name="file"; filename="' . $file['name'] . '"' . $eol
            //. 'Content-Type: image/png'.$eol
            . 'Content-Transfer-Encoding: binary' . $eol;

        $data .= $eol;
        $data .= $file['content'] . $eol;
        // files end

        // end delimiter
        $data .= "--" . $delimiter . "--" . $eol;


        return $data;
    }

    private static function createMetadataJson($image_content, $hash) {
        $name = $image_content['name'];
        return json_encode(array(
            'name' => $name,
            'image' => "ipfs://$hash"
        ), JSON_UNESCAPED_SLASHES);
    }
}