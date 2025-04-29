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
		add_action( 'admin_post_jobber_disconnect', [ $this, 'handle_ui_disconnect' ] );
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
	 * Delete all stored Jobber data.
	 */
	protected function delete_stored_data() {
		// Delete the settings which include the access token
		delete_option( Settings::SETTINGS_KEY );

		// Do action to allow some action to be taken.
		do_action( 'jobber_disconnected' );
	}
}
