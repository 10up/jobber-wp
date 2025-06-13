<?php
/**
 * Plugin name: Jobber Cypress Test Request Mock plugin
 */

// Mock the Jobber HTTP request calls and provide known response.
add_filter( 'pre_http_request', 'jobber_test_mock_http_requests', 10, 3 );

/**
 * Mock Jobber's HTTP requests.
 *
 * @param boolean $preempt     Whether to preempt an HTTP request's return value.
 * @param array   $parsed_args HTTP request arguments.
 * @param string  $url         The request URL.
 * @return boolean|array
 */
function jobber_test_mock_http_requests( $preempt, $parsed_args, $url ) {
	$response = '';

	if ( strpos( $url, 'jobber-prod.10upmanaged.io/jobber/graphql' ) !== false ) {
		$response = file_get_contents( __DIR__ . '/get-form.json' );
	}

	if ( ! empty( $response ) ) {
		return jobber_test_prepare_response( $response );
	}

	return $preempt;
}

/**
 * Prepare mock response for given request.
 *
 * @param string $response Response.
 */
function jobber_test_prepare_response( $response ) {
	return array(
		'headers'     => array(),
		'cookies'     => array(),
		'filename'    => null,
		'response'    => array(
			'code' => 200,
		),
		'status_code' => 200,
		'success'     => 1,
		'body'        => $response,
	);
}

add_action( 'admin_init', function () {
    if ( isset( $_GET['e2e_set_jobber_auth'] ) ) {
        // Update jobber_settings with a mock token (e.g. 'mock_token') in addition to authenticated=true.
        update_option( 'jobber_settings', '{"authenticated":true,"jobber_token":"mock_jobber_token","access_token":"mock_access_token"}' );
        // Optionally redirect to clean up the URL
        wp_safe_redirect( remove_query_arg( 'e2e_set_jobber_auth' ) );
        exit;
    }
} );
