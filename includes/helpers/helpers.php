<?php
/**
 * Plugin specific helpers.
 *
 * @package Jobber
 */

namespace Jobber;

if ( ! defined( 'ABSPATH' ) ) {
	return; // Return if accessed directly.
}

/**
 * Get an initialized class by its full class name, including namespace.
 *
 * @param string $class_name The class name including the namespace.
 * @return false|ModuleInterface
 */
function get_module( $class_name ) {
	return ModuleInitialization::get_module( $class_name );
}
