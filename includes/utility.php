<?php
/**
 * Utility functions.
 *
 * @package Jobber
 */

namespace Jobber\Utility;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Option name holding the index of every cache key this plugin has written.
 *
 * @var string
 */
const CACHE_INDEX_OPTION = 'jobber_query_cache_keys';

/**
 * Get asset info from extracted asset files
 *
 * @param string $slug Asset slug as defined in build/webpack configuration
 * @param string $attribute Optional attribute to get. Can be version or dependencies
 * @return ($attribute is null ? array{version: string, dependencies: array<string>} : $attribute is 'dependencies' ? array<string> : string)
 */
function get_asset_info( $slug, $attribute = null ) {
	if ( file_exists( JOBBER_PLUGIN_PATH . 'dist/js/' . $slug . '.asset.php' ) ) {
		$asset = require JOBBER_PLUGIN_PATH . 'dist/js/' . $slug . '.asset.php';
	} elseif ( file_exists( JOBBER_PLUGIN_PATH . 'dist/css/' . $slug . '.asset.php' ) ) {
		$asset = require JOBBER_PLUGIN_PATH . 'dist/css/' . $slug . '.asset.php';
	} else {
		$asset = [
			'version'      => JOBBER_PLUGIN_VERSION,
			'dependencies' => [],
		];
	}

	// @var <array{version: string, dependencies: array<string>}> $asset

	if ( ! empty( $attribute ) && isset( $asset[ $attribute ] ) ) {
		return $asset[ $attribute ];
	}

	return $asset;
}

/**
 * The list of known contexts for enqueuing scripts/styles.
 *
 * @return array<string>
 */
function get_enqueue_contexts(): array {
	return [ 'admin', 'frontend', 'shared' ];
}

/**
 * Generate an URL to a script, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @throws \RuntimeException If an invalid $context is specified.
 *
 * @param string $script Script file name (no .js extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared')
 * @return string
 */
function script_url( string $script, string $context ): string {
	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		throw new \RuntimeException( 'Invalid $context specified in Jobber script loader.' );
	}

	return JOBBER_PLUGIN_URL . "dist/js/{$script}.js";
}

/**
 * Generate an URL to a stylesheet, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @throws \RuntimeException If an invalid $context is specified.
 *
 * @param string $stylesheet Stylesheet file name (no .css extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared')
 * @return string
 */
function style_url( string $stylesheet, string $context ): string {
	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		throw new \RuntimeException( 'Invalid $context specified in Jobber stylesheet loader.' );
	}

	return JOBBER_PLUGIN_URL . "dist/css/{$stylesheet}.css";
}

/**
 * Get cached data.
 *
 * @param string $key The cache key.
 * @param string $form_type The form type.
 * @param bool   $force Whether to force a new request and bypass cache.
 * @return mixed The cached data or false if no data is found.
 */
function get_cached_data( string $key, string $form_type = '', bool $force = false ) {
	// Always return false if we want to force a new request.
	if ( $force ) {
		return false;
	}

	$data = get_transient( $key );

	// If we have a cached response, return it.
	if ( false !== $data ) {
		return $data;
	} else {
		// If we don't have a cached response, try to get it from the database.
		$data = get_option( $key, false );

		// If we have data here but not in cache, rebuild the cache.
		if ( false !== $data && ! wp_next_scheduled( 'jobber_rebuild_cache', [ $form_type ] ) ) {
			wp_schedule_single_event( time() + 1, 'jobber_rebuild_cache', [ $form_type ] );

			// Update the cache with the current data.
			// This is to handle a situation where the API is down and
			// rebuilding the cache won't work, leading to a loop of failed requests.
			set_cached_data( $key, $data );
		}

		return $data;
	}

	return false;
}

/**
 * Store cached data.
 *
 * Data is written to both a transient and an option. The option acts as a fallback
 * when the transient expires or is evicted, so the cache key is also recorded in an
 * index to make cleanup possible later.
 *
 * @param string $key The cache key.
 * @param mixed  $data The data to cache.
 */
function set_cached_data( string $key, $data ) {
	set_transient( $key, $data, DAY_IN_SECONDS );
	update_option( $key, $data );
	register_cache_key( $key );
}

/**
 * Record a cache key in the index of known cache keys.
 *
 * Cache keys are derived from the request body, so they cannot be reconstructed
 * without knowing every request that was ever made. The index is what allows
 * deactivation to clean up after itself.
 *
 * @param string $key The cache key.
 */
function register_cache_key( string $key ) {
	$keys = get_cache_keys();

	if ( in_array( $key, $keys, true ) ) {
		return;
	}

	$keys[] = $key;
	update_option( CACHE_INDEX_OPTION, $keys );
}

/**
 * Get every known cache key.
 *
 * @return array<string>
 */
function get_cache_keys(): array {
	$keys = get_option( CACHE_INDEX_OPTION, [] );

	return is_array( $keys ) ? $keys : [];
}

/**
 * Delete a single cached entry, both the transient and the option fallback.
 *
 * @param string $key The cache key.
 */
function delete_cached_data( string $key ) {
	delete_transient( $key );
	delete_option( $key );
}

/**
 * Delete every cached entry and the index itself.
 *
 * Also clears the two keys used before the index existed, so sites upgrading from
 * an earlier version do not leave options behind.
 */
function delete_all_cached_data() {
	foreach ( get_cache_keys() as $key ) {
		delete_cached_data( $key );
	}

	// Legacy keys, from before the cache index was introduced.
	foreach ( [ 'booking', 'request' ] as $form_type ) {
		delete_cached_data( 'jobber_query_' . md5( wp_json_encode( [ 'query' => $form_type ] ) ) );
	}

	delete_option( CACHE_INDEX_OPTION );
}
