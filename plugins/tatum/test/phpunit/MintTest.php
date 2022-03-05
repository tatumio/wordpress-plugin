<?php

declare(strict_types=1);

namespace Hathoriel\Tatum\Test;

use Hathoriel\Tatum\hooks\PublicHooks;
use Hathoriel\Tatum\tatum\Ipfs;
use Hathoriel\Tatum\tatum\LazyMint;
use Mockery;
use Mockery\MockInterface;
use ReflectionClass;
use WP_Mock;
use WP_Mock\Tools\TestCase;

final class MintTest extends TestCase
{
    private $lazy;

    public function setUp(): void {
        parent::setUp();
        $this->lazy = Mockery::mock(LazyMint::class);
    }

    public function testMintCelo() {
        WP_Mock::userFunction('get_option', [
            'return' => '7dd5bcaf-f22c-4be8-8cf7-43175828c8aa'
        ]);

        $order = new OrderWoocommerce();
        \WP_Mock::userFunction('wc_get_order', array(
            'args' => array(43),
            'return' => $order,
        ));

        $mint = new PublicHooks();
        $this->lazy->shouldReceive('getPreparedByProduct')->times(1)->andReturn(1);
        $this->setProtectedProperty($mint, 'lazyMint', $this->lazy);

        $mock = \Mockery::mock('alias:Ipfs');

        $this->assertEquals(1, $mint->woocommerce_order_set_to_processing(43));
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