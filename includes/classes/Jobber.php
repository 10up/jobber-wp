<?php
/**
 * Jobber API Connection
 *
 * @package Jobber
 */

namespace Jobber;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Jobber\Module;
use Jobber\REST\Token;
use WP_Error;

use function Jobber\Utility\get_cached_data;
use function Jobber\Utility\set_cached_data;

/**
 * Base class for Jobber API Connection
 */
class Jobber {

	use Module;

	/**
	 * API URL.
	 *
	 * @var string
	 */
	protected static $api_url = 'https://jobber-prod.10upmanaged.io';

	/**
	 * Query keyword used to ask the middleware for the full list of forms.
	 *
	 * @var string
	 */
	const FORMS_QUERY = 'forms';

	/**
	 * API Access Token.
	 *
	 * @var string
	 */
	private $access_token;

	/**
	 * Module constructor.
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
		add_action( 'jobber_rebuild_cache', [ $this, 'rebuild_cache' ] );
		add_filter( 'allowed_redirect_hosts', [ $this, 'allow_jobber_redirect' ] );
	}

	/**
	 * Get the API URL.
	 *
	 * @return string
	 */
	public static function get_api_url(): string {
		// Allow for a custom API URL to be set.
		if ( defined( 'JOBBER_API_URL' ) ) {
			return \JOBBER_API_URL;
		}

		return self::$api_url;
	}

	/**
	 * Get the endpoint for the Jobber API.
	 *
	 * @param string $path The path to the endpoint.
	 * @return string
	 */
	public static function get_endpoint( string $path = 'jobber' ): string {
		return self::get_api_url() . "/{$path}";
	}

	/**
	 * Rebuild the cache.
	 *
	 * @param string $form_type The form type.
	 */
	public function rebuild_cache( string $form_type = '' ) {
		$this->get_form( $form_type, true );
	}

	/**
	 * Add the middleware URL to the allowed redirect hosts.
	 *
	 * @param array $hosts Allowed Redirect Hosts.
	 * @return array
	 */
	public function allow_jobber_redirect( $hosts ) {
		$hosts[] = wp_parse_url( self::get_api_url(), PHP_URL_HOST );
		return $hosts;
	}

	/**
	 * Send a disconnect request to the middleware.
	 *
	 * @return array|WP_Error
	 */
	public function disconnect() {
		$disconnect_url = add_query_arg(
			[
				'clientUrl' => site_url( Token::get_endpoint( 'validate' ) ),
			],
			self::get_endpoint( 'disconnect' )
		);

		$request = wp_remote_post(
			$disconnect_url,
			[
				'headers' => [
					'Content-Type'   => 'application/json',
					'X-JOBBER-TOKEN' => $this->access_token,
				],
			]
		);

		if ( is_wp_error( $request ) ) {
			return $request;
		}

		return true;
	}

	/**
	 * Send a query request to the middleware.
	 *
	 * @param string $form_type Form type we want.
	 * @param bool   $force     Force a new request and bypass cache.
	 * @return array|WP_Error
	 */
	protected function query( string $form_type = '', bool $force = false ) {
		/**
		 * Short circuits a query to the middleware.
		 *
		 * Returning anything other than false skips the HTTP request entirely. Intended for
		 * local development, fixtures and end to end tests, where a live Jobber account is
		 * not available.
		 *
		 * @since x.x.x
		 * @hook jobber_pre_query
		 *
		 * @param false|array<string, mixed> $response  Short circuited response. Default false.
		 * @param string                     $form_type The query being run.
		 *
		 * @return false|array<string, mixed> Filtered response.
		 */
		$pre = apply_filters( 'jobber_pre_query', false, $form_type );

		if ( false !== $pre ) {
			return $pre;
		}

		if ( empty( $this->access_token ) ) {
			return new WP_Error( 'jobber_no_access_token', __( 'No token found.', 'jobber' ) );
		}

		$data      = [ 'query' => $form_type ];
		$cache_key = 'jobber_query_' . md5( wp_json_encode( $data ) );
		$response  = get_cached_data( $cache_key, $form_type, $force );

		// If we have a cached response, return it.
		if ( false !== $response ) {
			return $response;
		}

		// Request headers.
		$headers = [
			'Content-Type'   => 'application/json',
			'X-JOBBER-TOKEN' => $this->access_token,
		];

		// Request arguments.
		$args = [
			'headers' => $headers,
			'body'    => wp_json_encode( $data ),
		];

		// Execute the request.
		$endpoint = self::get_endpoint( 'jobber/graphql' );
		$request  = wp_remote_post( $endpoint, $args );

		if ( is_wp_error( $request ) ) {
			return $request;
		}

		// Check for an expired access token.
		if ( 401 === wp_remote_retrieve_response_code( $request ) ) {
			// Attempt to refresh the access token.
			$refresh_response = Auth::refresh_access_token();

			// If the refresh was successful, try the request again.
			if ( $refresh_response ) {
				// Execute the request again.
				$request = wp_remote_post( $endpoint, $args );
				if ( is_wp_error( $request ) ) {
					return $request;
				}
			}
		}

		$response = json_decode( wp_remote_retrieve_body( $request ), true );
		if ( isset( $response['errors'] ) ) {
			$errors = wp_list_pluck( $response['errors'], 'message' );
			return new WP_Error( 'jobber_graphql_error', implode( ' | ', $errors ) );
		}

		set_cached_data( $cache_key, $response );

		return $response;
	}

