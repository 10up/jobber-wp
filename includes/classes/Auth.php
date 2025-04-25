<?php
/**
 * Jobber Authorization Flow
 *
 * @package Jobber
 */

namespace Jobber;

/**
 * Base class for Jobber Authorization Flow
 */
class Auth {

	/**
	 * Jobber Auth Middleware URL.
	 *
	 * @todo Replace this with the actual middleware URL when available.
	 * @var string
	 */
	public static $url = 'http://localhost:8000/auth';

	/**
	 * Jobber Refresh Middleware URL.
	 *
	 * @todo Replace this with the actual middleware URL when available.
	 * @var string
	 */
	public static $refresh_url = 'http://localhost:8000/refresh';

	/**
	 * Determine if the user is authorized.
	 *
	 * @return bool
	 */
	public static function is_authorized(): bool {
		$settings = \Jobber\Admin\Settings::get_settings();
		if ( empty( $settings['authenticated'] ) ) {
			return false;
		}

		return (bool) $settings['authenticated'];
	}

	/**
	 * Get the Jobber Token(s).
	 *
	 * @param string $token The token to get.
	 * @return string Token.
	 */
	public static function get_token( string $token = 'access' ): string {
		$settings = \Jobber\Admin\Settings::get_settings();
		$token    = false !== strpos( $token, '_token' ) ? $token : "{$token}_token";

		if ( empty( $settings[ $token ] ) ) {
			return '';
		}

		return $settings[ $token ];
	}

	/**
	 * Initiate the Jobber refresh token flow.
	 *
	 * @return bool
	 */
	public static function refresh_access_token(): bool {
		$headers = [
			'Content-Type'   => 'application/json',
			'X-JOBBER-TOKEN' => self::get_token( 'jobber' ),
		];

		$args = [
			'headers' => $headers,
		];

		$request = wp_remote_post( self::$refresh_url, $args );

		if ( is_wp_error( $request ) ) {
			return false;
		}

		if ( 200 !== wp_remote_retrieve_response_code( $request ) ) {
			// If the request fails, we can assume things are disconnected.
			\Jobber\Admin\Settings::delete_settings();

			return false;
		}

		return true;
	}
}
