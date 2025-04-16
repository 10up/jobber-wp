<?php
/**
 * Jobber Authorization Flow
 *
 * @package Jobber
 */

namespace Jobber;

/**
 * Base class for the Jobber Authorization Flow
 */
class Auth {

	/**
	 * Jobber Auth Middleware URL
	 *
	 * @TODO Replace this with the actual middleware URL when available.
	 * @var string
	 */
	public static $url = 'http://localhost:8000/wp/auth';

	/**
	 * Determine if the user is authorized.
	 *
	 * @return bool
	 */
	public static function is_authorized(): bool {
		return ! empty( self::get_token() );
	}

	/**
	 * Get the Jobber API Token(s)
	 *
	 * @param string $token The token to get: access or refresh.
	 * @return string Decrypted token.
	 */
	public static function get_token( string $token = 'access' ): string {
		$settings = \Jobber\Admin\Settings::get_settings();
		$token    = false !== strpos( $token, '_token' ) ? $token : "{$token}_token";

		if ( empty( $settings[ $token ] ) ) {
			return '';
		}

		// Decrypt token.
		$encryption      = new Encryption();
		$decrypted_token = $encryption->decrypt( $settings[ $token ] );

		return false === $decrypted_token ? '' : $decrypted_token;
	}
}