	/**
	 * Get the form from Jobber.
	 *
	 * @param string $form_type The type of form to get. Default is 'request'.
	 * @param bool   $force     Force a new request and bypass cache.
	 * @return array|WP_Error
	 */
	public function get_form( string $form_type = 'request', bool $force = false ) {
		if ( 'booking' === $form_type ) {
			$form_type = 'booking';
		} elseif ( 'request' === $form_type ) {
			$form_type = 'request';
		} else {
			return new WP_Error( 'jobber_invalid_form_type', __( 'Invalid form type.', 'jobber' ) );
		}

		return $this->query( $form_type, $force );
	}

	/**
	 * Get every enabled form on the connected Jobber account.
	 *
	 * An account can have any number of forms, so this replaces the previous
	 * fixed choice between a booking form and a request form. Filtering to
	 * enabled forms happens at the query level, on the middleware.
	 *
	 * @param bool $force Force a new request and bypass cache.
	 * @return array<int, array<string, mixed>>|WP_Error List of normalized forms, or an error.
	 */
	public function get_forms( bool $force = false ) {
		$response = $this->query( self::FORMS_QUERY, $force );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return self::normalize_forms( $response );
	}

	/**
	 * Get a single form by its identifier.
	 *
	 * @param string $form_id The form identifier, as returned by get_forms().
	 * @param bool   $force   Force a new request and bypass cache.
	 * @return array<string, mixed>|WP_Error The form, or an error when it cannot be found.
	 */
	public function get_form_by_id( string $form_id, bool $force = false ) {
		$forms = $this->get_forms( $force );

		if ( is_wp_error( $forms ) ) {
			return $forms;
		}

		foreach ( $forms as $form ) {
			if ( (string) $form['id'] === $form_id ) {
				return $form;
			}
		}

		return new WP_Error(
			'jobber_form_not_found',
			__( 'The selected form is no longer available on this Jobber account.', 'jobber' ),
			[ 'status' => 404 ]
		);
	}

	/**
	 * Normalize a forms response into a predictable shape.
	 *
	 * Jobber returns `requestSettingsCollection.nodes`. Each node is expected to carry
	 * `name`, `requestUrl`, `bookingType`, `default` and `enabled`. An `id` is used when
	 * present, and `requestUrl` stands in as the identifier when it is not, because the
	 * block has to persist something stable and a form's name can be edited by the user.
	 *
	 * @param array<string, mixed> $response Raw decoded response.
	 * @return array<int, array<string, mixed>>
	 */
	public static function normalize_forms( array $response ): array {
		$nodes = $response['data']['requestSettingsCollection']['nodes'] ?? [];

		if ( ! is_array( $nodes ) ) {
			return [];
		}

		$forms = [];

		foreach ( $nodes as $node ) {
			if ( ! is_array( $node ) ) {
				continue;
			}

			$url = (string) ( $node['requestUrl'] ?? '' );

			// Without a URL there is nothing to embed, so the entry is unusable.
			if ( '' === $url ) {
				continue;
			}

			$forms[] = [
				'id'          => (string) ( $node['id'] ?? $url ),
				'name'        => (string) ( $node['name'] ?? __( 'Untitled form', 'jobber' ) ),
				'url'         => $url,
				'bookingType' => (string) ( $node['bookingType'] ?? '' ),
				'isDefault'   => ! empty( $node['default'] ),
				'embedScript' => (string) ( $node['requestEmbedScript'] ?? $node['embedScript'] ?? '' ),
			];
		}

		return $forms;
	}
}
