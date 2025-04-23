<?php
/**
 * Jobber Disconnect Handler
 *
 * @package Jobber
 */

namespace Jobber;

use Jobber\Admin\Settings;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * Class to handle disconnection from Jobber
 */
class Disconnect {

	use Module;

	/**
	 * Only register if we're in an admin context.
	 *
	 * @return bool
	 */
	public function can_register(): bool {
		return true;
	}

	/**
	 * Get the load order for this module.
	 *
	 * @return int
	 */
	public function load_order(): int {
		return 10;
	}

	/**
	 * Register needed hooks.
	 */
	public function register() {
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
		add_action( 'admin_post_jobber_disconnect', [ $this, 'handle_ui_disconnect' ] );
	}

	/**
	 * Register REST API routes.
	 */
	public function register_rest_routes() {
		register_rest_route(
			'jobber/v1',
			'/disconnect',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'handle_rest_disconnect' ],
				'permission_callback' => [ $this, 'verify_middleware_request' ],
			]
		);
	}

	/**
	 * Verify that the request came from the middleware.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return bool|WP_Error
	 */
	public function verify_middleware_request( WP_REST_Request $request ) {
		// TODO: Verify the request came from the middleware.
		// for now, we'll just return true.
		return true;
	}

	/**
	 * Handle disconnect request from the UI.
	 */
	public function handle_ui_disconnect() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'jobber-wp' ) );
		}

		check_admin_referer( 'jobber_disconnect' );

		$this->delete_stored_data();

		wp_safe_redirect( Settings::settings_url() );
		exit;
	}

	/**
	 * Handle disconnect request from the middleware.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function handle_rest_disconnect( WP_REST_Request $request ) {
		$this->delete_stored_data();

		return new WP_REST_Response(
			[
				'success' => true,
				'message' => __( 'Successfully disconnected from Jobber.', 'jobber-wp' ),
			],
			200
		);
	}

	/**
	 * Delete all stored Jobber data.
	 */
	protected function delete_stored_data() {
		// Delete the settings which include the access token
		delete_option( Settings::SETTINGS_KEY );

		// Do action to allow some action to be taken.
		do_action( 'jobber_disconnected' );
	}
} 