<?php
/**
 * Jobber Blocks.
 *
 * @package Jobber
 */
namespace Jobber;

use TenupFramework\Module;

/**
 * Base class for Jobber Blocks
 */
class Blocks {
	use Module;

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
	public function register() {
		add_action( 'init', [ $this, 'register_block_types' ] );
	}

	/**
	 * Register the block types.
	 *
	 * @return void
	 */
	public function register_block_types() {
		register_block_type( JOBBER_PLUGIN_PATH . 'blocks/jobber', [
			'render_callback' => [ $this, 'render_block' ],
		] );
	}

	/**
	 * Render the block.
	 *
	 * @param array $attributes The block attributes.
	 *
	 * @return string
	 */
	public function render_block( $attributes ) {
		if ( empty( $attributes['embedCode'] ) ) {
			return '<p>Please enter the Jobber embed code in the block settings.</p>';
		}
	
		return sprintf(
			'<div class="jobber-embed-block">%s</div>',
			$attributes['embedCode']
		);
	}
}
