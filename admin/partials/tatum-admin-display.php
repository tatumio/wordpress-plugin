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

        <form method="post" name="my-rdm-quotes_options" action="options.php">
            <?php
            //Grab all options
            $options = get_option($this->plugin_name);

            // Title quote
            $quo_title = $options['quo-title'];
            ?>
            <?php
            settings_fields($this->plugin_name);
            do_settings_sections($this->plugin_name);
            ?>

            <!-- Optional title for quotes list -->
            <fieldset>
                <legend class="screen-reader-text"><span>Include title in quotes list.</span></legend>
                <label for="<?php echo $this->plugin_name; ?>-quo-title">
                    <input type="checkbox" id="<?php echo $this->plugin_name; ?>-quo-title" name="<?php echo $this->plugin_name; ?>[quo-title]" value="1" <?php checked($quo_title, 1); ?>/>
                    <span><?php esc_attr_e('Include title in quotes list?', $this->plugin_name); ?></span>
                </label>
            </fieldset>

            <?php submit_button('Save all changes', 'primary','submit', TRUE); ?>

        </form>


    </div>

<?php settings_fields($this->plugin_name); ?>