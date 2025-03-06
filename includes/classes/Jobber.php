<?php
/**
 * Jobber API Connection
 *
 * @package Jobber
 */

namespace Jobber;

use WP_Error;
use TenupFramework\Module;

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
	protected $api_url = 'https://api.getjobber.com/api';

	/**
	 * API Access Token
	 *
	 * @var string
	 */
	private $access_token;

	/**
	 * Refresh Token
	 *
	 * @var string
	 */
	private $refresh_token;

	/**
	 * Module Constructor
	 */
	public function __construct() {
		$this->access_token  = Auth::get_token( 'access' );
		$this->refresh_token = Auth::get_token( 'refresh' );
	}

	/**
	 * Can we register this module?
	 *
	 * @return boolean
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Hook module into WP.
	 *
	 * @return void
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
	 * Execute a GraphQL Query.
	 * We must use cURL because wp_remote_post does not work with GraphQL.
	 *
	 * @param string $query GraphQL Query.
	 * @return array|WP_Error
	 */
	protected function query( $query ) {
		if ( empty( $this->access_token ) ) {
			return new WP_Error( 'jobber_no_access_token', __( 'No access token found.', 'jobber-plugin' ) );
		}

		// GraphQL Query
		$data = [
			'query' => $query,
		];

		// Request Headers
		$headers = [
			'Authorization' => "Bearer {$this->access_token}",
			'Content-Type'  => 'application/json',
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
			return new WP_Error( 'jobber_graphql_error', $response['errors'][0]['message'] );
		}

		return $response;
	}
}
