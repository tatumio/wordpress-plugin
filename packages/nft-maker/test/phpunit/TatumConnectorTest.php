<?php

declare(strict_types=1);

namespace Hathoriel\NftMaker\Test;

use Hathoriel\NftMaker\Connectors\TatumConnector;
use WP_Mock;
use WP_Mock\Tools\TestCase;

final class TatumConnectorTest extends TestCase
{

    public function setUp(): void {
        parent::setUp();
    }

    public function testMintCelo() {

        WP_Mock::userFunction('get_option', [
            'args' => array(TATUM_SLUG . '_api_key'),
            'return' => '7dd5bcaf-f22c-4be8-8cf7-43175828c8aa'
        ]);

        WP_Mock::userFunction('get_option', [
            'args' => array(TATUM_SLUG . '_region'),
            'return' => 'eu1'
        ]);

        WP_Mock::userFunction('wp_remote_post', [
            'return' => 'result'
        ]);

        WP_Mock::userFunction('wp_remote_retrieve_body', [
            'return' => '{"txId": "0x828a9fc0985f511c750d29cc81aba8db24dcabdb469a2aba9e79bc94d85f3170"}'
        ]);

        WP_Mock::userFunction('wp_remote_retrieve_response_code', [
            'return' => 200
        ]);

        $tatumConnector = new TatumConnector();

        $result = $tatumConnector->mintNft([
            'to' => '0x51abC4c9e7BFfaA99bBE4dDC33d75067EBD0384F',
            'chain' => 'CELO',
            'url' => "ipfs://bafkreihpjc4stdyzkwu52mwsezp644b7yey6464xhevjzrmt2pei6xecaa",
            'feeCurrency' => 'CELO'
        ]);

        $this->assertEquals(['txId' => '0x828a9fc0985f511c750d29cc81aba8db24dcabdb469a2aba9e79bc94d85f3170'], $result);
    }
}
