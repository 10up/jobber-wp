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
	 * Action parameter for the disconnect request.
	 *
	 * @var string
	 */
	const ACTION = 'jobber_disconnect';

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
		add_action( 'admin_post_' . self::ACTION, [ $this, 'handle_ui_disconnect' ] );
	}

	/**
	 * Handle disconnect request from the UI.
	 */
	public function handle_ui_disconnect() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'jobber' ) );
		}

		check_admin_referer( self::ACTION );

		// Send disconnect request to the middleware.
		$disconnect = ( new Jobber() )->disconnect();
		if ( ! $disconnect || is_wp_error( $disconnect ) ) {
			wp_die( esc_html__( 'Failed to disconnect from Jobber.', 'jobber' ) );
		}

		// Redirect to the settings page with the disconnected flag.
		$redirect_url = add_query_arg(
			[ self::ACTION => 'true' ],
			Settings::settings_url()
		);
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Disconnect the client from the Jobber API.
	 *
	 * Deletes the Jobber settings from the database.
	 */
	public static function disconnect_client() {
		Settings::delete_settings();
	}
}
