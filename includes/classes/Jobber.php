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
		$this->access_token  = Auth::get_token();
		$this->refresh_token = Auth::get_refresh_token();
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
	 * Allow Jobber Redirect
	 *
	 * @param array $hosts Allowed Redirect Hosts.
	 * @return array
	 */
	public function allow_jobber_redirect( $hosts ) {
		$hosts[] = wp_parse_url( $this->api_url, PHP_URL_HOST );
		return $hosts;
	}

	/**
	 * Query for the RequestForm object.
	 *
	 * @return string|\WP_Error
	 */
	public function query_request_form() {
		// Form graphql query
		$query = '
		query RequestForm {
			requestSettings {
				requestUrl
				id
			}
		}';

		$form = $this->graphql_query( $query );
		if ( ! is_wp_error( $form ) && ! empty( $form['data']['requestSettings'] ) ) {
			$form = [
				'url' => $form['data']['requestSettings']['requestUrl'],
				'id'  => $form['data']['requestSettings']['id'],
			];
		}

		return $form;
	}

	/**
	 * Execute a GraphQL Query.
	 * We must use cURL because wp_remote_post does not work with GraphQL.
	 *
	 * @param string $query GraphQL Query.
	 * @return array|WP_Error
	 */
	protected function graphql_query( $query ) {
		if ( empty( $this->access_token ) ) {
			return new WP_Error( 'jobber_no_access_token', 'No access token found.' );
		}

		$data = [
			'query' => $query,
		];

		$headers = [
			'Authorization' => "Bearer {$this->access_token}",
			'Content-Type'  => 'application/json',
		];

		// phpcs:disable
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, "{$this->api_url}/graphql" );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 0 );

		$response = curl_exec( $ch );
		curl_close( $ch );
		// phpcs:enable

		$response = json_decode( $response, true );
		if ( isset( $response['errors'] ) ) {
			return new WP_Error( 'jobber_graphql_error', $response['errors'][0]['message'] );
		}

		return $response;
	}
}
