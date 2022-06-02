<?php

namespace Hathoriel\NftMaker\Connectors;

class IpfsConnector
{
    private $apiKey;

    public function __construct() {
        $this->apiKey = get_option(TATUM_SLUG . '_api_key');
    }

    public function storeProductImageToIpfs($product_id) {
        $product = wc_get_product($product_id);
        if(!$product) {
            throw new \Exception("IPFS: Product $product_id was not found.");
        }
        $image = self::getProductImageNameAndContent($product);
        if ($image != false && $image['name'] != '' && $image['content'] != false) {
            $responseImage = $this->storeIpfsFile($image);
            $json = self::createMetadataJson($product, rawurldecode($responseImage['ipfsHash']));
            $responseMetadata = $this->storeIpfsFile(array('name' => 'metadata.json', 'content' => $json));
            return rawurldecode($responseMetadata['ipfsHash']);
        }
        throw new \Exception('IPFS: Cannot upload image.');
    }

    private function storeIpfsFile($data_files) {
        $curl = curl_init();
        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;

        $post_data = self::buildDataFiles($boundary, $data_files);

        curl_setopt_array($curl, array(
            CURLOPT_URL => TatumConnector::getBaseUrl(). '/v3/ipfs',
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: multipart/form-data; boundary=" . $delimiter,
                "Content-Length: " . strlen($post_data),
                "x-api-key: $this->apiKey"
            ),
        ));

        $response = curl_exec($curl);
        return json_decode($response, true);
    }

    private static function getProductImageNameAndContent($product) {
        $attachment_url = wp_get_attachment_url($product->get_image_id());
        $uploads = wp_upload_dir();
        $file_path = str_replace($uploads['baseurl'], $uploads['basedir'], $attachment_url);
        if (substr($file_path, 0, 4) === "http") {
            if (self::urlFileExists($file_path)) {
                if (self::urlFileSize($file_path) <= 52428800) {
                    return array('name' => basename($attachment_url), 'content' => file_get_contents($file_path));
                }
                throw new \Exception('IPFS: Image is too big.');
            }
        } else {
            if (file_exists($file_path)) {
                if (filesize($file_path) <= 52428800) {
                    return array('name' => basename($attachment_url), 'content' => file_get_contents($file_path));
                }
                throw new \Exception('IPFS: Image is too big.');
            }
        }
        throw new \Exception('IPFS: Cannot find image.');
    }

    private static function urlFileExists($url) {
        $options['http'] = array(
            'method' => "HEAD",
            'ignore_errors' => 1,
            'max_redirects' => 0
        );
        $body = file_get_contents($url, NULL, stream_context_create($options));
        sscanf($http_response_header[0], 'HTTP/%*d.%*d %d', $code);
        return $code === 200;
    }

    private static function urlFileSize($url) {
        $headers = get_headers($url, true);
        return $headers['Content-Length'];
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

    private static function createMetadataJson($product, $hash) {
        return json_encode(array(
            'name' => strip_tags(stripslashes($product->get_title())),
            'description' => strip_tags(stripslashes($product->get_description())),
            'image' => "ipfs://$hash"
        ), JSON_UNESCAPED_SLASHES);
    }
}