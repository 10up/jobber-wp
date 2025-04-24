<?php
/**
 * Jobber API Connection
 *
 * @package Jobber
 */

namespace Jobber;

use Jobber\Module;
use WP_Error;

/**
 * Base class for Jobber API Connection
 */
class Jobber {

	use Module;

	/**
	 * API URL
	 *
	 * @var string
	 */
	protected $api_url = 'http://localhost:8000/jobber';

	/**
	 * API Access Token
	 *
	 * @var string
	 */
	private $access_token;

	/**
	 * Module Constructor
	 */
	public function __construct() {
		$this->access_token = Auth::get_token( 'jobber' );
	}

	/**
	 * Can we register this module?
	 *
	 * @return bool
	 */
	public function can_register(): bool {
		return true;
	}

	/**
	 * Register needed hooks.
	 */
	public function register() {
		add_filter( 'allowed_redirect_hosts', [ $this, 'allow_jobber_redirect' ] );
	}

	/**
	 * Add the middleware URL to the allowed redirect hosts.
	 *
	 * @param array $hosts Allowed Redirect Hosts.
	 * @return array
	 */
	public function allow_jobber_redirect( $hosts ) {
		$hosts[] = wp_parse_url( Auth::$url, PHP_URL_HOST );
		return $hosts;
	}

	/**
	 * Send a query request to the middleware.
	 *
	 * @param string $form_type Form type we want.
	 * @return array|WP_Error
	 */
	protected function query( string $form_type ) {
		if ( empty( $this->access_token ) ) {
			return new WP_Error( 'jobber_no_access_token', __( 'No access token found.', 'jobber-wp' ) );
		}

		$data = [
			'query' => $form_type,
		];

		// Request Headers
		$headers = [
			'Content-Type'   => 'application/json',
			'X-JOBBER-TOKEN' => $this->access_token,
		];

		// Request Arguments
		$args = [
			'headers' => $headers,
			'body'    => wp_json_encode( $data ),
		];

		// Execute the request
		$request = wp_remote_post( "{$this->api_url}/graphql", $args );
		if ( is_wp_error( $request ) ) {
			return $request;
		}

		$response = json_decode( wp_remote_retrieve_body( $request ), true );
		if ( isset( $response['errors'] ) ) {
			$errors = wp_list_pluck( $response['errors'], 'message' );
			return new WP_Error( 'jobber_graphql_error', implode( ' | ', $errors ) );
		}

		return $response;
	}

	/**
	 * Get the form from Jobber.
	 *
	 * @param string $form_type The type of form to get. Default is 'request'.
	 * @return array|WP_Error
	 */
	public function get_form( string $form_type = 'request' ) {
		if ( 'booking' === $form_type ) {
			$form_type = 'booking';
		} elseif ( 'request' === $form_type ) {
			$form_type = 'request';
		} else {
			return new WP_Error( 'jobber_invalid_form_type', __( 'Invalid form type.', 'jobber-wp' ) );
		}

		return $this->query( $form_type );
	}
}
