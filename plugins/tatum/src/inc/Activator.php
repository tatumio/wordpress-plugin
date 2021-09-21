<?php
namespace Hathoriel\Tatum;

use Hathoriel\Tatum\base\UtilsProvider;
use Hathoriel\Utils\Activator as UtilsActivator;

// @codeCoverageIgnoreStart
defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request
// @codeCoverageIgnoreEnd

/**
 * The activator class handles the plugin relevant activation hooks: Uninstall, activation,
 * deactivation and installation. The "installation" means installing needed database tables.
 */
class Activator {
    use UtilsProvider;
    use UtilsActivator;

    /**
     * Method gets fired when the user activates the plugin.
     */
    public function activate() {
        $this->initDatabase();
    }

    /**
     * Method gets fired when the user deactivates the plugin.
     */
    public function deactivate() {
        // Your implementation...
    }

    /**
     * Install tables, stored procedures or whatever in the database.
     * This method is always called when the version bumps up or for
     * the first initial activation.
     *
     * @param boolean $errorlevel If true throw errors
     */
    public function dbDelta($errorlevel) {
        // Your table installation here...
        /*$table_name = $this->getTableName();
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            UNIQUE KEY id (id)
        ) $charset_collate;";
        dbDelta( $sql );

        if ($errorlevel) {
            $wpdb->print_error();
        }*/
    }

    private function initDatabase() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_name = $this->getTableName() . "_lazy_nft";

        $sql = "CREATE TABLE $table_name (
            id bigint NOT NULL AUTO_INCREMENT,
            UNIQUE KEY id (id)
        ) $charset_collate;";
        dbDelta($sql);
    }
}
