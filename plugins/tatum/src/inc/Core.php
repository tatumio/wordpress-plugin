<?php
namespace Hathoriel\Tatum;
use Hathoriel\Tatum\base\Core as BaseCore;
use Hathoriel\Tatum\hooks\Admin;
use Hathoriel\Tatum\hooks\PublicHooks;
use Hathoriel\Tatum\rest\SetupRest;
use Hathoriel\Tatum\view\menu\Page;
use Hathoriel\Tatum\view\widget\Widget;

// @codeCoverageIgnoreStart
defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request
// @codeCoverageIgnoreEnd

/**
 * Singleton core class which handles the main system for plugin. It includes
 * registering of the autoload, all hooks (actions & filters) (see BaseCore class).
 */
class Core extends BaseCore {
    /**
     * Singleton instance.
     */
    private static $me;

    /**
     * Application core constructor.
     */
    protected function __construct() {
        parent::__construct();

        // Register all your before init hooks here
        add_action('widgets_init', [$this, 'widgets_init']);
    }

    /**
     * The init function is fired even the init hook of WordPress. If possible
     * it should register all hooks to have them in one place.
     */
    public function init() {
        // Register all your hooks here
        add_action('rest_api_init', [SetupRest::instance(), 'rest_api_init']);
        add_action('admin_enqueue_scripts', [$this->getAssets(), 'admin_enqueue_scripts']);
        add_action('wp_enqueue_scripts', [$this->getAssets(), 'wp_enqueue_scripts']);
        add_action('admin_menu', [Page::instance(), 'admin_menu']);

        // Register woocommerce hooks
        add_action('admin_head', [Admin::instance(), 'add_product_data_icon']);
        add_action('woocommerce_product_data_tabs', [Admin::instance(), 'add_product_data_tab']);
        add_action('woocommerce_product_data_panels', [Admin::instance(), 'add_product_data_fields']);
        add_action('woocommerce_update_product', [Admin::instance(), 'productSave']);
        add_action('woocommerce_order_status_processing', [PublicHooks::instance(), 'woocommerce_order_set_to_processing']);
        add_action('woocommerce_checkout_update_order_meta', [PublicHooks::instance(), 'woocommerce_save_address_checkout']);
        add_action('woocommerce_checkout_process', [PublicHooks::instance(), 'woocommerce_validate_address_checkout']);
        add_action('woocommerce_before_checkout_billing_form', [PublicHooks::instance(), 'woocommerce_add_address_checkout']);
        add_action('woocommerce_thankyou_order_received_text', [PublicHooks::instance(), 'updateThankYouPage'], 10, 2);
    }

    /**
     * Register widgets.
     *
     * @codeCoverageIgnore Example implementations gets deleted the most time after plugin creation!
     */
    public function widgets_init() {
        register_widget(Widget::class);
    }

    /**
     * Get singleton core class.
     *
     * @return Core
     */
    public static function getInstance() {
        return !isset(self::$me) ? (self::$me = new Core()) : self::$me;
    }
}

// Inherited from packages/utils/src/Service
/**
 * See API docs.
 *
 * @api {get} /tatum/v1/plugin Get plugin information
 * @apiHeader {string} X-WP-Nonce
 * @apiName GetPlugin
 * @apiGroup Plugin
 *
 * @apiSuccessExample {json} Success-Response:
 * {
 *     Name: "My plugin",
 *     PluginURI: "https://example.com/my-plugin",
 *     Version: "0.1.0",
 *     Description: "This plugin is doing something.",
 *     Author: "<a href="https://example.com">John Smith</a>",
 *     AuthorURI: "https://example.com",
 *     TextDomain: "my-plugin",
 *     DomainPath: "/languages",
 *     Network: false,
 *     Title: "<a href="https://example.com">My plugin</a>",
 *     AuthorName: "John Smith"
 * }
 * @apiVersion 0.1.0
 */
