<?php
/**
 * This file is automatically requested when the uninstalls the plugin.
 *
 * @see https://developer.wordpress.org/plugins/the-basics/uninstall-methods/
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

delete_option('tatum_api_key');
delete_option('tatum_is_tutorial_dismissed');

global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS wp_tatum_lazy_nft");
wp_cache_flush();