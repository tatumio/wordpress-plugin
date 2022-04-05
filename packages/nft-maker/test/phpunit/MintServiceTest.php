<?php

declare(strict_types=1);

namespace Hathoriel\NftMaker\Test;

use Hathoriel\NftMaker\Connectors\IpfsConnector;
use Hathoriel\NftMaker\Connectors\TatumConnector;
use Hathoriel\NftMaker\Connectors\DbConnector;
use Hathoriel\NftMaker\Services\MintService;
use Hathoriel\NftMaker\Services\SetupService;
use Mockery;
use ReflectionClass;
use WP_Mock;
use WP_Mock\Tools\TestCase;

final class MintTest extends TestCase
{
    private $dbConnector;
    private $tatumConnector;
    private $ipfsConnector;
    private $setupService;

    public function setUp(): void {
        define("TATUM_SLUG", "TATUM");
        parent::setUp();
        $this->dbConnector = Mockery::mock(DbConnector::class);
        $this->tatumConnector = Mockery::mock(TatumConnector::class);
        $this->ipfsConnector = Mockery::mock(IpfsConnector::class);
        $this->setupService = Mockery::mock(SetupService::class);
    }

    public function testMintCelo() {
        $this->tatumConnector->shouldReceive('hasValidApiKey')->andReturn(true);
        $this->setupService->shouldReceive('isTestnet')->andReturn(true);
        WP_Mock::userFunction('get_option', [
            'return' => '7dd5bcaf-f22c-4be8-8cf7-43175828c8aa'
        ]);

        $order = new OrderWoocommerce();
        \WP_Mock::userFunction('wc_get_order', array(
            'args' => array(43),
            'return' => $order,
        ));

        $preparedNft = new \stdClass;
        $preparedNft->chain = 'CELO';
        $preparedNft->id = 10;

        $mintService = new MintService();
        $this->dbConnector->shouldReceive('getPreparedByProduct')->times(2)->andReturn([ $preparedNft ]);


        $this->ipfsConnector->shouldReceive('storeProductImageToIpfs')->andReturn('bafkreihpjc4stdyzkwu52mwsezp644b7yey6464xhevjzrmt2pei6xecaa');

        \WP_Mock::userFunction('get_post_meta', array(
            'return' => '0x51abC4c9e7BFfaA99bBE4dDC33d75067EBD0384F',
        ));

        $transactionResult = ['txId' => '0x828a9fc0985f511c750d29cc81aba8db24dcabdb469a2aba9e79bc94d85f3170'];

        $this->tatumConnector->shouldReceive('mintNft')->andReturn($transactionResult);

        $this->dbConnector->shouldReceive('insertLazyNft')->times(1)->andReturnNull();

        $this->setProtectedProperty($mintService, 'dbConnector', $this->dbConnector);
        $this->setProtectedProperty($mintService, 'tatumConnector', $this->tatumConnector);
        $this->setProtectedProperty($mintService, 'ipfsConnector', $this->ipfsConnector);
        $this->setProtectedProperty($mintService, 'setupService', $this->setupService);

        $this->assertEquals($transactionResult, $mintService->mintOrder(43));
    }

    public function setProtectedProperty($object, $property, $value) {
        $reflection = new ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($object, $value);
    }
}

class OrderWoocommerce
{
    public function get_items() {
        return [new OrderItemWooCommerce()];
    }
}

class OrderItemWooCommerce {
    public function get_product_id() {
        return 3;
    }

    public function get_quantity() {
        return 1;
    }
}
