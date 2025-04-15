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
	 * @return bool
	 */
	public function can_register(): bool {
		return true;
	}

	/**
	 * Hook the module into WP.
	 */
	public function register() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {
		// Get Form.
		register_rest_route(
			self::$namespace,
			'/get_form',
			[
				'methods'             => 'GET',
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

		// Get clients.
		register_rest_route(
			self::$namespace,
			'/clients',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_clients' ],
				'permission_callback' => [ $this, 'has_permission' ],
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
				__( 'No valid form URL found.', 'jobber-wp' ),
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

	/**
	 * Get the clients from Jobber.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_clients(): \WP_REST_Response {
		$jobber  = new \Jobber\Jobber();
		$clients = $jobber->get_clients();

		return rest_ensure_response( $clients );
	}
}
