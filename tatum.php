<?php

/**
 * Plugin Name:       Tatum
 * Plugin URI:        https://tatum.io/
 * Description:       Tatum is a free easiest and fastest plugin to work with NFTs without any expertise in blockchain field.
 * Version:           1.0.4
 * Author:            Lukas Kotol
 * Author URI:        https://github.com/Hathoriel
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       tatum
 * Domain Path:       /languages
 *
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
require plugin_dir_path(__FILE__) . 'vendor/autoload.php';
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('TATUM_VERSION', '1.0.5');
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tatum-activator.php
 */
function activate_tatum()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-tatum-activator.php';
    Tatum_Activator::activate();
}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tatum-deactivator.php
 */
function deactivate_tatum()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-tatum-deactivator.php';
    Tatum_Deactivator::deactivate();
}
register_activation_hook(__FILE__, 'activate_tatum');
register_deactivation_hook(__FILE__, 'deactivate_tatum');
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-tatum.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tatum()
{
    $plugin = new Tatum();
    $plugin->run();
}
run_tatum();