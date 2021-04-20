<?php
// TODO: disable deleting api keys
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://tatum.io/
 * @since      1.0.0
 *
 * @package    Tatum
 * @subpackage Tatum/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tatum
 * @subpackage Tatum/admin
 * @author     Lukas Kotol <lukas.kotol@tatum.io>
 */
class Tatum_Admin {

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
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tatum-admin.css', array(), time(), 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tatum-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Creates a new api key post type
	 *
	 * @since 1.0.0
	 * @access public
	 * @uses register_post_type()
	 */
	public function new_api_key_post_type() {
		$cap_type                     = 'post';
		$plural                       = 'Api keys';
		$single                       = 'Api key';
		$menu_name                    = 'Tatum';
		$cpt_name                     = 'api_key';
		$opts['can_export']           = true;
		$opts['capability_type']      = $cap_type;
		$opts['description']          = '';
		$opts['exclude_from_search']  = false;
		$opts['has_archive']          = false;
		$opts['hierarchical']         = false;
		$opts['map_meta_cap']         = true;
		$opts['menu_icon']            = 'data:image/svg+xml;base64,' . base64_encode( '<svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128"><defs><style>.cls-1{fill:#9da1a7;}</style></defs><title>icon</title><path class="cls-1" d="M84.89,60.42H57.22V111.2H84.89Z"/><path class="cls-1" d="M120.44,22.61H69.62V48h50.82Z"/><path class="cls-1" d="M57.22,35H6.44V60.42H57.22Z"/></svg>' );
		$opts['menu_position']        = 25;
		$opts['public']               = false;
		$opts['publicly_querable']    = false;
		$opts['query_var']            = true;
		$opts['register_meta_box_cb'] = array( $this, 'meta_box' );
		$opts['rewrite']              = false;
		$opts['show_in_admin_bar']    = true;
		$opts['show_ui']              = true;
		$opts['show_in_menu']         = true;
		$opts['show_in_nav_menu']     = true;
		$opts['capabilities']         = [
			'edit_post',
			'read_post',
			'delete_post',
			'edit_posts',
			'edit_others_posts',
			'read_private_posts'
		];

		$opts['labels']['add_new']            = esc_html__( "Add New {$single}", 'wisdom' );
		$opts['labels']['add_new_item']       = esc_html__( "Add New {$single}", 'wisdom' );
		$opts['labels']['all_items']          = esc_html__( $plural, 'wisdom' );
		$opts['labels']['edit_item']          = esc_html__( "Edit {$single}", 'wisdom' );
		$opts['labels']['menu_name']          = esc_html__( $menu_name, 'wisdom' );
		$opts['labels']['name']               = esc_html__( $plural, 'wisdom' );
		$opts['labels']['name_admin_bar']     = esc_html__( $single, 'wisdom' );
		$opts['labels']['new_item']           = esc_html__( "New {$single}", 'wisdom' );
		$opts['labels']['not_found']          = esc_html__( "No {$plural} Found", 'wisdom' );
		$opts['labels']['not_found_in_trash'] = esc_html__( "No {$plural} Found in Trash", 'wisdom' );
		$opts['labels']['parent_item_colon']  = esc_html__( "Parent {$plural} :", 'wisdom' );
		$opts['labels']['search_items']       = esc_html__( "Search {$plural}", 'wisdom' );
		$opts['labels']['singular_name']      = esc_html__( $single, 'wisdom' );
		$opts['labels']['view_item']          = esc_html__( "View {$single}", 'wisdom' );
		register_post_type( strtolower( $cpt_name ), $opts );
	}

	public function meta_box( $post ) {
		if ( in_array( get_post_meta( $post->ID, 'status', true ), [
			'wallet_generated',
			'contract_transaction_sent',
			'contract_address_obtained'
		] ) ) {
			add_meta_box( 'tatum_meta', 'Wallet settings', array( $this, 'render_meta_box' ) );
		}

		if ( in_array( get_post_meta( $post->ID, 'status', true ), [
			'wallet_generated',
			'contract_transaction_sent',
			'contract_address_obtained'
		] ) ) {
			add_meta_box( 'tatum_nft_meta', 'NFT settings', array( $this, 'render_meta_box_nft' ) );
		}
	}

