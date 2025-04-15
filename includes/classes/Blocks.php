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
	 * @return bool
	 */
	public function can_register(): bool {
		return true;
	}

	/**
	 * Hook the module into WP.
	 */
	public function register() {
		add_action( 'init', [ $this, 'register_block_types' ] );
	}

	/**
	 * Register the block types.
	 */
	public function register_block_types() {
		register_block_type(
			JOBBER_PLUGIN_PATH . 'blocks/forms',
			[
				'render_callback' => [ $this, 'render_block' ],
			]
		);
	}

	/**
	 * Render the block.
	 *
	 * @param array $attributes The block attributes.
	 * @return string
	 */
	public function render_block( array $attributes ): string {
		$form_type = ! empty( $attributes['formType'] ) ? sanitize_text_field( $attributes['formType'] ) : 'request';

		$jobber   = new \Jobber\Jobber();
		$response = $jobber->get_form( $form_type );

		if ( is_wp_error( $response ) ) {
			// If we encounter an error when rendering on the front-end,
			// do not render anything instead of showing an error message,
			// as the error message just confuses the actual user.
			// see https://github.com/10up/jobber-wp/issues/10#issue-2993579619.
			return '';
		}

		$iframe_url = '';

		if (
			'request' === $form_type &&
			isset( $response['data']['requestSettings']['requestUrl'] )
		) {
			$iframe_url = $response['data']['requestSettings']['requestUrl'];
		} elseif (
			'booking' === $form_type &&
			isset( $response['data']['onlineBookingConfiguration']['bookingUrl'] )
		) {
			$iframe_url = $response['data']['onlineBookingConfiguration']['bookingUrl'] . '/embedded';
		}

		if ( empty( $iframe_url ) ) {
			// If no iframe URL is returned on the front-end,
			// do not render anything instead of showing an error message,
			// as the error message just confuses the actual user.
			// see https://github.com/10up/jobber-wp/issues/10#issue-2993579619.
			return '';
		}

		return sprintf(
			'<div class="jobber-embed-block"><iframe src="%s" width="100%%" height="600" style="border:none;" title="%s"></iframe></div>',
			esc_url( $iframe_url ),
			esc_attr__( 'Jobber Form', 'jobber-wp' )
		);
	}
}
