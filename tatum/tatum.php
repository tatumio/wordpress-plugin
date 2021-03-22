<?php
/*
 * Plugin Name: Tatum plugin
 * Plugin URI: https://tatum.io
 * Description: Tatum plugin enables you to integrate to your pages with Tatum platform
 * Author: Lukas Kotol/Tatum
 * Author URI: https://tatum.io
 * Developer: Lukas Kotol/Tatum
 * Version: 1.0.0
 *
*/

// Check if Woocommerce is activated
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    return;
}

require_once('Settings_Form.php');
require_once('Tatum_Connector.php');

class Tatum
{

    private $settings_form;

    public function __construct()
    {
        // Hook into the admin menu
        add_action('admin_menu', array($this, 'create_plugin_settings_page'));

        // Add Settings and Fields
        add_action('admin_init', array($this, 'setup_settings_fields'));

    }

    /**
     * Create settings page
     */
    public function create_plugin_settings_page()
    {
        // Add the menu item and page
        $page_title = 'Tatum settings';
        $menu_title = 'Tatum settings';
        $capability = 'manage_options';
        $slug = 'tatum-settings';
        $callback = array($this, 'plugin_settings_page_content');
        $icon = 'dashicons-admin-plugins';
        $position = 100;

        add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon, $position);
    }

    /**
     * Create settings form
     */
    public function plugin_settings_page_content()
    { ?>
        <div class="wrap">
            <h2>Tatum settings page</h2><?php
            if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
                $this->submit_settings_form();
            } ?>
            <form method="POST" action="options.php">
                <?php
                settings_fields('tatum-settings');
                do_settings_sections('tatum-settings');
                submit_button();
                ?>
            </form>
        </div> <?php
    }

    public function setup_settings_fields()
    {
        $this->settings_form = new Settings_Form();
        $this->settings_form->create_fields();
    }

    public function submit_settings_form()
    {
        if (get_option('api_key')) {
            try {
                $response = Tatum_Connector::get_api_version();
                add_option('api_key_registered', true);
                $this->admin_notice_success('Your api key was successfully set. You are using version ' . $response['version'] . '.');
            } catch (Exception $error) {
                $this->admin_notice_error('Your api key was not successfully set. Please check if your api key is correct.');
            }
        }
    }

    public function admin_notice_success($message)
    { ?>
        <div class="notice notice-success is-dismissible">
        <p> <?php echo $message ?> </p>
        </div><?php
    }

    public function admin_notice_error($message)
    { ?>
        <div class="notice notice-error is-dismissible">
        <p> <?php echo $message ?> </p>
        </div><?php
    }

}

new Tatum();