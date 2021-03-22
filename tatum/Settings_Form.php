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
            $value = $arguments['default'] ?? null;
        }

        switch ($arguments['type']) {
            case 'text':
            case 'password':
            case 'number':
                printf('<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" size="50" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value);
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
        $fields = array(
            array(
                'uid' => 'api_key',
                'label' => 'Api Key',
                'type' => 'text',
                'placeholder' => 'Your Api Key',
                'section' => 'initial_settings',
                'supplimental' => 'You can obtain your api key at Tatum dashboard https://dashboard.tatum.io/login.'
            ),
        );

        foreach ($fields as $field) {
            add_settings_field($field['uid'], $field['label'], array($this, 'field_callback'), 'tatum-settings', $field['section'], $field);
            register_setting('tatum-settings', $field['uid']);
        }

    }
}