<?php

class Settings_Form
{
    public function __construct()
    {
        add_settings_section('initial_settings', '', array($this, 'section_callback'), 'tatum-settings');
    }

    public function section_callback($arguments)
    {
        switch ($arguments['id']) {
            case 'initial_settings':
                echo '';
                break;
        }
    }

    public function field_callback($arguments)
    {

        $value = get_option($arguments['uid']);

        if (!$value) {
            $value = isset($arguments['value']) ? $arguments['value'] : $arguments['default'] ?? null;
        }

        switch ($arguments['type']) {
            case 'text':
            case 'password':
            case 'number':
                printf('<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" size="50" %5$s %6$s />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value, isset($arguments['readonly']) ? 'readonly' : '', isset($arguments['required']) ? 'required' : '');
                break;
            case 'textarea':
                printf('<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value);
                break;
            case 'select':
            case 'multiselect':
                if (!empty ($arguments['options']) && is_array($arguments['options'])) {
                    $attributes = '';
                    $options_markup = '';
                    foreach ($arguments['options'] as $key => $label) {
                        $options_markup .= sprintf('<option value="%s" %s>%s</option>', $key, selected($value[array_search($key, $value, true)], $key, false), $label);
                    }
                    if ($arguments['type'] === 'multiselect') {
                        $attributes = ' multiple="multiple" ';
                    }
                    printf('<select name="%1$s[]" id="%1$s" %2$s>%3$s</select>', $arguments['uid'], $attributes, $options_markup);
                }
                break;
            case 'radio':
            case 'checkbox':
                if (!empty ($arguments['options']) && is_array($arguments['options'])) {
                    $options_markup = '';
                    $iterator = 0;
                    foreach ($arguments['options'] as $key => $label) {
                        $iterator++;
                        $options_markup .= sprintf('<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments['uid'], $arguments['type'], $key, checked($value[array_search($key, $value, true)], $key, false), $label, $iterator);
                    }
                    printf('<fieldset>%s</fieldset>', $options_markup);
                }
                break;
        }

        if (!empty($arguments['helper'])) {
            printf('<span class="helper"> %s</span>', $arguments['helper']);
        }

        if (!empty($arguments['supplimental'])) {
            printf('<p class="description">%s</p>', $arguments['supplimental']);
        }

    }

    public function create_fields()
    {
        $fields = array_merge(
            $this->get_api_key_fields(),
            get_option('tatum_wallet_generated') ? $this->get_wallet_fields() : [],
            get_option('tatum_wallet_generated') ? $this->deploy_smart_contract() : [],
            get_option('tatum_smart_contract_address') ? $this->smart_contract_address() : []
        );

        foreach ($fields as $field) {
            add_settings_field($field['uid'], $field['label'], array($this, 'field_callback'), 'tatum-settings', $field['section'], $field);
            register_setting('tatum-settings', $field['uid']);
        }

    }

    private function get_api_key_fields(): array
    {
        return array(
            array(
                'uid' => 'tatum_api_key',
                'label' => 'Api Key',
                'type' => 'text',
                'placeholder' => 'Your Api Key',
                'section' => 'initial_settings',
                'supplimental' => 'You can obtain your api key at Tatum dashboard https://dashboard.tatum.io/login.',
                'required' => 'true'
            ),
        );
    }

    private function get_wallet_fields(): array
    {
        $balance = Tatum_Connector::get_ethereum_balance(get_option('tatum_address'));
        update_option('tatum_account_balance', $balance['balance'] === '0' ? '0.0' :  $balance['balance']);
        return array(
            array(
                'uid' => 'tatum_mnemonic',
                'label' => 'Mnemonic',
                'type' => 'text',
                'placeholder' => 'Mnemonic',
                'section' => 'initial_settings',
                'supplimental' => 'Generated mnemonic for wallet.',
                'readonly' => true
            ),
            array(
                'uid' => 'tatum_xpub',
                'label' => 'Xpub',
                'type' => 'text',
                'placeholder' => 'Xpub',
                'section' => 'initial_settings',
                'supplimental' => 'Generated Extended public key for wallet with derivation path according to BIP44. This key can be used to generate addresses.',
                'readonly' => true
            ),
            array(
                'uid' => 'tatum_address',
                'label' => 'Address',
                'type' => 'text',
                'placeholder' => 'Address',
                'section' => 'initial_settings',
                'supplimental' => 'Generated address.',
                'readonly' => true
            ),
            array(
                'uid' => 'tatum_private_key',
                'label' => 'Private key',
                'type' => 'text',
                'placeholder' => 'Private key',
                'section' => 'initial_settings',
                'supplimental' => 'Generated private key.',
                'readonly' => true
            ),
            array(
                'uid' => 'tatum_account_balance',
                'label' => 'Account balance',
                'type' => 'text',
                'placeholder' => 'Account balance',
                'section' => 'initial_settings',
                'supplimental' => 'Account balance.',
                'readonly' => true,
            ),
        );
    }

    private function deploy_smart_contract()
    {
        return  [
            array_merge(
                ['uid' => 'tatum_smart_contract_name',
                    'label' => 'Smart Contract Name',
                    'type' => 'text',
                    'placeholder' => 'Smart Contract Name',
                    'section' => 'initial_settings',
                    'supplimental' => 'Name of the ERC721 token.',
                    'required' => 'true'],
                get_option('tatum_smart_contract_deployed') ? ['readonly' => true] : []
            ),
            array_merge(
                ['uid' => 'tatum_smart_contract_symbol',
                    'label' => 'Smart Contract Symbol',
                    'type' => 'text',
                    'placeholder' => 'Smart Contract Name',
                    'section' => 'initial_settings',
                    'supplimental' => 'Symbol of the ERC721 token.',
                    'required' => 'true'],
                get_option('tatum_smart_contract_deployed') ? ['readonly' => true] : []
            ),
        ];
    }

    private function smart_contract_address()
    {
        return [
            array_merge([
                'uid' => 'tatum_smart_contract_address',
                'label' => 'Smart Contract Address',
                'type' => 'text',
                'placeholder' => 'Smart Contract Address',
                'section' => 'initial_settings',
                'readonly' => true
            ])
        ];
    }
}