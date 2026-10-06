<?php
/**
 * Jobber Blocks.
 *
 * @package Jobber
 */

namespace Jobber;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

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
		$form_id = ! empty( $attributes['formId'] ) ? sanitize_text_field( $attributes['formId'] ) : '';

		// A saved form id means this block uses the account's form list.
		if ( '' !== $form_id ) {
			return $this->render_selected_form( $form_id );
		}

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

		return $this->wrap_embed_script( $embed_script );
	}

	/**
	 * Render a specific form chosen from the account's form list.
	 *
	 * Prefers the embed script when the API provides one, because it carries Jobber's own
	 * styling and height handling. Falls back to an iframe built from the form URL, which
	 * is all the forms collection is documented to return.
	 *
	 * @param string $form_id The saved form identifier.
	 * @return string
	 */
	protected function render_selected_form( string $form_id ): string {
		$form = ( new \Jobber\Jobber() )->get_form_by_id( $form_id );

		// Return nothing on failure, since the visitor cannot act on the error.
		// See https://github.com/10up/jobber-wp/issues/10#issue-2993579619.
		if ( is_wp_error( $form ) ) {
			return '';
		}

		if ( ! empty( $form['embedScript'] ) ) {
			return $this->wrap_embed_script( $form['embedScript'] );
		}

		if ( empty( $form['url'] ) ) {
			return '';
		}

		return sprintf(
			'<div class="jobber-embed-block"><iframe src="%1$s" title="%2$s" style="width:100%%;height:%3$dpx;border:0;" loading="lazy"></iframe></div>',
			esc_url( $form['url'] ),
			esc_attr( $form['name'] ),
			(int) self::get_form_height( $form['bookingType'] )
		);
	}

	/**
	 * Get a sensible iframe height for a form.
	 *
	 * Jobber's BookingType enum is NONE, JOB or ASSESSMENT. NONE creates a request only,
	 * so it renders the long work request form. JOB and ASSESSMENT both create a booking
	 * and show the shorter scheduler, which is what the API's own `bookingEnabled` filter
	 * means by "bookable". An unrecognised value gets the taller height rather than
	 * risking a cut off form.
	 *
	 * @param string $booking_type The form's bookingType value.
	 * @return int Height in pixels.
	 */
	public static function get_form_height( string $booking_type ): int {
		$bookable = array( 'JOB', 'ASSESSMENT' );
		$height   = in_array( strtoupper( $booking_type ), $bookable, true ) ? 400 : 1630;

		/**
		 * Filters the iframe height used when rendering a Jobber form.
		 *
		 * @since x.x.x
		 * @hook jobber_form_height
		 *
		 * @param int    $height       Height in pixels.
		 * @param string $booking_type The form's bookingType value.
		 *
		 * @return int Filtered height.
		 */
		return (int) apply_filters( 'jobber_form_height', $height, $booking_type );
	}

	/**
	 * Wrap an embed script in the block container, allowing only Jobber's own markup.
	 *
	 * @param string $embed_script Raw embed markup from the API.
	 * @return string
	 */
	protected function wrap_embed_script( string $embed_script ): string {
		$this->enqueue_resize_script();

		return sprintf(
			'<div class="jobber-embed-block">%s</div>',
			wp_kses(
				$embed_script,
				[
					'div'    => [
						'id'    => true,
						'class' => true,
					],
					'script' => [
						'src'          => true,
						'vendor_id'    => true,
						'form_url'     => true,
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

	/**
	 * Resize each embedded form to its own height.
	 *
	 * Jobber's embed script resizes the first `iframe.jobber-work-request` on the page
	 * whenever any form reports its height, so with several forms on one page only the
	 * first one grows and the rest stay cut off. This matches each height message to the
	 * iframe that sent it instead, and stops Jobber's handler from resizing the wrong one.
	 * Other messages, such as closing the dialog, still reach Jobber's handler.
	 */
	protected function enqueue_resize_script() {
		if ( wp_script_is( 'jobber-embed-resize', 'enqueued' ) ) {
			return;
		}

		wp_register_script( 'jobber-embed-resize', false, [], JOBBER_PLUGIN_VERSION, true );
		wp_add_inline_script(
			'jobber-embed-resize',
			'( function () {
				var heights = new Map();

				window.addEventListener( "message", function ( event ) {
					var data = event[ event.message ? "message" : "data" ];

					if ( "string" !== typeof data || ! /^\d+(\.\d+)?px$/.test( data ) ) {
						return;
					}

					var frames = document.querySelectorAll( "iframe.jobber-work-request" );
					var source = Array.prototype.find.call( frames, function ( frame ) {
						return frame.contentWindow === event.source;
					} );

					if ( ! source ) {
						return;
					}

					heights.set( source, data );

					// Reapply every known height, in case Jobber\'s handler already ran.
					heights.forEach( function ( height, frame ) {
						frame.style.height = height;
						frame.parentElement.style.height = height;
					} );

					event.stopImmediatePropagation();
				} );
			} )();'
		);
		wp_enqueue_script( 'jobber-embed-resize' );
	}
}
