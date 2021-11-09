<?php
namespace Hathoriel\Tatum\base;

use Hathoriel\Utils\Core as UtilsCore;

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

/**
 * Base class for the applications Core class.
 */
abstract class Core {
    use UtilsProvider;
    use UtilsCore;

    /**
     * The constructor handles the core startup mechanism.
     *
     * The constructor is protected because a factory method should only create
     * a Core object.
     *
     * @codeCoverageIgnore
     */
    protected function __construct() {
        // Define lazy constants
        define('TATUM_TD', $this->getPluginData('TextDomain'));
        define('TATUM_VERSION', $this->getPluginData('Version'));
        $this->construct();
    }
}
