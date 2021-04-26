<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://tatum.io/
 * @since      1.0.0
 *
 * @package    Tatum
 * @subpackage Tatum/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Tatum
 * @subpackage Tatum/includes
 * @author     Lukas Kotol <lukas.kotol@tatum.io>
 */
class Tatum_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$api_keys = get_posts( array( 'post_type' => 'tatum_api_key' ) );
		foreach ( $api_keys as $api_key ) {
			wp_delete_post( $api_key->ID, true );
		}
		delete_option('tatum');
	}

}
