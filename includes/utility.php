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
