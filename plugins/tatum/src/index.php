<?php
/**
 * Main file for WordPress.
 *
 * @wordpress-plugin
 * Plugin Name: 	NFT Maker
 * Plugin URI:		https://wordpress.org/plugins/tatum
 * Description: 	NFT Maker is the easiest and fastest free plugin to work with NFTs without any blockchain development experience.
 * Author:          Lukas Kotol
 * Author URI:		https://t.me/LukasKotol
 * Version: 		2.0.38
 * Text Domain:		tatum
 * Domain Path:		/languages
 * License:         MIT
 * License URI:     https://opensource.org/licenses/MIT
 *
 */

defined('ABSPATH') or die( 'No script kiddies please!' ); // Avoid direct file request

/**
 * Plugin constants. This file is procedural coding style for initialization of
 * the plugin core and definition of plugin configuration.
 */
if (defined('TATUM_PATH')) {
    return;
}
define('TATUM_FILE', __FILE__);
define('TATUM_PATH', dirname(TATUM_FILE));
define('TATUM_ROOT_SLUG', 'tatum');
define('TATUM_SLUG', basename(TATUM_PATH));
define('TATUM_INC', trailingslashit(path_join(TATUM_PATH, 'inc')));
define('TATUM_MIN_PHP', '7.0.0'); // Minimum of PHP 5.3 required for autoloading and namespacing
define('TATUM_MIN_WP', '5.5.0'); // Minimum of WordPress 5.0 required
define('TATUM_NS', 'Hathoriel\\Tatum');
define('TATUM_DB_PREFIX', 'tatum'); // The table name prefix wp_{prefix}
define('TATUM_OPT_PREFIX', 'tatum'); // The option name prefix in wp_options
define('TATUM_SLUG_CAMELCASE', lcfirst(str_replace('-', '', ucwords(TATUM_SLUG, '-'))));
//define('TATUM_TD', ''); This constant is defined in the core class. Use this constant in all your __() methods
//define('TATUM_VERSION', ''); This constant is defined in the core class
define('TATUM_DEBUG', true); // This constant should be defined in wp-config.php to enable the Base#debug() method


// Check PHP Version and print notice if minimum not reached, otherwise start the plugin core
require_once TATUM_INC .
    'base/others/' .
    (version_compare(phpversion(), TATUM_MIN_PHP, '>=') ? 'start.php' : 'fallback-php-version.php');