	public function render_meta_box( WP_Post $post ) {
		wp_nonce_field( 'tatum_nonce', 'tatum_nonce' );

		?>
        <table class="form-table">
			<?php $this->render_status( $post ); ?>
			<?php $this->render_meta_input( $post, 'mnemonic', 'Mnemonic', true ); ?>
			<?php $this->render_meta_input( $post, 'xpub', 'Xpub', true ); ?>
			<?php $this->render_meta_input( $post, 'address', 'Address', true ); ?>
			<?php $this->render_meta_input( $post, 'private_key', 'Private key', true ); ?>
			<?php $this->render_balance( $post ); ?>
        </table>
		<?php
	}

	public function render_meta_box_nft( $post ) {
		wp_nonce_field( 'tatum_nonce', 'tatum_nonce' );
		$status                    = get_post_meta( $post->ID, 'status', true );
		$readonly                  = in_array( $status, [ 'contract_transaction_sent', 'contract_address_obtained' ] );
		$contract_address_obtained = in_array( $status, [ 'contract_address_obtained' ] );
		?>
        <table class="form-table">
			<?php $this->render_meta_input( $post, 'nft_contract_name', 'Contract name', $readonly ); ?>
			<?php $this->render_meta_input( $post, 'nft_contract_symbol', 'Contract symbol', $readonly ); ?>
			<?php
			if ( $contract_address_obtained ) {
				$this->render_meta_input( $post, 'nft_contract_address', 'Contract address', $readonly );
			} ?>
        </table>
		<?php
	}

	public function render_meta_input( $post, $name, $label, $readonly = false, $default = null ) {
		$value = get_post_meta( $post->ID, $name, true );
		if ( ! $value ) {
			$value = $default;
		}
		$this->render_input( $name, $value, $label, $readonly );
	}

	public function render_input( $name, $value, $label, $readonly = false ) {
		?>
        <tr>
            <th><label for="<?php echo $name; ?>"><?php echo $label; ?></label></th>
            <td>
                <input id="<?php echo $name; ?>"
                       name="<?php echo $name; ?>"
                       type="text"
                       value="<?php echo esc_attr( $value ); ?>"
                       size="50"
					<?php echo $readonly ? 'readonly' : ''; ?>
                />
            </td>
        </tr>
		<?php
	}

	public function render_status( WP_Post $post ) {
		$status = $this->format_api_key_status( get_post_meta( $post->ID, 'status', true ) );
		?>
        <tr>
            <th><label>Status</label></th>
            <td>
				<?= $status ?>
            </td>
        </tr>
		<?php
	}

	public function render_balance( $post ) {
		$address = get_post_meta( $post->ID, 'address', true );
		$balance = Tatum_Connector::get_ethereum_balance( $address, $post->post_title );
		$this->render_input( 'balance', $balance['balance'] === '0' ? '0.0' : $balance['balance'], 'Balance', true );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since 1.0.0
	 */

	public function add_plugin_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=api_key',
			__( 'General settings', 'tatum' ),
			__( 'General settings', 'tatum' ),
			'manage_options',
			'projects_archive',
			array( $this, 'display_plugin_setup_page' ) );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since 1.0.0
	 */

