<?php
/**
 * Jobber REST API Base
 *
 * @package Jobber
 */

namespace Jobber\REST;

use TenupFramework\Module;

/**
 * Jobber API Base
 */
class API {

	use Module;

	/**
	 * API Namespace
	 *
	 * @var string
	 */
	public static $namespace = 'jobber/v1';

	/**
	 * Can we register this module?
	 *
	 * @return boolean
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Hook the module into WP.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register the REST API routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		// Register the routes here.

		// Get clients
		register_rest_route(
			self::$namespace,
			'/clients',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_clients' ],
				'permission_callback' => [ $this, 'validate_token' ],
				'args'                => [
					'param1'  => [
						'type'     => 'string',
						'required' => true,
					],
				],
			]
		);
	}

	/**
	 * Validate the Jobber Token.
	 *
	 * @param WP_REST_Request $request The REST Request.
	 * @return boolean
	 */
	public function validate_token( \WP_REST_Request $request ) {
		$token = new Token();
		return $token->validate_token( $request, true );
	}

	/**
	 * Get the clients from Jobber.
	 *
	 * @param WP_REST_Request $request The REST Request.
	 * @return WP_REST_Response
	 */
	public function get_clients( $request ) {
		$jobber = new \Jobber\Jobber();
		$clients = $jobber->get_clients();

		return rest_ensure_response( $clients );
	}

}
