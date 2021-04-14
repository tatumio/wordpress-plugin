<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://tatum.io/
 * @since      1.0.0
 *
 * @package    Tatum
 * @subpackage Tatum/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tatum
 * @subpackage Tatum/public
 * @author     Lukas Kotol <lukas.kotol@tatum.io>
 */
class Tatum_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Tatum_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Tatum_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/tatum-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Tatum_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Tatum_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/tatum-public.js', array('jquery'), $this->version, false);

    }

    public function woocommerce_add_custom_address_checkout_field($checkout) {
        echo '<div id="custom_checkout_field">';

        woocommerce_form_field('recipient_blockchain_address', array(
            'type' => 'text',
            'class' => array(
                'my-field-class form-row-wide'
            ),
            'label' => __('ETH address'),
            'placeholder' => __('ETH address'),
            'required' => true,
        ),
            $checkout->get_value('recipient_blockchain_address'));

        echo '</div>';
    }

    public function woocommerce_order_set_to_processing($order_id) {
        $order = new WC_Order( $order_id );
        print_r($order);
        exit();
    }

}
