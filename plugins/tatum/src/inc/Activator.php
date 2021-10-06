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
        // TODO: remove tatum options from DB
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

        $table_name = $this->getTableName("lazy_nft");
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint NOT NULL AUTO_INCREMENT,
            product_id bigint NOT NULL,
            order_id bigint,
            chain ENUM('CELO', 'ETH', 'BSC', 'ONE', 'MATIC') NOT NULL,
            transaction_id varchar(256),
            recipient_address varchar(256),
            error_cause varchar(256),   
            UNIQUE KEY id (id),
            INDEX(product_id),
            CONSTRAINT UniqChainProductId UNIQUE (product_id, chain)
        ) $charset_collate;";
        dbDelta($sql);
    }
}