	public function add_action_links( $links ) {
		$settings_link = array(
			'<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __( 'Settings', $this->plugin_name ) . '</a>',
		);

		return array_merge( $settings_link, $links );
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since 1.0.0
	 */

	public function display_plugin_setup_page() {
		include_once( 'partials/tatum-admin-display.php' );
	}

	public function options_update() {
		register_setting( $this->plugin_name, $this->plugin_name );
	}

	public function remove_editor_from_post() {
		remove_post_type_support( 'api_key', 'editor' );
	}

	public function post_published( $messages ) {
		$messages['api_key'] = array(
			0  => null,
			1  => null,
			2  => null,
			3  => null,
			4  => null,
			5  => null,
			6  => null,
			7  => null,
			8  => null,
			9  => null,
			10 => null,
			11 => null,
		);

		return $messages;
	}

	public function change_title( $title ): string {
		$screen = get_current_screen();
		if ( $screen->post_type == 'api_key' ) {
			$title = 'Enter Api key';
		}

		return $title;
	}

	public function generate_wallet( $post ) {
		try {
			$response = Tatum_Connector::get_api_version( $post->post_title );
			$this->add_flash_notice( 'Your API key was added and your wallet was generated. Now you should send funds to the address and deploy NFT contract.', "success" );
			$response = Tatum_Connector::generate_ethereum_wallet( $post->post_title );
			update_post_meta( $post->ID, 'mnemonic', $response['mnemonic'] );
			update_post_meta( $post->ID, 'xpub', $response['xpub'] );
			$response = Tatum_Connector::generate_ethereum_account( $response['xpub'], 1, $post->post_title );
			update_post_meta( $post->ID, 'address', $response['address'] );
			$response = Tatum_Connector::generate_ethereum_private_key( [
				'index'    => 1,
				'mnemonic' => get_post_meta( $post->ID, 'mnemonic', true )
			], $post->post_title );
			update_post_meta( $post->ID, 'private_key', $response['key'] );
			update_post_meta( $post->ID, 'status', 'wallet_generated' );

		} catch ( Exception $error ) {
			$this->add_flash_notice( 'Cannot add the entered API key, please check that you are submitted a valid API key.', "error" );
		}
	}

	public function create_smart_contract( $post ) {
		try {
			if ( ! isset( $_POST['nft_contract_name'] ) || ! isset( $_POST['nft_contract_symbol'] ) ) {
				$this->add_flash_notice( 'Contract name and symbol must be specified.', "error" );

				return;
			}

			$nft_contract_name   = $_POST['nft_contract_name'];
			$nft_contract_symbol = $_POST['nft_contract_symbol'];
			$address             = get_post_meta( $post->ID, 'address', true );
			$private_key         = get_post_meta( $post->ID, 'private_key', true );
			update_post_meta( $post->ID, 'nft_contract_name', $nft_contract_name );
			update_post_meta( $post->ID, 'nft_contract_symbol', $nft_contract_symbol );
			$balance = Tatum_Connector::get_ethereum_balance( $address, $post->post_title );
			if ( $balance['balance'] == 0 ) {
				$this->add_flash_notice( 'Your balance on address should not be zero.', "error" );
				return;
			}

			$response = Tatum_Connector::deploy_nft_smart_contract( [
				'name'           => $nft_contract_name,
				'symbol'         => $nft_contract_symbol,
				'fromPrivateKey' => $private_key,
				'chain'          => 'ETH'
			], $post->post_title );
			update_post_meta( $post->ID, 'nft_contract_transaction_hash', $response['txId'] );
			update_post_meta( $post->ID, 'status', 'contract_transaction_sent' );
		} catch ( Exception $error ) {
			$this->add_flash_notice( 'There was a problem with deploying your smart contract, please check if you have enough balance.', "error" );
		}
	}

	public function change_publish_button( $translation, $text ) {
		if ( 'api_key' == get_post_type() ) {
			if ( $text == 'Publish' || $text == 'Update' ) {
				return 'Save';
			}
		}

		return $translation;

	}

	// Just keep this function to run
	// $this->loader->add_action('admin_notices', $plugin_admin, 'display_flash_notices'); in class-tatum.php
	public function admin_notices() {
	}

	public function obtain_contract_address() {
		if ( is_admin() ) {
			if ( ! empty( $_GET['post'] ) ) {
				// Get the post object
				$post = get_post( $_GET['post'] );
				if ( $post->post_type == 'api_key' ) {
					$status = get_post_meta( $post->ID, 'status', true );
					if ( $status == 'contract_transaction_sent' ) {
						$contract_transaction = get_post_meta( $post->ID, 'nft_contract_transaction_hash', true );
						$transaction          = Tatum_Connector::get_ethereum_transaction( $contract_transaction, $post->post_title );
						if ( isset( $transaction['contractAddress'] ) ) {
							update_post_meta( $post->ID, 'nft_contract_address', $transaction['contractAddress'] );
							update_post_meta( $post->ID, 'status', 'contract_address_obtained' );
							update_post_meta( $post->ID, 'automatic_minting_index', 0 );
						} else {
							$txId = get_post_meta( $post->ID, 'nft_contract_transaction_hash', true );
							$this->add_flash_notice( 'Your contract was deployed with transaction ' . $txId . '. Wait please till transaction will be added to the block to obtain the contract address. To see if the contract address is obtained refresh page multiple times.', "success" );
						}
					}
					if ( $status == 'contract_address_obtained' ) {
						$this->add_flash_notice( '🎉 Your contract address is obtained and you are ready to mint your tokens!', "success" );
					}
				}
			}
		}
	}

	public function change_post_columns( $defaults ) {
		$defaults['status'] = 'Status';
		unset( $defaults['date'] );

		return $defaults;
	}

	public function fill_custom_columns( $column_name, $post_id ) {
		if ( $column_name == 'status' ) {
			echo $this->format_api_key_status( get_post_meta( $post_id, 'status', true ) );
		}
	}

	public function format_api_key_status( $status ) {
		if ( ! $status ) {
			return 'Api key not submitted';
		}
		$formatted_status = [
			'contract_address_obtained' => 'NFT Contract address set up! You are ready to mint tokens!',
			'contract_transaction_sent' => 'NFT Contract sent, waiting for obtaining contract address.',
			'wallet_generated'          => 'Wallet generated'
		];

		return $formatted_status[ $status ];
	}

	public function save_post( $post_ID, $post, $update ) {
		if ( ! $update ) {
			return;
		}
		if ( ! get_post_meta( $post->ID, 'status', true ) ) {
			$this->generate_wallet( $post );
		} else if ( get_post_meta( $post->ID, 'status', true ) == 'wallet_generated' ) {
			$this->create_smart_contract( $post );
		}
	}

	public function get_contract_address_obtained_api_keys() {
		return get_posts( [
			'post_type'     => 'api_key',
			'meta_key'      => 'status',
			'meta_value'    => 'contract_address_obtained',
			'post_per_page' => 100, /* add a reasonable max # rows */
			'no_found_rows' => true, /* don't generate a count as part of query, unless you need it. */
		] );
	}

	// Woocommerce methods
	public function add_product_data_tab( $tabs ) {
		$tabs['tatum'] = array(
			'label'    => 'Tatum',
			'target'   => 'tatum_product_data',
			'priority' => 21,
		);

		return $tabs;
	}

	public function get_active_api_key() {
		$options = get_option( $this->plugin_name );
		$api_key = $this->get_api_key_by_title( $options['api_key'] );
		if ( $api_key ) {
			return [ 'api_key' => $api_key, 'meta' => get_post_meta( $api_key->ID ) ];
		}

		return null;
	}

	public function get_api_key_by_title( $title ) {
		$args     = array( 'post_type' => 'api_key', 'title' => $title );
		$api_keys = get_posts( $args );
		if ( empty( $api_keys ) ) {
			return null;
		}

		return $api_keys[0];
	}

	public function add_product_data_fields() {
		$options       = get_option( $this->plugin_name );
		$is_minted     = get_post_meta( get_the_ID(), 'tatum_transaction_hash', true );
		$transfer_hash = get_post_meta( get_the_ID(), 'tatum_transfer_hash', true );
		echo '<div id="tatum_product_data" class="panel woocommerce_options_panel hidden">';

		woocommerce_wp_text_input( array_merge( array(
			'id'          => 'tatum_token_id',
			'value'       => get_post_meta( get_the_ID(), 'tatum_token_id', true ),
			'label'       => 'ID of token to be created.',
			'description' => 'ID of token to be created.',
		), $is_minted || $options['automatic_minting'] ? [ 'custom_attributes' => array( 'readonly' => 'readonly' ) ] : [] ) );

		woocommerce_wp_text_input( array_merge(
				array(
					'id'          => 'tatum_url',
					'value'       => get_post_meta( get_the_ID(), 'tatum_url', true ),
					'label'       => 'URL Metadata of the token',
					'description' => 'URL Metadata of the token.'
				),
				$is_minted || $options['automatic_minting'] ? [ 'custom_attributes' => array( 'readonly' => 'readonly' ) ] : []
			)
		);

		if ( $is_minted ) {
			woocommerce_wp_text_input( array(
				'id'                => 'tatum_transaction_hash',
				'value'             => get_post_meta( get_the_ID(), 'tatum_transaction_hash', true ),
				'label'             => 'Transaction hash',
				'description'       => 'Transaction hash',
				'custom_attributes' => array( 'readonly' => 'readonly' )
			) );

			if ( $transfer_hash ) {
				woocommerce_wp_text_input( array(
					'id'                => 'tatum_transfer_hash',
					'value'             => $transfer_hash,
					'label'             => 'Transfer hash',
					'description'       => 'Transfer hash',
					'custom_attributes' => array( 'readonly' => 'readonly' )
				) );
			}
		}


		echo '</div>';
	}

	/**
	 * Add a flash notice to {prefix}options table until a full page refresh is done
	 *
	 * @param string $notice our notice message
	 * @param string $type This can be "info", "warning", "error" or "success", "warning" as default
	 * @param boolean $dismissible set this to TRUE to add is-dismissible functionality to your notice
	 *
	 * @return void
	 */

	public function add_flash_notice( $notice = "", $type = "warning", $dismissible = true ) {
		// Here we return the notices saved on our option, if there are not notices, then an empty array is returned
		$notices = get_option( "my_flash_notices", array() );

		$dismissible_text = ( $dismissible ) ? "is-dismissible" : "";

		// We add our new notice.
		array_push( $notices, array(
			"notice"      => $notice,
			"type"        => $type,
			"dismissible" => $dismissible_text
		) );

		// Then we update the option with our notices array
		update_option( "my_flash_notices", $notices );
	}

	/**
	 * Function executed when the 'admin_notices' action is called, here we check if there are notices on
	 * our database and display them, after that, we remove the option to prevent notices being displayed forever.
	 * @return void
	 */

	public function display_flash_notices() {
		$notices = get_option( "my_flash_notices", array() );

		// Iterate through our notices to be displayed and print them.
		foreach ( $notices as $notice ) {
			printf( '<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>',
				$notice['type'],
				$notice['dismissible'],
				$notice['notice']
			);
		}

		// Now we reset our options to prevent notices being displayed forever.
		if ( ! empty( $notices ) ) {
			delete_option( "my_flash_notices", array() );
		}
	}

	private function save_tatum_option_field( $post_id, $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $field, $_POST[ $field ] );
		}
	}

	public function on_product_publish( $new_status, $old_status, $post ) {
		$active_key = $this->get_active_api_key();
		if ( $new_status == 'publish' && ! empty( $post->ID ) &&
		     in_array( $post->post_type, array( 'product' ) ) &&
		     ! get_post_meta( get_the_ID(), 'tatum_transaction_hash', true ) &&
		     $active_key ) {
			$options = get_option( $this->plugin_name );
			if ( $options['automatic_minting'] ) {
				$options = get_option( $this->plugin_name );
				$this->mint_token( $post, $new_status, $active_key['meta']['automatic_minting_index'][0], $options['metadata_url'] );

				// increment minting index
				update_post_meta( $post->ID, 'tatum_token_id', $active_key['meta']['automatic_minting_index'][0] );
				update_post_meta( $post->ID, 'tatum_url', $options['metadata_url'] );
				update_post_meta( $active_key['api_key']->ID, 'automatic_minting_index', $active_key['meta']['automatic_minting_index'][0] + 1 );

			} else {
				$this->save_tatum_option_field( $post->ID, 'tatum_token_id' );
				$this->save_tatum_option_field( $post->ID, 'tatum_url' );
				$tatum_token_id = get_post_meta( $post->ID, 'tatum_token_id', true );
				$tatum_url      = get_post_meta( $post->ID, 'tatum_url', true );
				$this->mint_token( $post, $new_status, $tatum_token_id, $tatum_url );
			}
		}
	}

	public function mint_token( $post, $new_status, $tatum_token_id, $tatum_url ) {

		if ( ! empty( $tatum_token_id ) && ! empty( $tatum_url ) ) {
			$active_key = $this->get_active_api_key();
			$minted     = Tatum_Connector::mint_nft( [
				'chain'           => 'ETH',
				'tokenId'         => $tatum_token_id,
				'to'              => $active_key['meta']['address'][0],
				'contractAddress' => $active_key['meta']['nft_contract_address'][0],
				'url'             => $tatum_url,
				'fromPrivateKey'  => $active_key['meta']['private_key'][0]
			], $active_key['api_key']->post_title );

			update_post_meta( $post->ID, 'tatum_transaction_hash', $minted['txId'] );
			update_post_meta( $post->ID, 'tatum_api_key', $active_key['api_key']->ID );
		}
	}

	public function woocommerce_add_transaction_header_column( $order ) {
		?>
        <th class="line_transfer_hash sortable" data-sort="your-sort-option">
            Transaction hash
        </th>
		<?php
	}

	public function woocommerce_add_transaction_data_column( $product, $item, $item_id ) {
		?>
        <td class="line_transfer_hash">
			<?= get_post_meta( $product->get_id(), 'tatum_transaction_hash', true ); ?>
        </td>
		<?php
	}
}
