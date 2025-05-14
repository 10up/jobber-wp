<?php
/**
 * REST API for authenticating the Middleware Token.
 *
 * @package Jobber
 */

namespace Jobber\REST;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Jobber\Admin\Settings;
use Jobber\Disconnect;
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
	public static $route = '/token';

	/**
	 * Register needed hooks.
	 */
	public function register() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
		add_action( 'init', [ $this, 'check_token' ] );
	}

	/**
	 * Check for a valid authentication.
	 */
	public function check_token() {
		/* phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended */
		if ( ! isset( $_GET[ self::$key ] ) ) {
			return;
		}

		// Make sure we have a valid token before saving.
		$token = sanitize_text_field( wp_unslash( $_GET[ self::$key ] ) );

		if (
			$this->validate( $token ) &&
			! empty( $_GET['tokens'] )
		) {
			Settings::update_settings( [ 'authenticated' => true ] );
		}
		/* phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended */
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {
		register_rest_route(
			self::$namespace,
			self::$route . '/generate',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'generate_token' ],
				'permission_callback' => [ $this, 'generate_token_permission_check' ],
			]
		);

		// Disconnect route.
		register_rest_route(
			self::$namespace,
			self::$route . '/validate',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'validate_token' ],
				'permission_callback' => '__return_true',
				'args'                => [
					self::$key         => [
						'type'     => 'string',
						'required' => true,
					],
					Disconnect::ACTION => [
						'type'     => 'string',
						'required' => false,
					],
				],
			]
		);
	}

	/**
	 * Check if the user has permission to access the route.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return bool
	 */
	public function generate_token_permission_check( WP_REST_Request $request ): bool {
		$nonce = $request->get_param( self::$key );

		// Ensure we have a nonce.
		if ( ! $nonce ) {
			return false;
		}

		// Check the nonce.
		$settings = Settings::get_settings();
		if ( empty( $settings['nonce'] ) ) {
			return false;
		}

		if ( $nonce !== $settings['nonce'] ) {
			return false;
		}

		return true;
	}

	/**
	 * Generate a token or return an existing one.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @param bool            $rtn     Whether to return the result or not.
	 * @return mixed
	 */
	public function generate_token( WP_REST_Request $request, bool $rtn = false ) {
		$token        = $this->get_token();
		$validate_url = self::get_endpoint( 'validate' );

		if ( ! empty( $token ) ) {
			if ( $rtn ) {
				return $token;
			}

			wp_send_json_success(
				[
					'clientToken' => $token,
					'validateUrl' => $validate_url,
				]
			);
		}

		$token = $this->generate();
		$this->save( $token );

		if ( $rtn ) {
			return $token;
		}

		wp_send_json_success(
			[
				'clientToken' => $token,
				'validateUrl' => $validate_url,
			]
		);
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

			// If we're validating a disconnect request, disconnect the client
			// after a successful validation. We have to do this here because
			// the disconnect_client() method deletes the token from the database,
			// and we need to validate the token first.
			$disconnect = $request->get_param( Disconnect::ACTION );
			if ( $disconnect && 'true' === $disconnect ) {
				Disconnect::disconnect_client();
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
	public function validate( string $token ): bool {
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
	 * Save the token.
	 *
	 * @param string $token The token to save.
	 */
	public function save( $token ) {
		Settings::update_settings( [ self::$key => $token ] );
	}

	/**
	 * Get the token.
	 *
	 * @return string
	 */
	public static function get_token(): string {
		$settings = Settings::get_settings();
		if ( empty( $settings[ self::$key ] ) ) {
			return '';
		}

		return $settings[ self::$key ];
	}
}
