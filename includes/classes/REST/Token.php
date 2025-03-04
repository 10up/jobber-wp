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
	protected $key = 'jobber_token';

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
				'callback' => [ $this, 'validate_token' ],
				'args'     => [
					$this->key => [
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
	 * @return mixed
	 */
	public function validate_token( WP_REST_Request $request ) {
		$token = $request->get_param( $this->key );
		if ( $this->validate( $token ) ) {
			wp_send_json_success();
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

		$saved = get_transient( $this->key );
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
}
