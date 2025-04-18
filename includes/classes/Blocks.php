<?php
/**
 * Jobber Blocks.
 *
 * @package Jobber
 */

namespace Jobber;

use Jobber\Module;

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
	 * Register needed hooks.
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
			// If we encounter an error return nothing
			// instead of returning an error message since the
			// end user can't do anything about the error.
			// see https://github.com/10up/jobber-wp/issues/10#issue-2993579619.
			return '';
		}

		$embed_script = '';

		if (
			'request' === $form_type &&
			isset( $response['data']['requestSettings']['requestEmbedScript'] )
		) {
			$embed_script = $response['data']['requestSettings']['requestEmbedScript'];
		} elseif (
			'booking' === $form_type &&
			isset( $response['data']['onlineBookingConfiguration']['bookingEmbedScript'] )
		) {
			$embed_script = $response['data']['onlineBookingConfiguration']['bookingEmbedScript'];
		}

		if ( empty( $embed_script ) ) {
			// If no iframe embed script is returned, return nothing
			// instead of returning an error message since the
			// end user can't do anything about the error.
			// see https://github.com/10up/jobber-wp/issues/10#issue-2993579619.
			return '';
		}

		return sprintf(
			'<div class="jobber-embed-block">HEYYY!%s</div>',
			wp_kses(
				$embed_script,
				[
					'div'    => [
						'id'    => true,
						'class' => true,
					],
					'script' => [
						'src'       => true,
						'vendor_id' => true,
						'form_url'  => true,
						'clienthub_id' => true,
					],
					'link'   => [
						'rel'   => true,
						'href'  => true,
						'media' => true,
					],
				]
			)
		);
	}
}
