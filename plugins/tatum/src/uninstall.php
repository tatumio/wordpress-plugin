<?php
/**
 * This file is automatically requested when the uninstalls the plugin.
 *
 * @see https://developer.wordpress.org/plugins/the-basics/uninstall-methods/
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

global $wpdb;
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'tatum\_%';");
$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "tatum_lazy_nft;");
$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "tatum_prepared_nft;");
wp_cache_flush();