<?php
/**
 * Jobber REST API for Token Management.
 *
 * @package Jobber
 */

namespace Jobber\REST;

use WP_Error;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Jobber API Token Management
 */
class Tokens extends API {

	/**
	 * API Route
	 *
	 * @var string
	 */
	protected static $route = '/token';

	/**
	 * Hook module into WP.
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
		register_rest_route(
			self::$namespace,
			self::$route,
			[
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'save_tokens' ],
				'args'     => [
					'access_token'  => [
						'type'     => 'string',
						'required' => true,
					],
					'refresh_token' => [
						'type'     => 'string',
						'required' => true,
					],
				],
			]
		);
	}

	/**
	 * Save the Jobber API Token.
	 * Sends success or error response to middleware.
	 *
	 * @param WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function save_tokens( WP_REST_Request $request ) {
		$token         = $request->get_param( 'access_token' );
		$refresh_token = $request->get_param( 'refresh_token' );

		if ( empty( $token ) || empty( $refresh_token ) ) {
			return new WP_Error( 'missing_tokens', 'Token and Refresh Token are required.', [ 'status' => 400 ] );
		}

		/**
		 * Access Token and Refresh Token.
		 *
		 * @todo encrypt tokens before saving.
		 */
		$encryption    = new \Jobber\Encryption();
		$token         = $encryption->encrypt( $token );
		$refresh_token = $encryption->encrypt( $refresh_token );
		$tokens        = [
			'access_token'  => $token,
			'refresh_token' => $refresh_token,
		];

		$status = \Jobber\Admin\Settings::update_settings( $tokens );
		return new WP_REST_Response( [ 'success' => $status ? 'true' : 'false' ] );
	}
}
