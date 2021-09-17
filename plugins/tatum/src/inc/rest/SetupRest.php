<?php

namespace Hathoriel\Tatum\rest;

use Hathoriel\Utils\Service;
use Hathoriel\Tatum\base\UtilsProvider;
use WP_REST_Response;
use WP_REST_Request;
use Hathoriel\Tatum\tatum\Setup;
use Hathoriel\Tatum\tatum\Estimate;

// @codeCoverageIgnoreStart
defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request
// @codeCoverageIgnoreEnd

/**
 * Create an example REST Service.
 *
 * @codeCoverageIgnore Example implementations gets deleted the most time after plugin creation!
 */
class SetupRest
{
    use UtilsProvider;

    /**
     * C'tor.
     */
    private function __construct() {
        // Silence is golden.
    }

    /**
     * Register endpoints.
     */
    public function rest_api_init() {
        $this->registerRoute('/setup', 'GET', 'getSetup');
        $this->registerRoute('/api-key', 'POST', 'setApiKey');
        $this->registerRoute('/estimate', 'GET', 'estimate');
        $this->registerRoute('/api-key', 'GET', 'getApiKey');
        $this->registerRoute('/dismiss-tutorial', 'POST', 'dismissTutorial');
    }

    /**
     * See API docs.
     *
     * @api {get} /tatum/v1/setup Return setup
     * @apiHeader {string} X-WP-Nonce
     * @apiName GetSetup
     * @apiGroup Setup
     *
     * @apiSuccessExample {json} Success-Response:
     * {
     *     "isWoocommerceInstalled": true"
     * }
     * @apiVersion 0.1.0
     */
    public function getSetup() {
        return new WP_REST_Response(Setup::getSetup());
    }

    public function setApiKey(WP_REST_Request $request) {
        $data = $request->get_json_params();
        $response = Setup::setApiKey($data['apiKey']);
        return new WP_REST_Response($response);
    }

    public function estimate() {
        return new WP_REST_Response(Estimate::estimateCountOfMintAllSupportedBlockchain());
    }

    public function getApiKey() {
        $api_key = Setup::getApiKey();
        return new WP_REST_Response($api_key);
    }

    public function dismissTutorial() {
        Setup::dismissTutorial();
    }

    /**
     * New instance.
     */
    public static function instance() {
        return new SetupRest();
    }

    private function registerRoute($path, $method, $callback) {
        $namespace = Service::getNamespace($this);
        register_rest_route($namespace, $path, [
            'methods' => $method,
            'callback' => [$this, $callback],
            'permission_callback' => '__return_true'
        ]);
    }
}
