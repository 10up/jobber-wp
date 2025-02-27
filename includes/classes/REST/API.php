<?php
/**
 * Jobber REST API Base
 *
 * @package Jobber
 */

namespace Jobber\REST;

use TenupFramework\Module;

/**
 * Jobber API Base
 */
abstract class API {

	use Module;

	/**
	 * API Namespace
	 *
	 * @var string
	 */
	public static $namespace = 'jobber/v1';

	/**
	 * Can we register this module?
	 *
	 * @return boolean
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Hook the module into WP.
	 *
	 * @return void
	 */
	abstract public function register();
}
