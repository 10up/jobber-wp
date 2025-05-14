<?php
/**
 * Jobber Authorization Flow
 *
 * @package Jobber
 */

namespace Jobber;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Jobber\REST\Token;

/**
 * Base class for Jobber Authorization Flow
 */
class Auth {

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

		$url_args = [
			'clientUrl' => site_url( Token::get_endpoint( 'validate' ) ),
		];

		$request = wp_remote_post(
			add_query_arg( $url_args, Jobber::get_endpoint( 'refresh' ) ),
			[
				'headers' => $headers,
			]
		);

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
