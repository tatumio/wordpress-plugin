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
class Tatum_Public {

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
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tatum-public.css', array(), $this->version, 'all' );

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tatum-public.js', array( 'jquery' ), $this->version, false );

	}

	public function woocommerce_add_address_checkout( WC_Checkout $checkout ) {
		$chains = [];
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$product_id = $cart_item['product_id'];
			$api_key_id = get_post_meta( $product_id, 'tatum_api_key', true );
			array_push( $chains, get_post_meta( $api_key_id, 'chain', true ) );
		}
		$chains = array_unique( $chains );

		foreach ( $chains as $chain ) {
			echo '<div id="custom_checkout_field">';

			woocommerce_form_field( 'recipient_blockchain_address_' . $chain, array(
				'type'        => 'text',
				'class'       => array(
					'my-field-class form-row-wide'
				),
				'label'       => __( $chain . ' address' ),
				'placeholder' => __( $chain . ' address' ),
				'required'    => true,
			),
				$checkout->get_value( 'recipient_blockchain_address_' . $chain ) );

			echo '</div>';
		}
	}

	public function woocommerce_validate_address_checkout() {
		$tatum_address_validator = new Tatum_Address_Validator();
		foreach ( [ 'ETH', 'CELO', 'BSC' ] as $chain ) {
			$recipient_address = sanitize_text_field($_POST[ 'recipient_blockchain_address_' . $chain ]);
			if ( isset( $_POST[ 'recipient_blockchain_address_' . $chain ] ) && ! $tatum_address_validator->isETHAddress($recipient_address ) ) {
				wc_add_notice( __( 'Please enter valid format of your ' . $chain . ' address.' ), 'error' );
			}
		}
	}

	public function woocommerce_save_address_checkout( $order_id ) {
		foreach ( [ 'ETH', 'CELO', 'BSC' ] as $chain ) {
			if ( ! empty( $_POST[ 'recipient_blockchain_address_' . $chain ] ) ) {
				update_post_meta( $order_id, 'recipient_blockchain_address_' . $chain, sanitize_text_field( $_POST[ 'recipient_blockchain_address_' . $chain ] ) );
			}
		}

	}

	public function woocommerce_display_address_on_admin_order_page( $order ) {
		foreach ( [ 'ETH', 'CELO', 'BSC' ] as $chain ) {
			if ( get_post_meta( $order->get_id(), 'recipient_blockchain_address_' . $chain, true ) ) {
				echo '<p><strong>' . __( $chain . ' Recipient Address' ) . ':</strong> ' . get_post_meta( $order->get_id(), 'recipient_blockchain_address_' . $chain, true ) . '</p>';
			}
		}
	}

	public function woocommerce_order_set_to_processing( $order_id ) {
		$order = new WC_Order( $order_id );
		foreach ( $order->get_items() as $item_id => $item ) {
			$product_id       = $item->get_product_id();
			$mint_hash        = get_post_meta( $product_id, 'tatum_transaction_hash', true );
			$tatum_api_key_id = get_post_meta( $product_id, 'tatum_api_key', true );
			$api_key          = get_post( $tatum_api_key_id );
			$contract_address = get_post_meta( $tatum_api_key_id, 'nft_contract_address', true );
			$private_key      = get_post_meta( $tatum_api_key_id, 'private_key', true );
			$chain            = get_post_meta( $tatum_api_key_id, 'chain', true );
			if ( $mint_hash && $api_key ) {
				foreach ( [ 'ETH', 'CELO', 'BSC' ] as $chain ) {
					$recipient_address = get_post_meta( $order_id, 'recipient_blockchain_address_' . $chain, true );
					if ( $recipient_address ) {
						$transfer_body = [
							'to'              => $recipient_address,
							'chain'           => $chain,
							'tokenId'         => get_post_meta( $product_id, 'tatum_token_id', true ),
							'contractAddress' => $contract_address,
							'fromPrivateKey'  => $private_key
						];
						if ( $chain === 'CELO' ) {
							$transfer_body['feeCurrency'] = 'CELO';
						}
						$response = Tatum_Connector::transfer_nft_token( $transfer_body, $api_key->post_title );
						update_post_meta( $product_id, 'tatum_transfer_hash', $response['txId'] );
					}
				}
			}
		}
	}

}
