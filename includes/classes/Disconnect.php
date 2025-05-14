<?php
/**
 * Jobber Disconnect Handler
 *
 * @package Jobber
 */

namespace Jobber;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Jobber\Admin\Settings;

/**
 * Class to handle disconnection from Jobber
 */
class Disconnect {

	use Module;

	/**
	 * Only register if a user has correct capability to disconnect.
	 *
	 * @return bool
	 */
	public function can_register(): bool {
		return current_user_can( 'manage_options' );
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
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'jobber' ) );
		}

		check_admin_referer( 'jobber_disconnect' );

		Settings::delete_settings();

		wp_safe_redirect( Settings::settings_url() );
		exit;
	}
}
