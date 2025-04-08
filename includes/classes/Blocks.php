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
		$form_type = ! empty( $attributes['formType'] ) ? sanitize_text_field( $attributes['formType'] ) : 'request';
	
		$jobber = new \Jobber\Jobber();
		$response = $jobber->get_form( $form_type );
	
		if ( is_wp_error( $response ) ) {
			return sprintf(
				'<p class="jobber-error">%s</p>',
				esc_html( $response->get_error_message() )
			);
		}
	
		$iframe_url = '';
		if ( 'request' === $form_type && isset( $response['data']['requestSettings']['requestUrl'] ) ) {
			$iframe_url = $response['data']['requestSettings']['requestUrl'];
		} elseif ( 'booking' === $form_type && isset( $response['data']['onlineBookingConfiguration']['bookingUrl'] ) ) {
			$iframe_url = $response['data']['onlineBookingConfiguration']['bookingUrl'];
		}		
	
		if ( empty( $iframe_url ) ) {
			return '<p class="jobber-error">Form iframe URL not found.</p>';
		}
	
		return sprintf(
			'<div class="jobber-embed-block"><iframe src="%s" width="100%%" height="600" style="border:none;" title="Jobber Form"></iframe></div>',
			esc_url( $iframe_url )
		);
	}
}
