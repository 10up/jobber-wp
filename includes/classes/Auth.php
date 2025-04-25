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
	 * Jobber Auth Middleware URL
	 *
	 * @todo Replace this with the actual middleware URL when available.
	 * @var string
	 */
	public static $url = 'http://localhost:8000/auth';

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
}
