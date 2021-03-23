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
require_once('Tatum_Woocommerce.php');

class Tatum
{

    private Settings_Form $settings_form;

    public function __construct()
    {
        // Hook into the admin menu
        add_action('admin_menu', array($this, 'create_plugin_settings_page'));

        // Add Settings and Fields
        add_action('admin_init', array($this, 'setup_settings_fields'));
        new Tatum_Woocommerce();

    }

    /**
     * Create settings page
     */
    public function create_plugin_settings_page()
    {
        // After smart contract creation fetch smart contract address
        $this->obtain_smart_contract_address();

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
    {
        ?>
        <div class="wrap">
            <h2>Tatum settings page</h2><?php
            if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
                $this->submit_settings_form();
            } ?>
            <form method="POST" action="options.php">
                <?php
                settings_fields('tatum-settings');
                do_settings_sections('tatum-settings');
                $this->get_submit_button();
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
        if (!get_option('tatum_api_key_ok')) {
            try {
                $response = Tatum_Connector::get_api_version();
                update_option('tatum_api_key_ok', true);
                $this->admin_notice_success('Your api key was successfully set. You are using Tatum version ' . $response['version'] . '. Now you can generate your wallet.');
            } catch (Exception $error) {
                update_option('tatum_api_key_ok', false);
                $this->admin_notice_error('Your api key was not successfully set. Please check if your api key is correct.');
            }
        } else if (get_option('tatum_api_key_ok') && !get_option('tatum_wallet_generated')) {
            $response = Tatum_Connector::generate_ethereum_wallet();
            update_option('tatum_mnemonic', $response['mnemonic']);
            update_option('tatum_xpub', $response['xpub']);
            $response = Tatum_Connector::generate_ethereum_account($response['xpub'], 1);
            update_option('tatum_address', $response['address']);
            $response = Tatum_Connector::generate_ethereum_private_key(['index' => 1, 'mnemonic' => get_option('tatum_mnemonic')]);
            update_option('tatum_private_key', $response['key']);
            update_option('tatum_wallet_generated', true);
            $this->settings_form->create_fields();
            $this->admin_notice_success('You have successfully generated your wallet.');
        } else if (get_option('tatum_wallet_generated') && !get_option('tatum_smart_contract_deployed')) {
            $balance = Tatum_Connector::get_ethereum_balance(get_option('tatum_address'));
            if ($balance['balance'] == 0) {
                return $this->admin_notice_error('Your balance is 0 ETH, you must have more the 0 ETH.');
            }
            try {
                $response = Tatum_Connector::deploy_nft_smart_contract([
                    'name' => get_option('tatum_smart_contract_name'),
                    'symbol' => get_option('tatum_smart_contract_symbol'),
                    'fromPrivateKey' => get_option('tatum_private_key'),
                    'chain' => 'ETH'
                ]);
                $this->admin_notice_success('You have successfully broadcast your smart contract with transaction id ' . $response['txId'] . '. Please wait until your transaction is confirmed.');
                update_option('tatum_smart_contract_deployed', true);
                update_option('tatum_smart_contract_transaction_hash', $response['txId']);
                $this->settings_form->create_fields();
            } catch (Exception $error) {
                echo $error;
                $this->admin_notice_error('Your smart contract was not successfully broadcast to the blockchain. Maybe your balance is too low. You have ' . $balance['balance'] . ' ETH.');
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

    public function admin_notice_info($message)
    { ?>
        <div class="notice notice-info">
        <p> <?php echo $message ?> </p>
        </div><?php
    }

    public function get_submit_button()
    {
        if (!get_option('tatum_api_key_ok')) {
            return submit_button('Add Api key');
        } else if (get_option('tatum_api_key_ok') && !get_option('tatum_wallet_generated')) {
            return submit_button('Generate wallet', 'button large');
        } else if (get_option('tatum_wallet_generated') && !get_option('tatum_smart_contract_deployed')) {
            return submit_button('Deploy NFT Smart Contract', 'button large');
        } else if (!get_option('tatum_smart_contract_address')) {
            $this->admin_notice_info('Waiting for obtaining Smart Contract Address. At least 6 confirmations are required to consider Smart Contract deployed. Smart Contract was created with transaction <a href="https://ropsten.etherscan.io/tx/' . get_option('tatum_smart_contract_transaction_hash') . '" target="_blank">' . get_option('tatum_smart_contract_transaction_hash') . '</a>. Refresh this page multiple times, after smart contract transaction deploy will be confirmed you will be able to mint NFT tokens.');
        } else {
            $this->admin_notice_info("<span style='font-size:20px;'>&#127881;</span> Everything is set and you are ready to go! You can start mint NFT tokens with product Tatum data tab.");
        }

    }

    public function obtain_smart_contract_address()
    {
        if (get_option('tatum_smart_contract_deployed') && !get_option('tatum_smart_contract_address')) {
            $transaction = Tatum_Connector::get_ethereum_transaction(get_option('tatum_smart_contract_transaction_hash'));
            if (isset($transaction['contractAddress'])) {
                update_option('tatum_smart_contract_address', $transaction['contractAddress']);
                $this->admin_notice_success('You have successfully obtained smart contract address ' . $transaction['contractAddress'] . '.');
            }
        }
    }
}


new Tatum();