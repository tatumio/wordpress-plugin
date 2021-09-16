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
        $namespace = Service::getNamespace($this);
        register_rest_route($namespace, '/setup', [
            'methods' => 'GET',
            'callback' => [$this, 'getSetup'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route($namespace, '/api-key', [
            'methods' => 'POST',
            'callback' => [$this, 'setApiKey'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route($namespace, '/estimate', [
            'methods' => 'GET',
            'callback' => [$this, 'estimate'],
            'permission_callback' => '__return_true'
        ]);
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

    /**
     * New instance.
     */
    public static function instance() {
        return new SetupRest();
    }
}
