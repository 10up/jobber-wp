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
	 *
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
	public function can_register(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Register needed hooks.
	 */
	public function register() {
		add_action( 'admin_menu', [ $this, 'register_menu' ] );
	}

	/**
	 * Register the settings page.
	 */
	public function register_menu() {
		add_options_page(
			__( 'Jobber Forms', 'jobber-wp' ),
			__( 'Jobber Forms', 'jobber-wp' ),
			'manage_options',
			self::SETTINGS_KEY,
			[ $this, 'render_page' ]
		);
	}

	/**
	 * Render the settings page.
	 */
	public function render_page() {
		$url_args = [
			'clientUrl' => site_url( Token::get_endpoint( 'generate' ) ),
			'returnUrl' => self::settings_url(),
			'nonce'     => $this->set_auth_nonce(),
		];

		$auth_url = add_query_arg( $url_args, Auth::$url );
		?>

		<div class="wrap">
			<div class="jobber-settings__logo" style="margin: 2rem 0 1rem;">
				<img src="<?php echo esc_url( JOBBER_PLUGIN_URL . 'assets/images/jobber-logo.png' ); ?>" alt="<?php esc_attr_e( 'Jobber logo', 'jobber-wp' ); ?>" style="max-width: 220px; margin-left: -10px;" />
			</div>

			<h2 class="screen-reader-text"><?php esc_html_e( 'Settings', 'jobber-wp' ); ?></h2>

			<div class="jobber-settings__container" style="max-width: 600px; font-size: 14px; line-height: 1.5;">
				<?php if ( ! Auth::is_authorized() ) : ?>
					<p style="font-size: 14px; line-height: 1.7;">
						<?php esc_html_e( 'The Jobber Forms plugin allows you to easily embed your Booking and Request forms using a new Jobber Forms block. To get started, follow the steps below:', 'jobber-wp' ); ?>
					</p>
					<ul style="list-style: decimal;">
						<li style="margin-left: 2rem;"><?php esc_html_e( 'Click the Connect button below and log in with your Jobber account', 'jobber-wp' ); ?></li>
						<li style="margin-left: 2rem;"><?php esc_html_e( 'Edit the page where you want to embed your form and insert the Jobber block.', 'jobber-wp' ); ?></li>
						<li style="margin-left: 2rem;"><?php esc_html_e( 'Within the block settings, choose the form type, either Request or Booking.', 'jobber-wp' ); ?></li>
					</ul>
					<p style="font-size: 14px; line-height: 1.7;">
						<?php
						printf(
							/* translators: %1$s: opening link tag, %2$s: closing link tag */
							esc_html__( 'If you don\'t have a Jobber account yet, follow %1$sthese%2$s instructions to create that first.', 'jobber-wp' ),
							'<a href="https://help.getjobber.com/hc/en-us/articles/360042653674-First-Steps-Basic-Account-Set-Up" target="_blank" rel="noreferrer noopener">',
							'</a>'
						);
						?>
					</p>
				<?php endif; ?>
			</div>

			<div class="jobber-settings__connection" style="margin-top: 2rem; max-width: 600px; font-size: 14px; line-height: 1.5;">
				<?php if ( ! Auth::is_authorized() ) : ?>
					<a href="<?php echo esc_url( $auth_url ); ?>" class="is-primary button button-primary">
						<?php esc_html_e( 'Connect to Jobber', 'jobber-wp' ); ?>
					</a>
				<?php else : ?>
					<p style="font-size: 14px;">
						<span class="dashicons dashicons-yes-alt" style="color: green;"></span>
						<?php esc_html_e( 'You\'re connected!', 'jobber-wp' ); ?>
					</p>
					<p style="font-size: 14px; font-weight: 600;"><?php esc_html_e( 'Next steps:', 'jobber-wp' ); ?></p>
					<ul style="list-style: decimal;">
						<li style="margin-left: 2rem;"><?php esc_html_e( 'Edit the page where you want to embed your form and insert the Jobber block.', 'jobber-wp' ); ?></li>
						<li style="margin-left: 2rem;"><?php esc_html_e( 'Within the block settings, choose the form type, either Request or Booking.', 'jobber-wp' ); ?></li>
					</ul>

					<p style="font-size: 14px; line-height: 1.7;">
						<?php esc_html_e( 'If you no longer need to have a form embedded, you can disconnect your account by clicking the button below. Note that any existing forms you have embedded will no longer show.', 'jobber-wp' ); ?>
					</p>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<?php wp_nonce_field( 'jobber_disconnect' ); ?>
						<input type="hidden" name="action" value="jobber_disconnect">
						<button type="submit" class="is-secondary is-destructive button" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to disconnect from Jobber? Any forms you have embedded will no longer work.', 'jobber-wp' ); ?>');" style="margin-top: 1rem;">
							<?php esc_html_e( 'Disconnect', 'jobber-wp' ); ?>
						</button>
					</form>
				<?php endif; ?>
			</div>
		</div>

		<?php
	}

	/**
	 * Create and store the auth nonce.
	 *
	 * @return string
	 */
	protected function set_auth_nonce(): string {
		$nonce = wp_create_nonce( 'jobber' );
		self::update_settings( [ 'nonce' => $nonce ] );

		return $nonce;
	}

	/**
	 * Get the settings URL.
	 *
	 * @return string
	 */
	public static function settings_url(): string {
		return admin_url( 'options-general.php?page=' . self::SETTINGS_KEY );
	}

	/**
	 * Update the settings.
	 *
	 * @param array $settings Settings to update.
	 * @return bool
	 */
	public static function update_settings( array $settings = [] ): bool {
		$current  = self::get_settings();
		$settings = wp_parse_args( $settings, $current );
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

	/**
	 * Delete the settings.
	 */
	public static function delete_settings() {
		delete_option( self::SETTINGS_KEY );
	}
}
