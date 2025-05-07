<?php
/**
 * Jobber REST API Base
 *
 * @package Jobber
 */

namespace Jobber\REST;

use Jobber\Module;
use Jobber\Disconnect;
use WP_REST_Server;

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
	 * API Route
	 *
	 * @var string
	 */
	public static $route = '';

	/**
	 * Can we register this module?
	 *
	 * @return bool
	 */
	public function can_register(): bool {
		return true;
	}

	/**
	 * Register needed hooks.
	 */
	public function register() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {
		// Get form.
		register_rest_route(
			self::$namespace,
			'/get_form',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_form' ],
				'permission_callback' => [ $this, 'has_permission' ],
				'args'                => [
					'form_type' => [
						'type'     => 'string',
						'required' => true,
					],
				],
			]
		);

		// Disconnect.
		register_rest_route(
			self::$namespace,
			'/disconnect',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'handle_disconnect' ],
				'permission_callback' => '__return_true',
				'args'                => [
					Token::$key => [
						'type'     => 'string',
						'required' => true,
					],
				],
			]
		);
	}

	/**
	 * Check if the user has permission to access the route.
	 *
	 * @return bool
	 */
	public function has_permission(): bool {
		return is_user_logged_in();
	}

	/**
	 * Get the endpoint for token API routes.
	 *
	 * @param string $type The type of endpoint to get.
	 * @return string
	 */
	public static function get_endpoint( string $type = '' ): string {
		return sprintf(
			'wp-json/%1$s/%2$s/%3$s',
			static::$namespace,
			ltrim( static::$route, '/' ),
			$type
		);
	}

	/**
	 * Handle the disconnect request.
	 *
	 * @param \WP_REST_Request $request The REST Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function handle_disconnect( \WP_REST_Request $request ) {
		$client_token = $request->get_param( Token::$key );

		if ( empty( $client_token ) ) {
			return new \WP_Error( 'no_client_token', __( 'No client token found.', 'jobber' ), [ 'status' => 400 ] );
		}

		$token = new Token();
		if ( ! $token->validate( $client_token ) ) {
			wp_send_json_error( [ 'message' => 'Invalid token' ], 401 );
		}

		// Disconnect the client.
		( new Disconnect() )->disconnect_client();

		wp_send_json_success();
	}

	/**
	 * Get the form from Jobber.
	 *
	 * @param \WP_REST_Request $request The REST Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_form( \WP_REST_Request $request ) {
		$form_type = $request->get_param( 'form_type' );

		if ( empty( $form_type ) ) {
			$form_type = 'request';
		}

		$jobber = new \Jobber\Jobber();
		$form   = $jobber->get_form( $form_type );

		if ( is_wp_error( $form ) ) {
			return rest_ensure_response( $form );
		}

		// Pull just the iframe/embed URL.
		$url = '';

		if (
			'request' === $form_type &&
			isset( $form['data']['requestSettings']['requestUrl'] )
		) {
			$url = $form['data']['requestSettings']['requestUrl'];
		} elseif (
			'booking' === $form_type &&
			isset( $form['data']['onlineBookingConfiguration']['bookingUrl'] )
		) {
			$url = $form['data']['onlineBookingConfiguration']['bookingUrl'] . '/embedded';
		}

		if ( empty( $url ) ) {
			return new \WP_Error(
				'no_url_found',
				__( 'No valid form URL found.', 'jobber' ),
				[ 'status' => 500 ]
			);
		}

		return rest_ensure_response(
			[
				'form' => [
					'iframeUrl' => esc_url_raw( $url ),
				],
			]
		);
	}
}
