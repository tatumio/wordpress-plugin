<?php
require_once 'class-tatum-connector.php';
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
class Tatum_Admin
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
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/tatum-admin.css', array(), $this->version, 'all');

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

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/tatum-admin.js', array('jquery'), $this->version, false);

    }

    /**
     * Creates a new custom post type
     *
     * @since 1.0.0
     * @access public
     * @uses register_post_type()
     */
    public function new_cpt_rdm_quote() {
        $cap_type = 'post';
        $plural = 'Api keys';
        $single = 'Random Quote';
        $menu_name = 'Tatum';
        $cpt_name = 'rdm-quote';
        $opts['can_export'] = TRUE;
        $opts['capability_type'] = $cap_type;
        $opts['description'] = '';
        $opts['exclude_from_search'] = FALSE;
        $opts['has_archive'] = FALSE;
        $opts['hierarchical'] = FALSE;
        $opts['map_meta_cap'] = TRUE;
        $opts['menu_icon'] = 'dashicons-businessman';
        $opts['menu_position'] = 25;
        $opts['public'] = FALSE;
        $opts['publicly_querable'] = FALSE;
        $opts['query_var'] = TRUE;
        $opts['register_meta_box_cb'] = array($this, 'meta_box');
        $opts['rewrite'] = FALSE;
        $opts['show_in_admin_bar'] = TRUE;
        $opts['show_ui'] = TRUE;
        $opts['show_in_menu'] = TRUE;
        $opts['show_in_nav_menu'] = TRUE;
        $opts['capabilities'] = ['edit_post', 'read_post', 'delete_post', 'edit_posts', 'edit_others_posts', 'read_private_posts'];

        $opts['labels']['add_new'] = esc_html__("Add New {$single}", 'wisdom');
        $opts['labels']['add_new_item'] = esc_html__("Add New {$single}", 'wisdom');
        $opts['labels']['all_items'] = esc_html__($plural, 'wisdom');
        $opts['labels']['edit_item'] = esc_html__("Edit {$single}", 'wisdom');
        $opts['labels']['menu_name'] = esc_html__($menu_name, 'wisdom');
        $opts['labels']['name'] = esc_html__($plural, 'wisdom');
        $opts['labels']['name_admin_bar'] = esc_html__($single, 'wisdom');
        $opts['labels']['new_item'] = esc_html__("New {$single}", 'wisdom');
        $opts['labels']['not_found'] = esc_html__("No {$plural} Found", 'wisdom');
        $opts['labels']['not_found_in_trash'] = esc_html__("No {$plural} Found in Trash", 'wisdom');
        $opts['labels']['parent_item_colon'] = esc_html__("Parent {$plural} :", 'wisdom');
        $opts['labels']['search_items'] = esc_html__("Search {$plural}", 'wisdom');
        $opts['labels']['singular_name'] = esc_html__($single, 'wisdom');
        $opts['labels']['view_item'] = esc_html__("View {$single}", 'wisdom');
        register_post_type(strtolower($cpt_name), $opts);

        register_post_status('wallet_generated', array(
            'label' => _x('Wallet generated', 'recipes'),
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Wallet generated <span class="count">(%s)</span>', 'Wallet generated <span class="count">(%s)</span>'),
        ));

        register_post_status('contract_transaction_sent', array(
            'label' => _x('Contract transaction sent', 'recipes'),
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Contract transaction sent <span class="count">(%s)</span>', 'Contract transaction sent <span class="count">(%s)</span>'),
        ));

        register_post_status('nft_contract_address_obtained', array(
            'label' => _x('NFT Contract address obtained', 'recipes'),
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('NFT Contract address obtained <span class="count">(%s)</span>', 'NFT Contract address obtained <span class="count">(%s)</span>'),
        ));

        add_action('init', 'custom_post_status');
    }

    public function meta_box($post) {
        if (in_array($post->post_status, ['wallet_generated'])) {
            add_meta_box('tatum_meta', 'Wallet settings', array($this, 'render_meta_box'));
        }

        if (in_array($post->post_status, ['wallet_generated', 'contract_transaction_sent'])) {
            add_meta_box('tatum_nft_meta', 'NFT settings', array($this, 'render_meta_box_nft'));
        }
    }

    public function render_meta_box($post) {
        wp_nonce_field('tatum_nonce', 'tatum_nonce');
        ?>
        <table class="form-table">
            <?php $this->render_meta_input($post, 'mnemonic', 'Mnemonic', true); ?>
            <?php $this->render_meta_input($post, 'xpub', 'Xpub', true); ?>
            <?php $this->render_meta_input($post, 'address', 'Address', true); ?>
            <?php $this->render_meta_input($post, 'private_key', 'Private key', true); ?>
            <?php $this->render_balance($post); ?>

        </table>
        <?php
    }

    public function render_meta_box_nft($post) {
        wp_nonce_field('tatum_nonce', 'tatum_nonce');
        ?>
        <table class="form-table">
            <?php $this->render_meta_input($post, 'nft_contract_name', 'Contract name'); ?>
            <?php $this->render_meta_input($post, 'nft_contract_symbol', 'Contract symbol'); ?>
        </table>
        <?php
    }

    public function render_meta_input($post, $name, $label, $readonly = false) {
        $value = get_post_meta($post->ID, $name, true);
        $this->render_input($name, $value, $label, $readonly);
    }

    public function render_input($name, $value, $label, $readonly = false) {
        ?>
        <tr>
            <th><label for="<?php echo $name; ?>"><?php echo $label; ?></label></th>
            <td>
                <input id="<?php echo $name; ?>"
                       name="<?php echo $name; ?>"
                       type="text"
                       value="<?php echo esc_attr($value); ?>"
                       size="50"
                    <?php echo $readonly ? 'readonly' : ''; ?>
                />
            </td>
        </tr>
        <?php
    }

    public function render_balance($post) {
        $address = get_post_meta($post->ID, 'address', true);
        $balance = Tatum_Connector::get_ethereum_balance($address, $post->post_title);
        $this->render_input('balance', $balance['balance'] === '0' ? '0.0' : $balance['balance'], 'Balance', true);
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since 1.0.0
     */

    public function add_plugin_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=rdm-quote',
            __('Portfolio Projects Options', 'rushhour'),
            __('Portfolio Options', 'rushhour'),
            'manage_options',
            'projects_archive',
            array($this, 'display_plugin_setup_page'));
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since 1.0.0
     */

    public function add_action_links($links) {
        $settings_link = array(
            '<a href="' . admin_url('options-general.php?page=' . $this->plugin_name) . '">' . __('Settings', $this->plugin_name) . '</a>',
        );
        return array_merge($settings_link, $links);
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since 1.0.0
     */

    public function display_plugin_setup_page() {
        include_once('partials/tatum-admin-display.php');
    }

    public function validate($input) {
        // All checkboxes inputs
        $valid = array();

        //Quote title
        $valid['quo-title'] = (isset($input['quo-title']) && !empty($input['quo-title'])) ? 1 : 0;
        //return 1;
        return $valid;
    }

    public function options_update() {
        register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
    }

    public function rushhour_projects_options_display() {
        echo 'settings';
    }

    public function remove_editor_from_post() {
        remove_post_type_support('rdm-quote', 'editor');
    }

    public function change_title($title) {
        $screen = get_current_screen();
        if ($screen->post_type == 'rdm-quote') {
            $title = 'Enter Api key';
        }
        return $title;
    }

    public function save_post($post_id, $post, $update) {
        switch ($post->post_status) {
            case 'publish':
                $this->generate_wallet($post);
                break;
            case 'wallet_generated':
                $this->create_smart_contract($post);
                break;
        }
    }


    public function generate_wallet($post) {
        try {
            $response = Tatum_Connector::get_api_version($post->post_title);
            $this->admin_notice('success', 'Your api key was successfully set. You are using Tatum version ' . $response['version'] . '. Now you can generate your wallet.');
            $response = Tatum_Connector::generate_ethereum_wallet($post->post_title);
            update_post_meta($post->ID, 'mnemonic', $response['mnemonic']);
            update_post_meta($post->ID, 'xpub', $response['xpub']);
            $response = Tatum_Connector::generate_ethereum_account($response['xpub'], 1, $post->post_title);
            update_post_meta($post->ID, 'address', $response['address']);
            $response = Tatum_Connector::generate_ethereum_private_key(['index' => 1, 'mnemonic' => get_post_meta($post->ID, 'mnemonic', true)], $post->post_title);
            update_post_meta($post->ID, 'private_key', $response['key']);
            $post_to_update = array(
                'ID' => $post->ID,
                'post_status' => 'wallet_generated',
            );
            wp_update_post($post_to_update);
            add_filter('redirect_post_location', array($this, 'add_notice_query_var'), 99);
        } catch (Exception $error) {
            // TODO: handle error
            print_r($error);
            exit();
        }
    }

    public function create_smart_contract($post) {
        try {
            $address = get_post_meta($post->ID, 'address', true);
            $private_key = get_post_meta($post->ID, 'private_key', true);
            $nft_contract_name = $_POST['nft_contract_name'];
            $nft_contract_symbol = $_POST['nft_contract_symbol'];
            update_post_meta($post->ID, 'nft_contract_name', $nft_contract_name);
            update_post_meta($post->ID, 'nft_contract_symbol', $nft_contract_symbol);

            $balance = Tatum_Connector::get_ethereum_balance($address, $post->post_title);
            if ($balance['balance'] == 0) {
                echo 'too low balance';
                exit();
            }
            $response = Tatum_Connector::deploy_nft_smart_contract([
                'name' => $nft_contract_name,
                'symbol' => $nft_contract_symbol,
                'fromPrivateKey' => $private_key,
                'chain' => 'ETH'
            ], $post->post_title);
            update_post_meta($post->ID, 'nft_contract_transaction_hash', $response['txId']);
            $post_to_update = array(
                'ID' => $post->ID,
                'post_status' => 'contract_transaction_sent',
            );
            wp_update_post($post_to_update);
//            $this->admin_notic('success', 'You have successfully broadcast your smart contract with transaction id ' . $response['txId'] . '. Please wait until your transaction is confirmed.');
        } catch (Exception $error) {
            // TODO: handle error
            print_r($error);
            exit();
        }
    }

    public function add_notice_query_var($location) {
        remove_filter('redirect_post_location', array($this, 'add_notice_query_var'), 99);
        return add_query_arg(array('YOUR_QUERY_VAR' => 'ID'), $location);
    }

    public function admin_notices() {
        if (!isset($_GET['YOUR_QUERY_VAR'])) {
            return;
        }
        ?>
        <div class="updated">
            <p><?php esc_html_e('YOUR MESSAGE', 'text-domain'); ?></p>
        </div>
        <?php
    }

    public function change_publish_button($translation, $text) {
        if ('rdm-quote' == get_post_type())
            if ($text == 'Publish' || $text == 'Update')
                return 'Save';

        return $translation;

    }

    public function admin_notice($type, $message) { ?>
    <div class="notice notice-<?php echo $type; ?> is-dismissible">
        <p> <?php echo $message ?> </p>
        </div><?php
    }

    public function obtain_contract_address() {
        if (is_admin()) {
            if (!empty($_GET['post'])) {
                // Get the post object
                $post = get_post($_GET['post']);
                if ($post->post_type == 'rdm-quote' && $post->post_status == 'contract_transaction_sent') {
                    $contract_transaction = get_post_meta($post->ID, 'nft_contract_transaction_hash', true);
                    $transaction = Tatum_Connector::get_ethereum_transaction($contract_transaction, $post->post_title);
                    if (isset($transaction['contractAddress'])) {
                        update_post_meta($post->ID, 'nft_contract_address', $transaction['contractAddress']);
                        $post_to_update = array(
                            'ID' => $post->ID,
                            'post_status' => 'nft_contract_address_obtained',
                        );
                        wp_update_post($post_to_update);
                    }
                }
            }
        }
    }
}
