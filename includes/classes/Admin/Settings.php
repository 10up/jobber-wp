<?php
/**
 * Jobber Plugin Settings
 *
 * @package Jobber
 */

namespace Jobber\Admin;

use Jobber\Module;
use Jobber\Auth;
use Jobber\REST\Token;

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
			__( 'Jobber Forms', 'jobber-wp' ),
			__( 'Jobber Forms', 'jobber-wp' ),
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
		$token    = $this->set_auth_token();
		$url_args = [
			'clientUrl' => get_site_url(),
			'returnUrl' => self::settings_url(),
		];
		if ( ! empty( $token ) ) {
			$url_args[ Token::$key ] = $token;
		}

		$auth_url = add_query_arg( $url_args, Auth::$url );
		?>
		<div class="wrap">
			<div class="jobber-logo" style="margin: 2rem 0;">
				<img src="<?php echo esc_url( JOBBER_PLUGIN_URL . 'assets/images/jobber-logo.png' ); ?>" alt="<?php esc_attr_e( 'Jobber', 'jobber-wp' ); ?>" style="max-width: 200px" />
			</div>
			<h2><?php esc_html_e( 'Settings', 'jobber-wp' ); ?></h2>
			<p><?php esc_html_e( 'Connect to your Jobber account to access your forms.', 'jobber-wp' ); ?></p>
			<div style="margin-top: 2rem;">
				<?php if ( ! Auth::is_authorized() ) : ?>
					<a href="<?php echo esc_url( $auth_url ); ?>" class="button button-primary">
						<?php esc_html_e( 'Connect to Jobber', 'jobber-wp' ); ?>
					</a>
				<?php else : ?>
					<p><?php esc_html_e( 'You are connected to Jobber.', 'jobber-wp' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Set the auth token for WP and the Middleware.
	 *
	 * @return string|bool
	 */
	protected function set_auth_token() {
		$token = Token::get_token();
		if ( ! empty( $token ) ) {
			return $token;
		}

		// Delete the option if it exists.
		delete_option( self::SETTINGS_KEY );

		$tokens = new Token();
		$token  = $tokens->generate();
		$tokens->save( $token );

		return $token;
	}

	/**
	 * Get the settings URL.
	 *
	 * @return string
	 */
	public static function settings_url() {
		return admin_url( 'options-general.php?page=' . self::SETTINGS_KEY );
	}

	/**
	 * Update the settings.
	 *
	 * @param array $settings Settings to update.
	 * @return bool
	 */
	public static function update_settings( $settings = [] ) {
		return update_option( self::SETTINGS_KEY, wp_json_encode( $settings ) );
	}

	/**
	 * Get the settings.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$settings = get_option( self::SETTINGS_KEY, '' );
		return json_decode( $settings, true );
	}
}
