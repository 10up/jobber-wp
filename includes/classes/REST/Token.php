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
	 * Register needed hooks.
	 */
	public function register() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
		add_action( 'init', [ $this, 'check_token' ] );
	}

	/**
	 * Check for a valid authentication.
	 *
	 * @return void
	 */
	public function check_token() {
		/* phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized */
		if ( ! isset( $_GET[ self::$key ] ) ) {
			return;
		}

		// Make sure we have a valid token before saving.
		$token = sanitize_text_field( wp_unslash( $_GET[ self::$key ] ) );
		if (
			$this->validate( $token ) &&
			! empty( $_GET['tokens'] )
		) {
			\Jobber\Admin\Settings::update_settings( [ 'authenticated' => true ] );
		}
		/* phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized */
	}

	/**
	 * Register the REST API routes.
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
	 * Get the endpoint for the token.
	 *
	 * @return string
	 */
	public static function get_endpoint() {
		$namespace = self::$namespace;
		return "wp-json/{$namespace}/" . ltrim( self::$route, '/' );
	}

	/**
	 * Validate the token generated for the middleware.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @param bool            $rtn     Whether to return the result or not.
	 * @return mixed
	 */
	public function validate_token( WP_REST_Request $request, bool $rtn = false ) {
		$token = $request->get_param( self::$key );

		if ( $this->validate( $token ) ) {
			if ( $rtn ) {
				return true;
			}

			wp_send_json_success();
		}

		if ( $rtn ) {
			return false;
		}

		wp_send_json_error( [ 'message' => 'Invalid token' ], 401 );
	}

	/**
	 * Validate the token generated for the middleware.
	 *
	 * @param string $token The token to validate
	 * @return bool
	 */
	protected function validate( string $token ): bool {
		if ( empty( $token ) ) {
			return false;
		}

		$saved = self::get_token();
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
	public function generate(): string {
		return bin2hex( openssl_random_pseudo_bytes( 16 ) );
	}

	/**
	 * Save the token for 5 minutes.
	 *
	 * @param string $token The token to save.
	 */
	public function save( $token ) {
		\Jobber\Admin\Settings::update_settings( [ self::$key => $token ] );
	}

	/**
	 * Get the token.
	 *
	 * @return string|bool
	 */
	public static function get_token(): string {
		$settings = \Jobber\Admin\Settings::get_settings();
		if ( empty( $settings[ self::$key ] ) ) {
			return '';
		}

		return $settings[ self::$key ];
	}
}
