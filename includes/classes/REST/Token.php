<?php
/**
 * REST API for authenticating the Middleware Token.
 *
 * @package Jobber
 */

namespace Jobber\REST;

use WP_REST_Server;
use WP_REST_Request;

/**
 * Middleware Token Authentication
 */
class Token extends API {

	/**
	 * Token Key
	 *
	 * @var string
	 */
	public static $key = 'jobber_token';

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
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'validate_token' ],
				'permission_callback' => '__return_true',
				'args'                => [
					self::$key => [
						'type'     => 'string',
						'required' => true,
					],
				],
			]
		);
	}

	/**
	 * Validate the token generated for the middleware.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @param boolean         $return  Whether to return the result or not.
	 * @return mixed
	 */
	public function validate_token( WP_REST_Request $request, $return = false ) {
		$token = $request->get_param( self::$key );
		if ( $this->validate( $token ) ) {
			if ( $return ) {
				return true;
			}

			wp_send_json_success();
		}

		if ( $return ) {
			return false;
		}
		wp_send_json_error( [ 'message' => 'Invalid token' ], 401 );
	}

	/**
	 * Validate the token generated for the middleware.
	 *
	 * @param string $token The token to validate
	 * @return boolean
	 */
	protected function validate( $token ) {
		if ( empty( $token ) ) {
			return false;
		}

		$saved = get_transient( self::$key );
		if ( empty( $saved ) || $token !== $saved ) {
			return false;
		}

		return true;
	}

	/**
	 * Generate a new token.
	 *
	 * @return string
	 */
	public function generate() {
		return bin2hex( openssl_random_pseudo_bytes( 16 ) );
	}

	/**
	 * Save the token for 5 minutes.
	 *
	 * @param string $token The token to save.
	 * @return void
	 */
	public function save( $token ) {
		set_transient( self::$key, $token, 5 * MINUTE_IN_SECONDS );
	}
}
