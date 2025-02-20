<?php
/**
 * Jobber Plugin Settings
 *
 * @package Jobber
 */

namespace Jobber\Admin;

use TenupFramework\Module;
use Jobber\Auth;

/**
 * Base class for Jobber Configuration settings
 */
class Settings {

	use Module;

	/**
	 * Settings Key.
	 * All Jobber settings are stored under this meta key.
	 *
	 * @var string
	 */
	const SETTINGS_KEY = 'jobber_settings';

	/**
	 * Only register the menu if the current user has the correct capability.
	 *
	 * @return bool
	 */
	public function can_register() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Registers the settings page.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', [ $this, 'register_menu' ] );
	}

	/**
	 * Create the Settings Page.
	 */
	public function register_menu() {
		add_options_page(
			__( 'Jobber Settings', 'jobber-plugin' ),
			__( 'Jobber Settings', 'jobber-plugin' ),
			'manage_options',
			'jobber_settings',
			[ $this, 'render_page' ]
		);
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public function render_page() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Jobber Settings', 'jobber-plugin' ); ?></h2>
			<div style="margin-top: 2rem;">
				<?php if ( ! Auth::is_authorized() ) : ?>
					<a href="<?php echo esc_url( Auth::middleware_url( 'authorize' ) ); ?>" class="button button-primary">
						<?php esc_html_e( 'Connect to Jobber', 'jobber-plugin' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
