<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://tatum.io/
 * @since      1.0.0
 *
 * @package    Tatum
 * @subpackage Tatum/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Tatum
 * @subpackage Tatum/includes
 * @author     Lukas Kotol <lukas.kotol@tatum.io>
 */
class Tatum
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Tatum_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('TATUM_VERSION')) {
            $this->version = TATUM_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'tatum';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Tatum_Loader. Orchestrates the hooks of the plugin.
     * - Tatum_i18n. Defines internationalization functionality.
     * - Tatum_Admin. Defines all hooks for the admin area.
     * - Tatum_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-tatum-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-tatum-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-tatum-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-tatum-public.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-tatum-connector.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-tatum-address-validator.php';

        $this->loader = new Tatum_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Tatum_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Tatum_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    public function define_admin_hooks() {

        $plugin_admin = new Tatum_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        // Add menu item
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');

        // Add Settings link to the plugin
        $plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_name . '.php');

        // Add custom post type
        $this->loader->add_action('init', $plugin_admin, 'new_api_key_post_type');

        $this->loader->add_filter('plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links');

        $this->loader->add_action('admin_init', $plugin_admin, 'options_update');

        // Remove editor from post
        $this->loader->add_action('init', $plugin_admin, 'remove_editor_from_post');

        // Change custom post title
        $this->loader->add_filter('enter_title_here', $plugin_admin, 'change_title');

        // Hide post status
        $this->loader->add_filter('gettext', $plugin_admin, 'change_publish_button', 10, 2);

        // Save post
        $this->loader->add_action('admin_notices', $plugin_admin, 'admin_notices');

        // obtain contract address
        $this->loader->add_action('load-post.php', $plugin_admin, 'obtain_contract_address');

        $this->loader->add_action('save_post_api_key', $plugin_admin, 'save_post', 10, 3);

        // Change post columns
        $this->loader->add_filter('manage_api_key_posts_columns', $plugin_admin, 'change_post_columns', 10, 1);

        // Fill post columns
        $this->loader->add_action('manage_api_key_posts_custom_column', $plugin_admin, 'fill_custom_columns', 10, 2);

        // Woocommerce hooks and filters
        // Add product data tabs
        $this->loader->add_filter('woocommerce_product_data_tabs', $plugin_admin, 'add_product_data_tab');

        // Add product data fields
        $this->loader->add_action('woocommerce_product_data_panels', $plugin_admin, 'add_product_data_fields');

        // On publish hook
        $this->loader->add_action('transition_post_status', $plugin_admin, 'on_product_publish', 10, 3);

        // Add column header with transaction hash to order items table
        $this->loader->add_action('woocommerce_admin_order_item_headers', $plugin_admin, 'woocommerce_add_transaction_header_column', 10, 1);

        // Add column data with transaction hash to order items table
        $this->loader->add_action('woocommerce_admin_order_item_values', $plugin_admin, 'woocommerce_add_transaction_data_column', 10, 3);

	    // We add our display_flash_notices function to the admin_notices
        $this->loader->add_action('admin_notices', $plugin_admin, 'display_flash_notices');

        // Remove post published message
	    $this->loader->add_filter('post_updated_messages', $plugin_admin, 'post_published');

        // Remove api key delete option
	    $this->loader->add_filter('post_row_actions', $plugin_admin, 'remove_row_actions_post', 10, 2);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Tatum_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Add address checkout field
        $this->loader->add_action('woocommerce_before_checkout_billing_form', $plugin_public, 'woocommerce_add_address_checkout');

        // Validate address in checkout
        $this->loader->add_action('woocommerce_checkout_process', $plugin_public, 'woocommerce_validate_address_checkout');

        // Save address in checkout
        $this->loader->add_action('woocommerce_checkout_update_order_meta', $plugin_public, 'woocommerce_save_address_checkout');

        // Process processing status
        $this->loader->add_action('woocommerce_order_status_processing', $plugin_public, 'woocommerce_order_set_to_processing');

        // Display the address field value on the admin order edition page
        $this->loader->add_action('woocommerce_admin_order_data_after_billing_address', $plugin_public, 'woocommerce_display_address_on_admin_order_page');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Tatum_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version() {
        return $this->version;
    }

}
