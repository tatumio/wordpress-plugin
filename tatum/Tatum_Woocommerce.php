<?php

require_once('Tatum_Connector.php');

class Tatum_Woocommerce
{
    public function __construct()
    {
        // Add product data tabs
        add_filter('woocommerce_product_data_tabs', array($this, 'add_product_data_tab'));

        // Add product data fields
        add_action('woocommerce_product_data_panels', array($this, 'add_product_data_fields'));

        // Save product data fields on product update
        add_action('woocommerce_process_product_meta_simple', array($this, 'save_tatum_option_fields'));
        add_action('woocommerce_process_product_meta_variable', array($this, 'save_tatum_option_fields'));

        // On publish hook
        add_action('transition_post_status', array($this, 'on_product_publish'), 10, 3);

    }

    public function add_product_data_tab($tabs)
    {
        $tabs['tatum'] = array(
            'label' => 'Tatum',
            'target' => 'tatum_product_data',
            'priority' => 21,
        );
        return $tabs;
    }

    public function add_product_data_fields()
    {
        $is_minted = get_post_meta(get_the_ID(), 'tatum_transaction_hash', true);
        echo '<div id="tatum_product_data" class="panel woocommerce_options_panel hidden">';

        woocommerce_wp_text_input(array_merge(array(
            'id' => 'tatum_token_id',
            'value' => get_post_meta(get_the_ID(), 'tatum_token_id', true),
            'label' => 'ID of token to be created.',
            'description' => 'ID of token to be created.',
        ), $is_minted ? ['custom_attributes' => array('readonly' => 'readonly')] : []));

        woocommerce_wp_text_input(array_merge(
                array(
                    'id' => 'tatum_url',
                    'value' => get_post_meta(get_the_ID(), 'tatum_url', true),
                    'label' => 'Token Url',
                    'description' => 'Metadata of the token.'
                ),
                $is_minted ? ['custom_attributes' => array('readonly' => 'readonly')] : []
            )
        );

        if ($is_minted) {
            woocommerce_wp_text_input(array(
                'id' => 'tatum_transaction_hash',
                'value' => get_post_meta(get_the_ID(), 'tatum_transaction_hash', true),
                'label' => 'Transaction hash',
                'description' => 'Transaction hash',
                'custom_attributes' => array('readonly' => 'readonly')
            ));
        }
        echo '</div>';
    }

    public function save_tatum_option_fields($post_id)
    {
        $this->save_tatum_option_field($post_id, 'tatum_token_id');
        $this->save_tatum_option_field($post_id, 'tatum_url');
    }

    private function save_tatum_option_field($post_id, $field)
    {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, $_POST[$field]);
        }
    }

    public function on_product_publish($new_status, $old_status, $post)
    {
        if ($old_status != 'publish' &&
            $new_status == 'publish' &&
            !empty($post->ID) &&
            n_array($post->post_type, array('product')) &&
            !get_post_meta(get_the_ID(), 'tatum_transaction_hash', true)
        ) {
            $minted = Tatum_Connector::mint_nft([
                'chain' => 'ETH',
                'tokenId' => get_post_meta($post->ID, 'tatum_token_id', true),
                'to' => get_option('tatum_address'),
                'contractAddress' => get_option('tatum_smart_contract_address'),
                'url' => get_post_meta($post->ID, 'tatum_url', true),
                'fromPrivateKey' => get_option('tatum_private_key')
            ]);
            update_post_meta($post->ID, 'tatum_transaction_hash', $minted['txId']);
        }
    }

    public function add_checkout_field($checkout) {

    }
}



/**

 * Add custom field to the checkout page

 */

add_action('woocommerce_after_order_notes', 'custom_checkout_field');

function custom_checkout_field($checkout)

{

    echo '<div id="custom_checkout_field"><h2>' . __('New Heading') . '</h2>';

    woocommerce_form_field('custom_field_name', array(

        'type' => 'text',

        'class' => array(

            'my-field-class form-row-wide'

        ) ,

        'label' => __('Custom Additional Field') ,

        'placeholder' => __('New Custom Field') ,

    ) ,

        $checkout->get_value('custom_field_name'));

    echo '</div>';

}
