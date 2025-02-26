<?php
/**
 * Jobber Authorization Flow
 *
 * @package Jobber
 */

namespace Jobber;

use WP_Error;

/**
 * Base class for Jobber Authorization Flow
 */
class Auth {
	/**
	 * Jobber API Token Key
	 * This is the key used to store the Jobber API token in the options table.
	 *
	 * @var string
	 */
	const TOKEN_KEY = 'jobber_token';

	/**
	 * Jobber API Refresh Token Key
	 * This is the key used to store the Jobber API refresh token in the options table.
	 *
	 * @var string
	 */
	const REFRESH_KEY = 'jobber_refresh_token';

	/**
	 * Jobber API Access Token
	 *
	 * @var string
	 */
	public $access_token;

	/**
	 * Jobber API Refresh Token
	 *
	 * @var string
	 */
	public $refresh_token;

	/**
	 * Jobber Middleware URL
	 *
	 * @var string
	 */
	public static $middleware = '';

	/**
	 * Determine if the user is authorized.
	 *
	 * @return boolean
	 */
	public static function is_authorized() {
		return ! empty( self::get_token() ) && ! empty( self::get_refresh_token() );
	}

	/**
	 * Get the Middleware URL.
	 *
	 * @param string $path Path to append to the middleware URL.
	 * @return string
	 */
	public static function middleware_url( $path = '' ) {
		return trailingslashit( self::$middleware ) . ltrim( $path, '/' );
	}

	/**
	 * Get the Jobber API Token
	 *
	 * @return string
	 */
	public static function get_token() {
		return get_option( self::TOKEN_KEY );
	}

	/**
	 * Get the Jobber API Refresh Token
	 *
	 * @return string
	 */
	public static function get_refresh_token() {
		return get_option( self::REFRESH_KEY );
	}
}
