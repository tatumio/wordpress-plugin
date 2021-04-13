<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://tatum.io/
 * @since      1.0.0
 *
 * @package    Tatum
 * @subpackage Tatum/admin/partials
 */
?>

    <!-- This file should primarily consist of HTML with a little bit of PHP. -->
    <div class="wrap">

        <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

        <form method="post" name="tatum_options" action="options.php">
            <?php
            //Grab all options
            $options = get_option($this->plugin_name);
            $active_api_key = $this->get_active_api_key();
            ?>
            <?php
            settings_fields($this->plugin_name);
            do_settings_sections($this->plugin_name);
            ?>

            <table class="form-table">
                <tbody>
                <tr>
                    <th><label for="api_key">Select api key</label></th>
                    <td>
                        <select name="<?= $this->plugin_name ?>[api_key]" id="<?= $this->plugin_name ?>_api_key">
                            <?php foreach ($this->get_contract_address_obtained_api_keys() as $key): ?>
                                <option value="<?= $key->post_title ?>"
                                    <?= $key->ID == $active_api_key['api_key']->ID ? 'selected' : '' ?>
                                >
                                    <?= $key->post_title ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tbody>
                <tr>
                    <th><label for="automatic_minting">Automatic product minting</label></th>
                    <td>
                        <input type="checkbox" name="<?= $this->plugin_name ?>[automatic_minting]"
                               id="<?= $this->plugin_name ?>_automatic_minting"
                            <?= isset($options['automatic_minting']) ? 'checked' : '' ?>/>
                    </td>
                </tr>
                </tbody>
            </table>
            <!-- Optional title for quotes list -->

            <p class="submit">
                <?php submit_button('Save all changes', 'primary', 'submit', TRUE); ?>
            </p>
        </form>


    </div>

<?php settings_fields($this->plugin_name); ?>